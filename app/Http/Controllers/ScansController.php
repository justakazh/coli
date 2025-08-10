<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workflows;
use App\Models\Scopes;
use App\Models\Scans;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class ScansController extends Controller
{
    /**
     * Display a listing of scans.
     */
    public function index()
    {
        try {
            $scans = Scans::orderBy('created_at', 'desc')->paginate(15);
            $workflows = Workflows::all();
            return view('scans.index', compact('scans', 'workflows'));
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while loading scans: ' . $e->getMessage());
        }
    }

    /**
     * Search for scans based on filters.
     */
    public function search(Request $request)
    {
        try {
            $target = $request->input('target');
            $workflow = $request->input('workflow');
            $status = $request->input('status');

            $scans = Scans::where('target', 'like', '%' . $target . '%')
                ->where('workflow_id', 'like', '%' . $workflow . '%')
                ->where('status', 'like', '%' . $status . '%')
                ->paginate(15)
                ->withQueryString();

            $workflows = Workflows::all();
            return view('scans.index', compact('scans', 'workflows'));
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred during search: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new scan for a given scope.
     */
    public function create($id)
    {
        try {
            $scope = Scopes::find($id);
            if (!$scope) {
                return redirect()->route('scans.index')->with('error', 'Scope not found.');
            }

            $outputDir = $scope->output_path;
            $files = [];

            if (file_exists($outputDir) && is_dir($outputDir)) {
                try {
                    $files = File::allFiles($outputDir);
                    $files = array_map(fn($file) => $file->getRealPath(), $files);
                } catch (Exception $e) {
                    return redirect()->route('scans.index')->with('error', 'Failed to read output directory: ' . $e->getMessage());
                }
            }

            $workflows = Workflows::all();
            return view('scans.create', compact('scope', 'workflows', 'files'));
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while preparing scan creation: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created scan in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'target' => 'required|string',
                'workflow_id' => 'required|exists:workflows,id',
                'scope_id' => 'required|exists:scopes,id',
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . $e->getMessage());
        }

        try {
            $scope = Scopes::find($request->scope_id);
            if (!$scope) {
                return redirect()->route('scans.index')->with('error', 'Scope not found.');
            }

            $type = (strpos($request->target, '/scans/outputs/') !== false) ? 'output' : $scope->type;
            $target = trim($request->target);

            if (empty($target)) {
                return redirect()->back()->withInput()->with('error', 'Target cannot be empty.');
            }

            $workflow = Workflows::find($request->workflow_id);
            if (!$workflow) {
                return redirect()->back()->withInput()->with('error', 'Workflow not found.');
            }

            $directory = rtrim($scope->output_path, '/') . '/' . $workflow->slug;
            if (!file_exists($directory)) {
                if (!@mkdir($directory, 0777, true)) {
                    return redirect()->back()->withInput()->with('error', 'Failed to create scan directory.');
                }
            }

            $scan = new Scans();
            $scan->target = $target;
            $scan->process = "";
            $scan->workflow_id = $workflow->id;
            $scan->scope_id = $scope->id;
            $scan->type = $type;
            $scan->status = 'pending';
            $scan->output_path = $directory;
            $scan->save();

            return redirect()->route('scans.index')->with('success', 'Scan created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the scan: ' . $e->getMessage());
        }
    }

    /**
     * Run a scan by starting its process.
     */
    public function run($id)
    {
        try {
            $scan = Scans::find($id);
            if (!$scan) {
                return redirect()->route('scans.index')->with('error', 'Scan not found.');
            }

            $artisan = base_path('artisan');
            $cmd = "setsid php $artisan run:ewe {$scan->id} > /dev/null 2>&1 & echo $!";
            $scan->started_at = now();

            $exec = shell_exec($cmd);
            $pid = intval(trim($exec));

            if ($pid) {
                $scan->pid = $pid;
                $scan->status = 'running';
                $scan->started_at = now();
                $scan->save();

                return redirect()->route('scans.index')->with('success', 'Scan is running with PID ' . $pid . '.');
            } else {
                return redirect()->route('scans.index')->with('error', 'Failed to start scan.');
            }
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while running the scan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified scan and its output directory.
     */
    public function destroy($id)
    {
        try {
            $scan = Scans::find($id);
            if (!$scan) {
                return redirect()->route('scans.index')->with('error', 'Scan not found.');
            }

            if($scan->status == 'running'){
                return redirect()->route('scans.index')->with('error', 'Scan is running. Please stop it first.');
            }

            $directory = $scan->output_path;
            if (file_exists($directory)) {
                try {
                    File::deleteDirectory($directory);
                } catch (Exception $e) {
                    return redirect()->route('scans.index')->with('error', 'Failed to delete scan directory: ' . $e->getMessage());
                }
            }

            $scan->delete();

            return redirect()->route('scans.index')->with('success', 'Scan deleted successfully.');
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while deleting the scan: ' . $e->getMessage());
        }
    }

    /**
     * Stop a running scan process.
     */
    public function stop($id)
    {
        try {
            $scan = Scans::find($id);
            if (!$scan) {
                return redirect()->route('scans.index')->with('error', 'Scan not found.');
            }

            if (!$scan->pid) {
                return redirect()->route('scans.index')->with('error', 'No PID recorded for this scan.');
            }

            $pgid = intval($scan->pid);
            exec("kill -TERM -$pgid 2>&1", $output, $status);

            $log = file_exists($scan->process) ? file_get_contents($scan->process) : null;
            $logData = $log ? json_decode($log, true) : [];
            $logData = $this->setProcessStatus($logData);

            if ($status === 0) {
                $scan->status = 'stopped';
                $scan->pid = null;
                $scan->finished_at = now();
                $scan->save();
                return redirect()->route('scans.index')->with('success', 'Scan and subprocesses stopped successfully.');
            } else {
                $errorMsg = isset($output[0]) ? $output[0] : 'Process may not exist.';
                $scan->status = 'done';
                $scan->finished_at = now();
                $scan->save();
                return redirect()->route('scans.index')->with('error', 'Failed to stop scan. ' . $errorMsg);
            }
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while stopping the scan: ' . $e->getMessage());
        }
    }

    /**
     * Recursively set the status of all tasks in a process to 'stopped' if they are running, pending, or waiting.
     */
    public function setProcessStatus($process)
    {
        if (!is_array($process)) {
            return $process;
        }
        foreach ($process as &$task) {
            if (
                isset($task['status']) &&
                in_array($task['status'], ['running', 'pending', 'waiting'])
            ) {
                $task['status'] = 'stopped';
            }
            if (isset($task['tasks']) && is_array($task['tasks'])) {
                $task['tasks'] = $this->setProcessStatus($task['tasks']);
            }
        }
        return $process;
    }



    /**
     * Show logs for a specific scan.
     */
    public function logs($id)
    {
        try {
            $scan = Scans::find($id);
            if (!$scan) {
                return redirect()->route('scans.index')->with('error', 'Scan not found.');
            }

            return view('scans.logs', compact('scan'));
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while loading logs: ' . $e->getMessage());
        }
    }

    /**
     * Show the review page for a scan, listing CSV files in its output directory.
     */
    public function review($id)
    {
        try {
            $scan = Scans::find($id);
            if (!$scan) {
                return redirect()->route('scans.index')->with('error', 'Scan not found.');
            }

            $outputDir = $scan->output_path;
            $scope = $scan->scope;
            $files = [];

            if (file_exists($outputDir) && is_dir($outputDir)) {
                try {
                    $allFiles = File::allFiles($outputDir);
                    $csvFiles = array_filter($allFiles, function ($file) {
                        return strtolower($file->getExtension()) === 'csv';
                    });
                    $files = array_map(function ($file) {
                        return $file->getRealPath();
                    }, $csvFiles);
                } catch (Exception $e) {
                    return redirect()->route('scans.index')->with('error', 'Failed to read output directory: ' . $e->getMessage());
                }
            }

            $files = array_map(function ($file) {
                return [
                    'path' => $file,
                    'size' => File::size($file),
                ];
            }, $files);

            return view('scans.review', compact('scan', 'scope', 'files'));
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while loading review: ' . $e->getMessage());
        }
    }

    /**
     * Show the paginated result of a CSV file from a scan's output.
     */
    public function reviewResult(Request $request)
    {
        $basePath = rtrim(env('HUNT_PATH'), '/');
        $relativePath = ltrim($request->output, '/\\');

        // Prevent directory traversal
        if (strpos($relativePath, '..') !== false) {
            return redirect()->back()->with('error', 'File path not allowed.');
        }

        $output = realpath($basePath . '/' . $relativePath);

        // Ensure the file is within the allowed directory and exists
        if (!$output || strpos($output, $basePath) !== 0 || !File::exists($output)) {
            return redirect()->back()->with('error', 'The specified file does not exist or access is not allowed.');
        }

        // Only allow CSV files
        if (strtolower(pathinfo($output, PATHINFO_EXTENSION)) !== 'csv') {
            return redirect()->back()->with('error', 'Only CSV files are allowed.');
        }

        $perPage = 15;
        $page = $request->input('page', 1);

        $rows = [];
        if (($handle = fopen($output, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        $total = count($rows);
        $header = $total > 0 ? $rows[0] : [];
        $dataRows = $rows;
        if ($total > 0) {
            array_shift($dataRows);
        }

        $totalDataRows = count($dataRows);
        $offset = ($page - 1) * $perPage;
        $paginatedRows = array_slice($dataRows, $offset, $perPage);

        $paginator = new LengthAwarePaginator(
            $paginatedRows,
            $totalDataRows,
            $perPage,
            $page,
            [
                'path' => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('scans.review-result', [
            'paginatedRows' => $paginator,
            'header' => $header,
        ]);
    }

    public function output($id)
    {
        $scan = Scans::find($id);
        if (!$scan) {
            return redirect()->route('scans.index')->with('error', 'Scan not found.');
        }
        return view('scans.output', compact('scan'));
    }

    /**
     * Show the form for bulk creating scans.
     */
    public function bulkCreate(Request $request)
    {
        $scopes = Scopes::whereIn('id', $request->input('scope_ids'))->get();
        $workflows = Workflows::all();
        return view('scans.bulk-create', compact('scopes', 'workflows'));
    }

    /**
     * Store multiple scans at once.
     */
    public function bulkStore(Request $request)
    {
        try {
            $workflowMode = $request->input('workflow_mode', 'same');

            if ($workflowMode === 'same') {
                $request->validate([
                    'same_workflow' => 'required|exists:workflows,id',
                    'scope_ids' => 'required|array|min:1',
                    'scope_ids.*' => 'exists:scopes,id',
                ]);
            } else {
                $request->validate([
                    'workflows' => 'required|array|min:1',
                    'scope_ids' => 'required|array|min:1',
                    'scope_ids.*' => 'exists:scopes,id',
                    'workflows.*' => 'required|exists:workflows,id',
                ]);
            }
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'Validation failed: ' . $e->getMessage());
        }

        try {
            $scopeIds = $request->input('scope_ids', []);
            $scopes = Scopes::whereIn('id', $scopeIds)->get();

            if ($scopes->isEmpty()) {
                return redirect()->route('scans.index')->with('error', 'No valid targets selected.');
            }

            $createdScans = [];

            if ($workflowMode === 'same') {
                $workflowId = $request->input('same_workflow');
                $workflow = Workflows::find($workflowId);
                if (!$workflow) {
                    return redirect()->route('scans.index')->with('error', "Workflow not found.");
                }

                foreach ($scopes as $scope) {
                    $target = $scope->target;
                    $type = $scope->type;
                    $directory = rtrim($scope->output_path, '/') . '/' . $workflow->slug;

                    if (!file_exists($directory)) {
                        if (!@mkdir($directory, 0777, true) && !is_dir($directory)) {
                            return redirect()->route('scans.index')->with('error', "Failed to create scan directory for target: {$target}");
                        }
                    }

                    $scan = new Scans();
                    $scan->target = $target;
                    $scan->type = $type;
                    $scan->workflow_id = $workflow->id;
                    $scan->scope_id = $scope->id;
                    $scan->status = 'pending';
                    $scan->output_path = $directory;
                    $scan->save();

                    $createdScans[] = $scan;
                }
            } else {
                $workflowsInput = $request->input('workflows', []);
                foreach ($scopes as $scope) {
                    $workflowId = isset($workflowsInput[$scope->id]) ? $workflowsInput[$scope->id] : null;
                    if (!$workflowId) {
                        return redirect()->route('scans.index')->with('error', "No workflow selected for target: {$scope->target}");
                    }
                    $workflow = Workflows::find($workflowId);
                    if (!$workflow) {
                        return redirect()->route('scans.index')->with('error', "Workflow not found for target: {$scope->target}");
                    }

                    $target = $scope->target;
                    $type = $scope->type;
                    $directory = rtrim($scope->output_path, '/') . '/' . $workflow->slug;

                    if (!file_exists($directory)) {
                        if (!@mkdir($directory, 0777, true) && !is_dir($directory)) {
                            return redirect()->route('scans.index')->with('error', "Failed to create scan directory for target: {$target}");
                        }
                    }

                    $scan = new Scans();
                    $scan->target = $target;
                    $scan->type = $type;
                    $scan->workflow_id = $workflow->id;
                    $scan->scope_id = $scope->id;
                    $scan->status = 'pending';
                    $scan->output_path = $directory;
                    $scan->save();

                    $createdScans[] = $scan;
                }
            }

            return redirect()->route('scans.index')->with('success', count($createdScans) . ' scan(s) created successfully.');
        } catch (Exception $e) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while creating scans: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete scans and their output directories.
     */
    public function bulkDelete(Request $request)
    {
        $scanIds = $request->input('scan_ids', []);
        $scans = Scans::whereIn('id', $scanIds)->get();

        foreach ($scans as $scan) {
            if($scan->status == 'running'){
                return redirect()->route('scans.index')->with('error', 'Some scans are running. Please stop them first.');
            }

            if (!empty($scan->output_path) && is_dir($scan->output_path)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($scan->output_path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $fileinfo) {
                    if ($fileinfo->isDir()) {
                        @rmdir($fileinfo->getRealPath());
                    } else {
                        @unlink($fileinfo->getRealPath());
                    }
                }
                @rmdir($scan->output_path);
            }
            $scan->delete();
        }

        return redirect()->route('scans.index')->with('success', 'Scans and workflow directories deleted successfully.');
    }

    /**
     * Bulk run scans that are not already running.
     */
    public function bulkRun(Request $request)
    {
        $scanIds = $request->input('scan_ids', []);
        $scans = Scans::whereIn('id', $scanIds)->where('status', '!=', 'running')->get();
        $success = 0;
        $failed = 0;
        $messages = [];

        foreach ($scans as $scan) {
            try {
                $artisan = base_path('artisan');
                $cmd = "setsid php $artisan run:ewe {$scan->id} > /dev/null 2>&1 & echo $!";
                $scan->started_at = now();

                $pid = shell_exec($cmd);
                $pid = intval(trim($pid));

                $workflowData = [];
                try {
                    $workflowData = json_decode($scan->workflow->script, true);
                } catch (Exception $e) {
                    $failed++;
                    $messages[] = "Failed to decode workflow for scan ID {$scan->id}: " . $e->getMessage();
                    continue;
                }

                if ($pid) {
                    $scan->pid = $pid;
                    $scan->status = 'running';
                    $scan->save();
                    $success++;
                } else {
                    $failed++;
                    $messages[] = "Failed to start scan ID {$scan->id}.";
                }
            } catch (Exception $e) {
                $failed++;
                $messages[] = "Error while starting scan ID {$scan->id}: " . $e->getMessage();
            }
        }

        $message = "{$success} scan(s) started successfully.";
        if ($failed > 0) {
            $message .= " {$failed} scan(s) could not be started.";
            $message .= " " . implode(' ', $messages);
            return redirect()->route('scans.index')->with('error', $message);
        }
        return redirect()->route('scans.index')->with('success', $message);
    }

    /**
     * Bulk stop running scans.
     */
    public function bulkStop(Request $request)
    {
        $scanIds = $request->input('scan_ids', []);
        $success = 0;
        $failed = 0;
        $messages = [];

        $scans = Scans::whereIn('id', $scanIds)
            ->where('status', 'running')
            ->get();

        $requestedIds = collect($scanIds);
        $foundIds = $scans->pluck('id');
        $notFoundOrNotRunning = $requestedIds->diff($foundIds);

        foreach ($notFoundOrNotRunning as $id) {
            $failed++;
            $messages[] = "Scan with ID {$id} was not found or is not running.";
        }

        foreach ($scans as $scan) {
            try {
                if (!$scan) {
                    $failed++;
                    $messages[] = "Scan with ID {$scan->id} not found.";
                    continue;
                }

                if (!$scan->pid) {
                    $failed++;
                    $messages[] = "No PID recorded for scan ID {$scan->id}.";
                    continue;
                }

                $pgid = intval($scan->pid);
                exec("kill -TERM -$pgid 2>&1", $output, $status);

                $log = file_exists($scan->process) ? file_get_contents($scan->process) : null;
                $process = $log ? json_decode($log, true) : [];
                if (method_exists($this, 'setProcessStatus')) {
                    $process = $this->setProcessStatus($process);
                }
                $scan->process = $log;

                if ($status === 0) {
                    $scan->status = 'stopped';
                    $scan->pid = null;
                    $scan->finished_at = now();
                    $scan->save();
                    $success++;
                } else {
                    $errorMsg = isset($output[0]) ? $output[0] : 'Process may not exist.';
                    $scan->status = 'done';
                    $scan->finished_at = now();
                    $scan->save();
                    $failed++;
                    $messages[] = "Failed to stop scan ID {$scan->id}. {$errorMsg}";
                }
            } catch (Exception $e) {
                $failed++;
                $messages[] = "An error occurred while stopping scan ID {$scan->id}: " . $e->getMessage();
            }
        }

        $message = "{$success} scan(s) stopped successfully.";
        if ($failed > 0) {
            $message .= " {$failed} scan(s) could not be stopped.";
            $message .= " " . implode(' ', $messages);
            return redirect()->route('scans.index')->with('error', $message);
        }
        return redirect()->route('scans.index')->with('success', $message);
    }

    
}
