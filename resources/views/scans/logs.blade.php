@extends('app.template')
@section('title', 'Scan Logs - COLI')
<?php
    // The $scan variable should be passed from the controller
    // $scan->process is a JSON string containing the structured process log
    $processLog = [];
    if (isset($scan) && $scan->process) {
        if(file_exists($scan->output_path . '/logs/logs.json')){
            $log = file_get_contents($scan->output_path . '/logs/logs.json');
            $processLog = json_decode($log, true);
            $processLog = $processLog['tasks'];
        }
        else{
            $processLog = [];
        }
    }

    // Function to get MermaidJS color and Bootstrap class for each status
    function getStatusInfo($status) {
        $status = strtolower($status);
        switch ($status) {
            case 'done':
                return [
                    'mermaid' => 'green',
                    'badge' => 'success',
                    'label' => 'Done'
                ];
            case 'error':
                return [
                    'mermaid' => 'red',
                    'badge' => 'danger',
                    'label' => 'Error'
                ];
            case 'running':
                return [
                    'mermaid' => 'yellow',
                    'badge' => 'warning text-dark',
                    'label' => 'Running'
                ];
            case 'stopped':
            case 'stoped':
                return [
                    'mermaid' => 'gray',
                    'badge' => 'secondary',
                    'label' => 'Stopped'
                ];
            case 'waiting':
            case 'pending':
                return [
                    'mermaid' => 'blue',
                    'badge' => 'primary text-white',
                    'label' => 'Waiting'
                ];
            default:
                return [
                    'mermaid' => 'gray',
                    'badge' => 'secondary',
                    'label' => ucfirst($status)
                ];
        }
    }

    // Recursive function to generate MermaidJS flowchart
    function renderMermaidTasks($tasks, &$nodes, &$edges, $parentId = null, &$counter = 1) {
        foreach ($tasks as $task) {
            $nodeId = 'T' . $counter++;
            $label = addslashes($task['name'] ?? 'Unnamed Task');
            $status = $task['status'] ?? 'unknown';
            $statusInfo = getStatusInfo($status);
            $color = $statusInfo['mermaid'];
            $nodes[] = "$nodeId([\"$label\"]):::status_$color";
            if ($parentId) {
                // Change line color to #646EFB
                $edges[] = "$parentId --> $nodeId";
            }
            if (!empty($task['tasks'])) {
                renderMermaidTasks($task['tasks'], $nodes, $edges, $nodeId, $counter);
            }
        }
    }

    // Recursive function to flatten tasks for table display
    function flattenTasks($tasks, $parent = null, &$flat = [], $level = 0) {
        foreach ($tasks as $task) {
            $flat[] = [
                'name' => $task['name'] ?? 'Unnamed Task',
                'status' => $task['status'] ?? 'unknown',
                'output' => $task['output'] ?? '',
                'error' => $task['error'] ?? '',
                'level' => $level,
                'command' => $task['command'] ?? '',
            ];
            if (!empty($task['tasks'])) {
                flattenTasks($task['tasks'], $task, $flat, $level + 1);
            }
        }
        return $flat;
    }

    $nodes = [];
    $edges = [];
    $counter = 1;
    if (!empty($processLog)) {
        renderMermaidTasks($processLog, $nodes, $edges, null, $counter);
        $flatTasks = flattenTasks($processLog);
    } else {
        $flatTasks = [];
    }

    // Function to display Bootstrap badge for status
    function getStatusBadge($status) {
        $info = getStatusInfo($status);
        return '<span class="badge bg-' . $info['badge'] . '">' . $info['label'] . '</span>';
    }

    // Recursive function to display all task logs (nested)
    function displayTaskLogs($tasks, $level = 0, &$idx = 1) {
        foreach ($tasks as $task) {
            echo '<tr>';
            echo '<td class="text-muted">' . $idx++ . '</td>';
            echo '<td><span style="/*padding-left:' . ($level * 18) . 'px*/">' . e($task['name'] ?? 'Unnamed Task') . '</span></td>';
            echo '<td>';
            echo getStatusBadge($task['status'] ?? 'unknown');
            echo '</td>';
            echo '<td>';

            if (env("ERROR_LOG") == "true") {
                $modalId = 'errorModal' . uniqid();
                echo '<button class="btn btn-link btn-sm p-0 text-secondary" data-bs-toggle="modal" data-bs-target="#' . $modalId . '">View</button>';
                // Error Modal
                echo '<div class="modal fade" id="' . $modalId . '" tabindex="-1" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">';
                echo '  <div class="modal-dialog modal-lg modal-dialog-scrollable">';
                echo '    <div class="modal-content">';
                echo '      <div class="modal-header">';
                echo '        <h5 class="modal-title" id="' . $modalId . 'Label">Logging: ' . e($task['name'] ?? 'Unnamed Task') . '</h5>';
                echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                echo '      </div>';
                echo '      <div class="modal-body">';
                echo '        <pre class="p-2 rounded text-danger" style="max-height:400px;overflow:auto;background:none;">' . e($task['stdout']) . '</pre>';
                echo '      </div>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
            else{
                echo '<span class="text-muted">-</span>';
            }

            echo '</td>';
            echo '<td>';
            if ((strtolower($task['status'] ?? '') === 'error') && !empty($task['error'])) {
                $modalId = 'errorModal' . uniqid();
                echo '<button class="btn btn-link btn-sm p-0 text-danger" data-bs-toggle="modal" data-bs-target="#' . $modalId . '">View</button>';
                // Error Modal
                echo '<div class="modal fade" id="' . $modalId . '" tabindex="-1" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">';
                echo '  <div class="modal-dialog modal-lg modal-dialog-scrollable">';
                echo '    <div class="modal-content">';
                echo '      <div class="modal-header">';
                echo '        <h5 class="modal-title" id="' . $modalId . 'Label">Task Error: ' . e($task['name'] ?? 'Unnamed Task') . '</h5>';
                echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                echo '      </div>';
                echo '      <div class="modal-body">';
                echo '        <pre class="p-2 rounded text-danger" style="max-height:400px;overflow:auto;background:none;">' . e($task['error']) . '</pre>';
                echo '      </div>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            } else {
                echo '<span class="text-muted">-</span>';
            }
            echo '</td>';
            echo '<td>';
            if (!empty($task['command'])) {
                echo '<span class="text-monospace small">' . $task['command'] . '</span>';
            } else {
                echo '<span class="text-muted">-</span>';
            }
            echo '</td>';
            echo '</tr>';
            // Loop children
            if (!empty($task['tasks'])) {
                displayTaskLogs($task['tasks'], $level + 1, $idx);
            }
        }
    }
?>

@section('content')
<div class="container-fluid px-0">
    <div class="card shadow-sm border-0 mb-4 w-100">
        <div class="card-body pb-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0 fw-bold">
                    <i class="mdi mdi-file-document-outline me-1"></i> Scan Process Logs
                </h4>
                <a href="{{ route('scans.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Scans
                </a>
            </div>
            @if(empty($processLog))
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="mdi mdi-alert me-2"></i>
                    <div>No process logs found for this scan.</div>
                </div>
            @else
                {{-- Diagram Section --}}
                <div class="card border-0 shadow-sm h-100 mb-4">
                    <div class="card-header border-bottom-0 pb-2">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-chart-timeline-variant me-2 text-info"></i>
                            <h6 class="mb-0 fw-semibold">Process Flow Diagram</h6>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="legend mb-3">
                            <span class="badge bg-success me-2">Done</span>
                            <span class="badge bg-danger me-2">Error</span>
                            <span class="badge bg-warning text-dark me-2">Running</span>
                            <span class="badge bg-primary text-white me-2">Waiting</span>
                            <span class="badge bg-secondary me-2">Stopped</span>
                        </div>
                        <div class="mermaid-container border rounded p-2" style="background: none;">
                            <pre class="mermaid" id="mermaid-log-diagram" style="background:none; margin-bottom:0;">
flowchart TD
@foreach($nodes as $node)
{!! $node !!}
@endforeach
@foreach($edges as $edge)
{!! $edge !!}
@endforeach

classDef status_green fill:#c6f6d5,stroke:#38a169,stroke-width:2px;
classDef status_red fill:#fed7d7,stroke:#e53e3e,stroke-width:2px;
classDef status_yellow fill:#fefcbf,stroke:#d69e2e,stroke-width:2px;
classDef status_gray fill:#e2e8f0,stroke:#718096,stroke-width:2px;
classDef status_blue fill:#bee3f8,stroke:#3182ce,stroke-width:2px;
%% Change line color to #646EFB
linkStyle default stroke:#646EFB,stroke-width:2px;
                            </pre>
                        </div>
                    </div>
                </div>
                {{-- Table Section --}}
                <div class="card border-0 shadow-sm h-100 mb-4">
                    <div class="card-header border-bottom-0 pb-2">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-format-list-bulleted-type me-2 text-info"></i>
                            <h6 class="mb-0 fw-semibold">Task Log Details</h6>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Task Name</th>
                                        <th>Status</th>
                                        <th>Logging</th>
                                        <th>Error</th>
                                        <th>Command</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($processLog))
                                        @php $idx = 1; @endphp
                                        {!! displayTaskLogs($processLog, 0, $idx) !!}
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="mdi mdi-folder-open mdi-24px d-block mb-2"></i>
                                                No task logs found.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({
        startOnLoad: true,
        theme: 'default',
        flowchart: {
            curve: 'basis',
            padding: 20
        }
    });
</script>
@endsection