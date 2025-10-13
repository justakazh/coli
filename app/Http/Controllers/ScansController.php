<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scopes;
use App\Models\Workflows;
use App\Models\Scans;

class ScansController extends Controller
{
    public function index(Request $request)
    {
        // Build query with combinable search parameters
        $query = Scans::query();

        // Search by target (actually matches related 'scopes.target')
        if ($request->filled('target')) {
            $query->whereHas('scope', function($q) use ($request) {
                $q->where('target', 'like', '%' . $request->target . '%');
            });
        }

        // Filter by workflow_id (from form: 'workflow')
        if ($request->filled('workflow') || $request->filled('workflow_id')) {
            $workflowId = $request->workflow ?? $request->workflow_id;
            $query->where('workflow_id', $workflowId);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data['scans'] = $query->get();
        $data['workflows'] = Workflows::all();

        return view('scans.index', compact('data'));
    }

    public function create(Request $request){
        //validate request
        $request->validate([
            'target' => 'required',
            'workflow_id' => 'required|integer|exists:workflows,id',
        ]);

        //create scope
        $scope = Scopes::create([
            'target' => $request->target,
        ]);

        //create directory output
        $output_dir = env('WORKDIR') . '/output/' . $scope->id . '/' . $request->workflow_id;
        if (!file_exists($output_dir)) {
            mkdir($output_dir, 0777, true);
        }

        //create scan
        $scan = Scans::create([
            'scope_id' => $scope->id,
            'workflow_id' => $request->workflow_id,
            'status' => 'pending',
            'output' => $output_dir
        ]);

        //run workflow
        $workflow = $scan->workflow->task_data;

        return redirect()->route('scans')->with('success', 'Scan created successfully');

    }

    public function action($id, Request $request)
    {
        //validate request
        $request->validate([
            'action' => 'required|in:start,stop',
        ]);

        $scan = Scans::find($id);
        $action = $request->action;

        //if action is start
        if($action == 'start'){
            $scan->started_at = now();
            $scan->finished_at = null;
            $scan->status = 'running';
            $scan->save();

            //Start Coli Executor
            $command = 'export PATH=$PATH:'.env('USER_PATH')." && export HOME=".env('USER_HOME')." &&  setsid  php ".base_path('artisan')." app:executor ".$scan->id. " > /dev/null 2>&1 & echo $!";
            $exec = shell_exec($command);
            $pid = intval(trim($exec));
            $scan->pid = $pid;
            $scan->save();
            
            return redirect()->route('scans')->with('success', 'Scan started successfully');
        }

        if ($action == 'stop') {
            try {
                $pgid = intval($scan->pid);
                exec("kill -TERM -$pgid 2>/dev/null");
                sleep(1);
                exec("pgrep -g $pgid | xargs kill -9 2>/dev/null");
        
                $scan->finished_at = now();
                $scan->status = 'stopped';
                $scan->pid = null;
                $scan->log = null;
                $scan->save();
            } catch (\Throwable $th) {
                $scan->status = 'stopped';
                $scan->log = $th->getMessage();
                $scan->save();
            }
        }

        $scan->save();

        return redirect()->route('scans')->with('success', 'Scan action performed successfully');
    }


    public function destroy($id)
    {
        $scan = Scans::find($id);
        //delete scope
        $scope = Scopes::find($scan->scope_id);

        // force delete output folder recursively
        $output = $scan->output;
        if (file_exists($output)) {
            // Recursively delete all files and folders
            $it = new \RecursiveDirectoryIterator($output, \FilesystemIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($output);
        }

        $scan->delete();
        $scope->delete();

        return redirect()->route('scans')->with('success', 'Scan deleted successfully');
    }


}
