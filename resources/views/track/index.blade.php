@extends('templates.v1')
@section('content')



<div class="container-fluid" >

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show my-2" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show my-2" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif



    <div class="card shadow mb-3">
        <div class="card-body ">
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width:1000px;">
                        <thead class="table-dark">
                            <tr>
                                <th style="white-space:nowrap;">Target</th>
                                <th style="white-space:nowrap;">Workflow</th>
                                <th style="white-space:nowrap;">Started at</th>
                                <th style="white-space:nowrap;">Finished at</th>
                                <th style="white-space:nowrap;">Status</th>
                                <th class="text-end" style="white-space:nowrap;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $scan = $data['scan'];
                                $workflow_error = false;
                            @endphp
                            <tr>
                                <td style="white-space:nowrap;max-width:220px;overflow:hidden;text-overflow:ellipsis;">
                                    <span class="d-inline-block text-truncate w-100" style="max-width:220px;">{{ $scan->scope->target }}</span>
                                </td>
                                <td style="white-space:nowrap;">
                                    @if($scan->workflow)
                                        <span class="badge bg-secondary bg-opacity-75" style="white-space:nowrap;">
                                            <span class="d-inline-block text-truncate" style="max-width:140px;">{{ $scan->workflow->name }}</span>
                                        </span>
                                    @else
                                        @php $workflow_error = true; @endphp
                                        <span class="badge bg-danger bg-opacity-75" style="white-space:nowrap;">
                                            Error: Workflow not found
                                        </span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;">
                                    @if($scan->started_at)
                                        {{ \Carbon\Carbon::parse($scan->started_at)->format('d M Y H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;">
                                    @if($scan->finished_at)
                                        {{ \Carbon\Carbon::parse($scan->finished_at)->format('d M Y H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;">
                                    @php
                                        $status = strtolower($scan->status);
                                        $statusColor = 'secondary';
                                        $statusIcon = 'fa-circle';
                                        if ($status == 'running') {
                                            $statusColor = 'warning text-dark';
                                            $statusIcon = 'fa-spinner fa-spin';
                                        } elseif ($status == 'finished') {
                                            $statusColor = 'success';
                                            $statusIcon = 'fa-check';
                                        } elseif ($status == 'failed' || $status == 'stopped') {
                                            $statusColor = 'danger';
                                            $statusIcon = 'fa-times';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} text-uppercase d-inline-flex align-items-center gap-2" style="white-space:nowrap;">
                                        <i class="fas {{ $statusIcon }}"></i> {{ $scan->status }}
                                    </span>
                                    @if($scan->status == 'failed')
                                    <span class="badge bg-warning text-uppercase d-inline-flex align-items-center gap-2" style="cursor:pointer;white-space:nowrap;" data-bs-toggle="modal" data-bs-target="#logModal-{{ $scan->id }}" title="Show Log">
                                        <i class="fas fa-file-alt text-dark"></i>
                                    </span>
                                    <!-- Modal -->
                                    <div class="modal fade" id="logModal-{{ $scan->id }}" tabindex="-1" aria-labelledby="logModalLabel-{{ $scan->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="logModalLabel-{{ $scan->id }}">Log for Scan {{ $scan->scope->target }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body" style="max-height:60vh;overflow:auto;">
                                                    <pre style="background-color:#212529;padding:16px;border-radius:4px;font-size:0.95em;color:#fff;white-space:pre-wrap;word-break:break-all;">{{ $scan->log ?? 'No log available.' }}</pre>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td class="text-center" style="min-width:230px;white-space:nowrap;">
                                    <div class="d-flex justify-content-end align-items-center gap-2 flex-nowrap">
                                        @if($workflow_error != true)
                                            <form action="{{  route('scans.action', $scan->id) }}" method="POST" class="d-inline stop-scan-form" style="white-space:nowrap;">
                                                @csrf
                                                @if($scan->status == 'running')
                                                    <input type="hidden" name="action" value="stop">
                                                    <button type="button" id="scan_stop" class="btn btn-primary btn-sm d-flex align-items-center btn-stop-scan" title="Stop Scan">
                                                        <i class="fas fa-stop"></i>
                                                        <span class="d-none d-sm-inline ms-1">Stop</span>
                                                    </button>
                                                @elseif($scan->status == 'pending' )
                                                    <input type="hidden" name="action" value="start">
                                                    <button type="button" id="scan_start" class="btn btn-primary btn-sm d-flex align-items-center btn-start-scan" title="Start Scan">
                                                        <i class="fas fa-play"></i>
                                                        <span class="d-none d-sm-inline ms-1">Start</span>
                                                    </button>
                                                @elseif($scan->status == 'finished' || $scan->status == 'failed' || $scan->status == 'stopped')
                                                    <input type="hidden" name="action" value="start">
                                                    <button type="button" id="scan_rescan" class="btn btn-primary btn-sm d-flex align-items-center btn-rescan-scan" title="Rescan">
                                                        <i class="fas fa-redo"></i>
                                                        <span class="d-none d-sm-inline ms-1">Rescan</span>
                                                    </button>
                                                @endif
                                            </form>
                                            <a href="{{ route('scans.track', $scan->id) }}" class="btn btn-primary btn-sm d-flex align-items-center flex-shrink-0" title="View Scan" style="white-space:nowrap;">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                                <span class="d-none d-sm-inline ms-1">Track</span>
                                            </a>
                                            <a href="{{ route('explorer', $scan->id) }}" class="btn btn-primary btn-sm d-flex align-items-center" title="View Scan" style="white-space:nowrap;">
                                                <i class="fas fa-folder-open"></i>
                                                <span class="d-none d-sm-inline ms-1">Explorer</span>
                                            </a>
                                        @endif
                                        <form action="{{ route('scans.destroy', $scan->id) }}" method="POST" class="d-inline delete-scan-form" style="white-space:nowrap;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center btn-delete-scan" title="Delete Scan">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-none d-sm-inline ms-1">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </table>
            </div>
        </div>
    </div>

    @php
// $scan should be passed from the controller.
// We expect $scan->output to be the path containing process_log.json
$processLog = [];
$processName = null;
$processDescription = null;

// The only allowed statuses.
$allowedStatusMap = [
    ''          => 'pending', // treat empty as pending
    'pending'   => 'pending',
    'running'   => 'running',
    'failed'    => 'failed',
    'stoped'    => 'stoped',
    'finished'  => 'finished',
];

// Canonical display format for allowed statuses
function statusDisplayMap($status) {
    switch ($status) {
        case 'pending':
            return ['mermaid' => 'blue',    'badge' => 'primary text-white', 'label' => 'Pending'];
        case 'running':
            return ['mermaid' => 'yellow',  'badge' => 'warning text-dark',  'label' => 'Running'];
        case 'failed':
            return ['mermaid' => 'red',     'badge' => 'danger',             'label' => 'Failed'];
        case 'stoped':
            return ['mermaid' => 'gray',    'badge' => 'secondary',          'label' => 'Stopped'];
        case 'finished':
            return ['mermaid' => 'green',   'badge' => 'success',            'label' => 'Finished'];
        default:
            return ['mermaid' => 'gray',    'badge' => 'secondary',          'label' => ucfirst($status)];
    }
}

// Helper to transform incoming status to allowed set only
function normalizeStatus($status) {
    $status = strtolower(trim($status));
    if ($status === '') return 'pending';
    if (in_array($status, ['pending', 'running', 'failed', 'stoped', 'finished'])) return $status;
    // Additional aliases
    if ($status === 'stopped')   return 'stoped';
    if ($status === 'done')      return 'finished';
    if ($status === 'error')     return 'failed';
    return 'pending';
}

// Recursively transform all tasks' statuses only to allowed set
function enforceAllowedTaskStatuses(&$tasks) {
    foreach ($tasks as &$t) {
        $t['status'] = normalizeStatus($t['status'] ?? '');
        if (!empty($t['tasks'])) {
            enforceAllowedTaskStatuses($t['tasks']);
        }
    }
}

// Load process log if available
if (isset($scan) && !empty($scan->output)) {
    $processLogPath = $scan->output . '/process_log.json';
    if (file_exists($processLogPath)) {
        $log = file_get_contents($processLogPath);
        $processJson = json_decode($log, true);
        if (is_array($processJson)) {
            $processLog = $processJson['tasks'] ?? [];
            $processName = $processJson['name'] ?? null;
            $processDescription = $processJson['description'] ?? null;
        }
    }
}
if (!empty($processLog)) {
    enforceAllowedTaskStatuses($processLog);
}

// Recursive function to generate MermaidJS flowchart
function renderMermaidTasks($tasks, &$nodes, &$edges, $parentId = null, &$counter = 1) {
    foreach ($tasks as $task) {
        $nodeId = 'T' . $counter++;
        $label = addslashes($task['name'] ?? 'Unnamed Task');
        $status = $task['status'] ?? 'pending';
        $statusInfo = statusDisplayMap($status);
        $color = $statusInfo['mermaid'];
        $nodes[] = "$nodeId([\"$label\"]):::status_$color";
        if ($parentId) {
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
            'status' => $task['status'] ?? 'pending',
            'output' => $task['output'] ?? '',
            'error' => $task['error'] ?? '',
            'level' => $level,
            'command' => $task['command'] ?? '',
            'stdout' => $task['stdout'] ?? '',
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
    $info = statusDisplayMap($status);
    return '<span class="badge bg-' . $info['badge'] . '">' . $info['label'] . '</span>';
}

// Recursive function to display all task logs (nested)
function displayTaskLogs($tasks, $level = 0, &$idx = 1) {
    foreach ($tasks as $task) {
        echo '<tr>';
        echo '<td class="text-muted">' . $idx++ . '</td>';
        echo '<td><span style="">' . e($task['name'] ?? 'Unnamed Task') . '</span></td>';
        echo '<td>';
        echo getStatusBadge($task['status'] ?? 'pending');
        echo '</td>';

        // Logging column
        echo '<td>';
        if (env("ERROR_LOG") == "true") {
            $modalId = 'stdoutModal' . uniqid();
            if (!empty($task['stdout'])) {
                echo '<button class="btn btn-link btn-sm p-0 text-secondary" data-bs-toggle="modal" data-bs-target="#' . $modalId . '">View</button>';
                // Stdout Modal
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
            } else {
                echo '<span class="text-muted">-</span>';
            }
        } else {
            echo '<span class="text-muted">-</span>';
        }
        echo '</td>';

        // Error column: Only for status == failed
        echo '<td>';
        if ($task['status'] === 'failed' && !empty($task['error'])) {
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

        // Command column
        echo '<td>';
        if (!empty($task['command'])) {
            echo '<span class="text-monospace small">' . e($task['command']) . '</span>';
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
@endphp

<div class="card border-0 shadow-sm h-100 mb-4">
<div class="card-header border-bottom-0 pb-2">
    <div class="d-flex align-items-center">
        <i class="mdi mdi-chart-timeline-variant me-2 text-info"></i>
        <h6 class="mb-0 fw-semibold">
            Process Flow Diagram
            @if($processName)
                <small class="text-muted fw-normal ms-2">{{ $processName }}</small>
            @endif
        </h6>
    </div>
</div>
<div class="card-body pt-2">
    <div class="legend mb-3">
        <span class="badge bg-primary text-white me-2">Pending</span>
        <span class="badge bg-warning text-dark me-2">Running</span>
        <span class="badge bg-danger me-2">Failed</span>
        <span class="badge bg-secondary me-2">Stopped</span>
        <span class="badge bg-success me-2">Finished</span>
    </div>
    <div class="mermaid-container border rounded p-2" style="background: none;">
        @if(empty($processLog))
            <div class="alert alert-warning mb-0 py-2 px-3" role="alert">
                <i class="mdi mdi-alert me-2"></i>
                No process logs found for this scan.
            </div>
        @else
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
        @endif
    </div>
</div>
</div>
<div class="card border-0 shadow-sm h-100 mb-4">
<div class="card-header border-bottom-0 pb-2">
    <div class="d-flex align-items-center">
        <i class="mdi mdi-format-list-bulleted-type me-2 text-info"></i>
        <h6 class="mb-0 fw-semibold">Task Log Details</h6>
    </div>
</div>
<div class="card-body pt-2">
    <div class="table-responsive">
        <div class="table-responsive" style="min-width: 330px;">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width:700px;">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width:40px;white-space:nowrap;">#</th>
                        <th style="white-space:nowrap;">Task Name</th>
                        <th style="white-space:nowrap;">Status</th>
                        <th style="white-space:nowrap;">Logging</th>
                        <th style="white-space:nowrap;">Error</th>
                        <th style="white-space:nowrap;">Command</th>
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
</div>

</div>







@push('scripts')
<script>
    mermaid.initialize({
        startOnLoad: true,
        theme: 'default',
        flowchart: {
            curve: 'basis',
            padding: 20
        }
    });


document.addEventListener('DOMContentLoaded', function () {
    // Delete confirmation
    document.querySelectorAll('.btn-delete-scan').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Delete Scan',
                text: 'Are you sure you want to delete this scan?',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Delete',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
                background: '#212529',
                color: '#fff',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if(result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });

    // Start confirmation
    document.querySelectorAll('.btn-start-scan').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'Start Scan',
                text: 'Are you sure you want to start this scan?',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-play me-1"></i> Start',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
                background: '#212529',
                color: '#fff',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if(result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });

    // Stop confirmation
    document.querySelectorAll('.btn-stop-scan').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Stop Scan',
                text: 'Are you sure you want to stop this scan?',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-stop me-1"></i> Stop',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
                background: '#212529',
                color: '#fff',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if(result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });

    // Rescan confirmation
    document.querySelectorAll('.btn-rescan-scan').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'Rescan',
                text: 'Are you sure you want to re-run this scan?',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-redo me-1"></i> Rescan',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
                background: '#212529',
                color: '#fff',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if(result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });
});
</script>
@endpush


@endsection
