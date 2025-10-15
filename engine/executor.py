import json
import subprocess
import threading
import argparse
import os
import signal
import sys

# ────────────────────────────────
# Argument Parser
# ────────────────────────────────
parser = argparse.ArgumentParser(description="Drawflow Workflow Executor")
parser.add_argument("-w", "--workflow", required=True, help="Path ke file drawflow (JSON)")
parser.add_argument("-t", "--target", required=True, help="Nama target (opsional)")
parser.add_argument("-o", "--output", required=True, help="Path untuk output (opsional)")
parser.add_argument("-d", "--debug", required=False, help="debugging mode")
args = parser.parse_args()

# ────────────────────────────────
# Load Drawflow Data
# ────────────────────────────────
with open(args.workflow) as f:
    data = json.load(f)["drawflow"]["Home"]["data"]

# ────────────────────────────────
# Ensure Output Directory & Save Initial Log
# ────────────────────────────────
os.makedirs(args.output, exist_ok=True)
with open(os.path.join(args.output, 'process_log.json'), 'w') as fw:
    json.dump(data, fw, indent=4)

# ────────────────────────────────
# Thread Lock for Log Write
# ────────────────────────────────
log_lock = threading.Lock()

def create_process_log(task_id, data_log):
    with log_lock:
        task = data[task_id]['data']
        task['command'] = data_log['command']
        task['status'] = data_log['status']
        task['stdout'] = data_log['stdout']
        task['error'] = data_log['error']
        task['pid'] = data_log['pid']
        with open(os.path.join(args.output, 'process_log.json'), 'w') as fw:
            json.dump(data, fw, indent=4)

def set_all_nodes_status(status_value):
    # set all node's status (running or pending) to status_value, and dump log
    with log_lock:
        for node_id, node in data.items():
            st = node['data'].get('status', '')
            if st == "pending" or st == "running" or st == "":
                node['data']['status'] = status_value
        with open(os.path.join(args.output, 'process_log.json'), 'w') as fw:
            json.dump(data, fw, indent=4)

# ────────────────────────────────
# Build Dependency Graph
# ────────────────────────────────
graph = {}
parents = {}

for node_id, node in data.items():
    graph[node_id] = [c["node"] for c in node["outputs"]["output_1"]["connections"]]
    for c in node["outputs"]["output_1"]["connections"]:
        parents.setdefault(c["node"], []).append(node_id)

# ────────────────────────────────
# Task Status Dictionary
# ────────────────────────────────
status = {node_id: "pending" for node_id in data}
status_lock = threading.Lock()

# ────────────────────────────────
# Process Tracking for SIGTERM/INT
# ────────────────────────────────
running_procs = []
running_procs_lock = threading.Lock()

def force_terminate_all_processes():
    with running_procs_lock:
        for proc in running_procs[:]:
            try:
                if proc.poll() is None:
                    try:
                        # Kill the whole group
                        os.killpg(os.getpgid(proc.pid), signal.SIGKILL)
                    except Exception:
                        proc.kill()
                    try:
                        proc.wait(timeout=2)
                    except Exception:
                        pass
            except Exception:
                pass
        running_procs.clear()

def signal_handler(signum, frame):
    # print(f"\n[!] Received signal {signum}. Terminating subprocesses...", file=sys.stderr)
    # Set running and pending nodes to stopped before exiting
    set_all_nodes_status("stopped")
    force_terminate_all_processes()
    sys.exit(1)

signal.signal(signal.SIGINT, signal_handler)
signal.signal(signal.SIGTERM, signal_handler)
if hasattr(signal, 'SIGHUP'):
    signal.signal(signal.SIGHUP, signal_handler)

# ────────────────────────────────
# Task Runner
# ────────────────────────────────
def run_task(node_id):
    data_log = {"command": "", "status": "running", "stdout": "", "error": "", "pid": ""}
    node = data[node_id]
    cmd = command_replacement(node["data"].get("command", ""), node)

    # set status running secara atomik
    with status_lock:
        status[node_id] = "running"
        data[node_id]['data']['status'] = "running"

    proc = subprocess.Popen(
        cmd,
        shell=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True,
        preexec_fn=os.setsid  # start new session/process group (UNIX)
    )
    with running_procs_lock:
        running_procs.append(proc)

    # Log running state
    data_log['command'] = cmd
    data_log['status'] = "running"
    data_log['pid'] = proc.pid
    create_process_log(node_id, data_log)

    stdout, stderr = proc.communicate()

    with running_procs_lock:
        if proc in running_procs:
            running_procs.remove(proc)

    # update status akhir secara atomik
    with status_lock:
        if stderr:
            status[node_id] = "failed"
            data_log['status'] = "failed"
            data[node_id]['data']['status'] = "failed"
            data_log['error'] = stderr
        else:
            status[node_id] = "finished"
            data_log['status'] = "finished"
            data[node_id]['data']['status'] = "finished"
            data_log['error'] = None

    if args.debug:
        data_log['stdout'] = stdout
    else:
        data_log['stdout'] = None

    data_log['pid'] = None
    create_process_log(node_id, data_log)

    # trigger next (diluar lock untuk menghindari deadlock)
    trigger_next(node_id)


# ────────────────────────────────
# Trigger Child Tasks
# ────────────────────────────────
def trigger_next(finished_id):
    for child in graph.get(finished_id, []):
        wait_flag = data[child]["data"].get("wait", False)
        all_parents = parents.get(child, [])

        if wait_flag:
            # tunggu semua parent spesifik dari child selesai
            with status_lock:
                parents_done = all(status.get(p) == "finished" for p in all_parents)
                if parents_done and status.get(child) == "pending":
                    # mark running immediately untuk mencegah double-start
                    status[child] = "running"
                    data[child]['data']['status'] = "running"
                    threading.Thread(target=run_task, args=(child,)).start()
        else:
            # normal: kalau masih pending, start (parent yang memicu sudah selesai)
            with status_lock:
                if status.get(child) == "pending":
                    status[child] = "running"
                    data[child]['data']['status'] = "running"
                    threading.Thread(target=run_task, args=(child,)).start()


# ────────────────────────────────
# Command Replacement
# ────────────────────────────────
def command_replacement(command, node):
    command = command.replace('{{name}}', node["data"]["name"])
    command = command.replace('{{dec}}', node["data"]["description"])
    command = command.replace('{{target}}', args.target)
    command = command.replace('{{output}}', args.output)
    return command

# ────────────────────────────────
# Run All Root Tasks In Parallel
# ────────────────────────────────
root_tasks = [nid for nid in data if not parents.get(nid)]
for t in root_tasks:
    threading.Thread(target=run_task, args=(t,)).start()
