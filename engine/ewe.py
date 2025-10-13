#!/usr/bin/env python3
import argparse
import json
import os
import threading
import subprocess
import signal
import sys

class EWE:
    def __init__(self, args):
        self.args = args 
        self.thread_lock = threading.Lock()
        self.log_handler = None
        self.running_handler = []
        self.task_map = {}  # task id -> reference, for easier log update

        self._register_signal_handlers()
        self.preparation()
        self.execution()

    def _register_signal_handlers(self):
        def handler(signum, frame):
            print(f"\n[!] Received signal {signum}. Forcefully terminating running tasks...", file=sys.stderr)
            self.force_terminate_all_processes()
            sys.exit(1)
        signal.signal(signal.SIGINT, handler)
        signal.signal(signal.SIGTERM, handler)
        if hasattr(signal, 'SIGHUP'):
            signal.signal(signal.SIGHUP, handler)

    def force_terminate_all_processes(self):
        """
        Forcefully (SIGKILL) kill all running child processes.
        """
        for proc in self.running_handler[:]:
            try:
                if proc.poll() is None:
#if Linux: try killing the whole process group:
                    try:
                        os.killpg(os.getpgid(proc.pid), signal.SIGKILL)
                    except Exception:
                        proc.kill()
                # Wait for process to exit but don't wait forever
                try:
                    proc.wait(timeout=2)
                except Exception:
                    pass
            except Exception:
                pass
        self.running_handler.clear()

    # ---------------------------------------- #
    #               PREPARATION                #
    # ---------------------------------------- #

    def preparation(self):
        os.makedirs(self.args.output, exist_ok=True)
        process_log = self.generate_json_log()
        with open(os.path.join(self.args.output, 'process_log.json'), 'w') as fw:
            json.dump(process_log, fw, indent=4)
        self.log_handler = process_log

        # flat map for updating tasks by id with thread safety
        self.task_map = {}
        self._task_map_index(self.log_handler.get("tasks", []))

    def _task_map_index(self, tasks):
        for task in tasks:
            self.task_map[task["id"]] = task
            if "tasks" in task and isinstance(task["tasks"], list):
                self._task_map_index(task["tasks"])

    def generate_json_log(self, node=None, prefix=""):
        if node is None:
            with open(self.args.workflow, 'r') as file:
                node = json.load(file)

        if 'tasks' in node and isinstance(node['tasks'], list):
            for i, task in enumerate(node['tasks']):
                if prefix == '':
                    task_id = f"{i+1}"
                else:
                    task_id = f"{prefix}.{i+1}"

                task.update({
                    'id': task_id,
                    'status': '',
                    'stdout': '',
                    'error': '',
                    'pid': '',
                    'output': os.path.join(self.args.output, task.get('result', f"result_{task_id}"))
                })

                task['command'] = self.set_placeholder(task, node)
                task = self.generate_json_log(task, prefix=task_id)
                node['tasks'][i] = task

        node['target'] = self.args.target
        node['name'] = node.get('name', '')
        node['description'] = node.get('description', '')
        node['output'] = self.args.output
        node['output_path'] = os.path.join(os.getcwd(), self.args.output)
        return node

    def set_placeholder(self, task, parent_task=None):
        command = task['command']
        command = command.replace('{target}', self.args.target)
        command = command.replace('{name}', task['name'])
        command = command.replace('{result}', self._check_result_path(task.get('result', f"{task['name']}.txt")))
        command = command.replace('{output_path}', self.args.output)
        if parent_task:
            command = command.replace('{parent_name}', parent_task['name'])
            command = command.replace('{parent_result}', os.path.join(self.args.output, parent_task.get('result', f"{parent_task['name']}.txt")))
        else:
            command = command.replace('{parent_name}', '')
            command = command.replace('{parent_result}', '')
        return command

    def _check_result_path(self, result):
        if '/' in result:
            dir_path = os.path.dirname(result)
            if dir_path:
                os.makedirs(os.path.join(self.args.output, dir_path), exist_ok=True)
            return os.path.join(self.args.output, result)
        else:
            return os.path.join(self.args.output, result)

    # ---------------------------------------- #
    #               Execution                  #
    # ---------------------------------------- #

    def execution(self, tasks=None, parent_status=None):
        if tasks is None:
            tasks = self.log_handler['tasks']
        threads = []
        for task in tasks:
            thread = threading.Thread(target=self._execute_task, args=(task, parent_status))
            thread.start()
            threads.append(thread)
        for thread in threads:
            thread.join()

    def _execute_task(self, task, parent_status=None):
        if parent_status in ['skipped', 'stopped', 'error']:
            with self.thread_lock:
                task['status'] = 'stopped'
                self._update_json_log(task)
            if 'tasks' in task and isinstance(task['tasks'], list):
                self.execution(task['tasks'], task['status'])
            return

        try:
            with self.thread_lock:
                task['status'] = 'running'
                self._update_json_log(task)
            command = task['command']
            # Set process group for each task so we can kill all related processes if needed
            proc = subprocess.Popen(
                command,
                shell=True,
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True,
                preexec_fn=os.setsid  # start new session/process group (UNIX)
            )
            task['pid'] = proc.pid
            with self.thread_lock:
                self.running_handler.append(proc)
                self._update_json_log(task)
            # update log after got PID and running
            stdout, stderr = proc.communicate()
            # Update right after process finishes: error & stdout can be very large/racy
            with self.thread_lock:
                task['error'] = stderr
                task['status'] = 'done' if proc.returncode == 0 else 'error'
                if self.args.stdout_json:
                    lines = stdout.splitlines()
                    limited = lines[:100]
                    task['stdout'] = "\n".join(limited)
                    if len(lines) > 100:
                        task['stdout'] += '\n[ -- SNIPPED -- ]\n'
                self._update_json_log(task)
        except Exception as e:
            with self.thread_lock:
                task['status'] = 'error'
                task['error'] = str(e)
                self._update_json_log(task)
            return
        finally:
            with self.thread_lock:
                if 'proc' in locals() and proc in self.running_handler:
                    self.running_handler.remove(proc)
                self._update_json_log(task)  # Always update the latest log for this task

        if 'tasks' in task and isinstance(task['tasks'], list):
            self.execution(task['tasks'], task['status'])

    def _update_json_log(self, task):
        # Selalu update log dan hindari race condition (thread_lock sudah aktif di pemanggil)
        if hasattr(self, "task_map") and task['id'] in self.task_map:
            self.task_map[task['id']].update(task)
        json_file = os.path.join(self.args.output, 'process_log.json')
        # Always write fresh log from root
        with open(json_file, 'w') as file:
            json.dump(self.log_handler, file, indent=4)

def main():
    parser = argparse.ArgumentParser(description="EWE - Execution Workflow Engine")
    parser.add_argument("-t", "--target", type=str, required=True, help="Target value")
    parser.add_argument("-w", "--workflow", type=str, help="Workflow file")
    parser.add_argument("-o", "--output", type=str, required=True, help="Output folder")
    parser.add_argument("--stdout-json", action="store_true", help="Write limited stdout to json log if enabled (default: False)")

    args = parser.parse_args()
    runner = EWE(args)

if __name__ == "__main__":
    main()
