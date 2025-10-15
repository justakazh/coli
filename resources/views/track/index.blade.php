@extends('templates.v1')
@section('content')
@section("title", "Track")

@push('styles')
<style>
@media (max-width: 576px) {
    .btn-text {
        display: none !important;
    }
}
</style>
@endpush

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
                                    <span class="badge bg-warning text-uppercase d-inline-flex align-items-center gap-2" style="cursor:pointer;white-space:nowrap;" data-bs-toggle="modal" data-bs-target="#sharedLogModal" title="Show Log"
                                          data-log="{{ htmlspecialchars($scan->log ?? 'No log available.', ENT_QUOTES, 'UTF-8') }}"
                                          data-title="Log for Scan {{ $scan->scope->target }}">
                                        <i class="fas fa-file-alt text-dark"></i>
                                    </span>
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
    $scan = $data['scan'];
    $processLog = [];
    $processName = null;
    $processDescription = null;
    // Drawflow-style process_log.json
    $drawflowData = null;
    if (isset($scan) && !empty($scan->output)) {
        $processLogPath = $scan->output . '/process_log.json';
        if (file_exists($processLogPath)) {
            $log = file_get_contents($processLogPath);
            $json = json_decode($log, true);
            $drawflowData = $json;
            // merge fallback if process_log is still array of tasks
            if (isset($json['tasks'])) {
                $processLog = $json['tasks'];
                $processName = $json['name'] ?? null;
                $processDescription = $json['description'] ?? null;
            }
        }
    }

    // Helper: status color to mermaid class
    function df_status_to_mermaid($status) {
        $status = strtolower(trim($status));
        if ($status === "finished") return "green";
        if ($status === "stoped" || $status === "stopped") return "gray";
        if ($status === "failed") return "red";
        if ($status === "running") return "yellow";
        if ($status === "pending") return "blue";
        return "blue";
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
            @if(empty($drawflowData))
                <div class="alert alert-warning mb-0 py-2 px-3" role="alert">
                    <i class="mdi mdi-alert me-2"></i>
                    No process logs found for this scan.
                </div>
            @else
                <pre class="mermaid" id="mermaid-log-diagram" style="background:none; margin-bottom:0;">
flowchart TD
@php
    // Build Mermaid nodes for each drawflow "task node"
    $dfNodes = [];
    $dfEdges = [];
    foreach ($drawflowData as $id => $node) {
        if (!is_array($node) || !isset($node['data']['name'])) continue;
        $nodeId = "N".$id;
        $title = addslashes($node['data']['name']);
        $status = isset($node['data']['status']) ? df_status_to_mermaid($node['data']['status']) : "blue";
        $dfNodes[$id] = $nodeId . "([\"$title\"]):::status_$status";
    }
    // Walk outputs to generate edges
    foreach ($drawflowData as $id => $node) {
        $fromId = "N".$id;
        // walk all outputs
        if (!empty($node['outputs']) && is_array($node['outputs'])) {
            foreach ($node['outputs'] as $outputName => $output) {
                if (!empty($output['connections']) && is_array($output['connections'])) {
                    foreach ($output['connections'] as $conn) {
                        $toIdStr = (string)($conn['node'] ?? '');
                        if (isset($dfNodes[$toIdStr])) {
                            $dfEdges[] = $fromId . " --> " . $dfNodes[$toIdStr];
                        }
                    }
                }
            }
        }
    }
    // Output node and edges
    foreach ($dfNodes as $line) { echo "$line\n"; }
    foreach ($dfEdges as $line) { echo "$line\n"; }
@endphp

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
            <div class="table-responsive" style="min-width: 330px;">
                
                <table class="table table-sm table-hover align-middle mb-0 w-100" style="min-width: 600px;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px;white-space:nowrap;">#</th>
                            <th style="white-space:nowrap;">Task Name</th>
                            <th style="white-space:nowrap;">Status</th>
                            <th style="white-space:nowrap;">Log</th>
                            <th style="white-space:nowrap;">Error</th>
                            <th style="white-space:nowrap;">Command</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            function statusColoredLabel($status) {
                                $status_lc = strtolower(trim($status));
                                switch ($status_lc) {
                                    case 'pending':
                                        return '<span class="badge bg-primary text-white">Pending</span>';
                                    case 'running':
                                        return '<span class="badge bg-warning text-dark">Running</span>';
                                    case 'failed':
                                        return '<span class="badge bg-danger">Failed</span>';
                                    case 'stoped':
                                    case 'stopped':
                                        return '<span class="badge bg-secondary">Stopped</span>';
                                    case 'finished':
                                        return '<span class="badge bg-success">Finished</span>';
                                    default:
                                        return '<span class="badge bg-light text-dark">'.ucfirst($status).'</span>';
                                }
                            }

                            // Prepare commands for modal
                            $commandsForModal = [];

                            if (!empty($processLog)) {
                                $idx = 1;
                                $displayTaskLogs = function($tasks, $level = 0) use (&$displayTaskLogs, &$idx, &$commandsForModal) {
                                    foreach ($tasks as $task) { ?>
                                        <tr>
                                            <td class="text-center"><?= $idx++ ?></td>
                                            <td><?= e(htmlspecialchars($task['name'] ?? 'Unnamed Task')) ?></td>
                                            <td><?= statusColoredLabel($task['status'] ?? 'pending') ?></td>

                                            <!-- Log -->
                                            <?php if (env("ERROR_LOG") == "true" && !empty($task['stdout'])): ?>
                                                <td>
                                                    <button class="btn btn-primary btn-sm btn-show-log-modal" 
                                                        data-log="<?= htmlspecialchars($task['stdout'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-title="Logging: <?= htmlspecialchars($task['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#sharedLogModal">
                                                        <i class="fas fa-file-alt"></i>
                                                        <span class="btn-text">Log</span>
                                                    </button>
                                                </td>
                                            <?php else: ?>
                                                <td>-</td>
                                            <?php endif; ?>

                                            <!-- Error -->
                                            <?php if (isset($task['status']) && strtolower($task['status']) === 'failed' && !empty($task['error'])): ?>
                                                <td>
                                                    <button class="btn btn-danger btn-sm btn-show-error-modal"
                                                        data-error="<?= htmlspecialchars($task['error'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-name="<?= htmlspecialchars($task['name'] ?? 'Task Error', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#sharedErrorModal">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        <span class="btn-text">Error</span>
                                                    </button>
                                                </td>
                                            <?php else: ?>
                                                <td>-</td>
                                            <?php endif; ?>

                                            <!-- Command -->
                                            <?php if (!empty($task['command'])): 
                                                $commandsForModal[] = [
                                                    'name' => $task['name'] ?? 'Command',
                                                    'command' => $task['command'],
                                                ];
                                            ?>
                                                <td>
                                                    <button class="btn btn-primary btn-sm btn-show-command-modal" 
                                                        data-command="<?= htmlspecialchars($task['command'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-name="<?= htmlspecialchars($task['name'] ?? 'Command', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#sharedCommandModal">
                                                        <i class="fas fa-terminal"></i>
                                                        <span class="btn-text">Command</span>
                                                    </button>
                                                </td>
                                            <?php else: ?>
                                                <td>-</td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php
                                        // Subtasks
                                        if (!empty($task['tasks'])) {
                                            $displayTaskLogs($task['tasks'], $level + 1);
                                        }
                                    }
                                };
                                $displayTaskLogs($processLog);
                            } elseif(!empty($drawflowData)) {
                                $idx = 1;
                                foreach ($drawflowData as $id => $node) {
                                    if (!is_array($node) || !isset($node['data']['name'])) continue;
                                    $data = $node['data'];
                                    $status = $data['status'] ?? "pending";
                                    $command = $data['command'] ?? "";
                                    $stdout = $data['stdout'] ?? "";
                                    $error = $data['error'] ?? "";
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $idx++ ?></td>
                                        <td><?= e($data['name'] ?? 'Unnamed Task') ?></td>
                                        <td><?= statusColoredLabel($status) ?></td>
                                        <!-- Log -->
                                        <?php if (env("ERROR_LOG") == "true" && !empty($stdout)): ?>
                                            <td>
                                                <button class="btn btn-link btn-sm p-0 btn-show-log-modal" 
                                                    data-log="<?= htmlspecialchars($stdout, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-title="Logging: <?= htmlspecialchars($data['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#sharedLogModal">
                                                    <i class="fas fa-file-alt"></i>
                                                    <span class="btn-text">Log</span>
                                                </button>
                                            </td>
                                        <?php else: ?>
                                            <td>-</td>
                                        <?php endif; ?>

                                        <!-- Error -->
                                        <?php if (strtolower($status) === 'failed' && !empty($error)): ?>
                                            <td>
                                                <button class="btn btn-danger btn-sm btn-show-error-modal"
                                                    data-error="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-name="<?= htmlspecialchars($data['name'] ?? 'Task Error', ENT_QUOTES, 'UTF-8') ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#sharedErrorModal">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <span class="btn-text">Error</span>
                                                </button>
                                            </td>
                                        <?php else: ?>
                                            <td>-</td>
                                        <?php endif; ?>

                                        <!-- Command -->
                                        <?php if (!empty($command)): 
                                            $commandsForModal[] = [
                                                'name' => $data['name'] ?? 'Command',
                                                'command' => $command,
                                            ];
                                        ?>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-show-command-modal"
                                                        data-command="<?= htmlspecialchars($command, ENT_QUOTES, 'UTF-8') ?>"
                                                        data-name="<?= htmlspecialchars($data['name'] ?? 'Command', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#sharedCommandModal">
                                                        <i class="fas fa-terminal"></i>
                                                        <span class="btn-text">Command</span>
                                                </button>
                                            </td>
                                        <?php else: ?>
                                            <td>-</td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php
                                }
                            } else {
                        ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="mdi mdi-folder-open mdi-24px d-block mb-2"></i>
                                    No task logs found.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ONE shared LOG modal --}}
<div class="modal fade" id="sharedLogModal" tabindex="-1" aria-labelledby="sharedLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sharedLogModalLabel">Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="sharedLogContent" class="p-2 rounded" style="max-height:60vh;overflow:auto;font-size:0.95em;white-space:pre-wrap;word-break:break-all;background:#212529;color:#fff;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ONE shared command modal (will be filled by JS) --}}
<div class="modal fade" id="sharedCommandModal" tabindex="-1" aria-labelledby="sharedCommandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sharedCommandModalLabel">Command</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="sharedCommandContent" class="p-2 rounded" style="max-height:400px;overflow:auto;font-size:0.95em;white-space:pre-wrap;word-break:break-all;background:none;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ONE shared error modal (akan diisi dengan JS) --}}
<div class="modal fade" id="sharedErrorModal" tabindex="-1" aria-labelledby="sharedErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sharedErrorModalLabel">Task Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="sharedErrorContent" class="p-2 rounded" style="max-height:400px;overflow:auto;white-space:pre-wrap;word-break:break-all;background:none;"></pre>
            </div>
        </div>
    </div>
</div>

</div>

@push('scripts')
<script>
//automatic refresh page 
setInterval(function() {
window.location.reload();
}, 60000); // 60000 ms = 1 minute

    
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

    // One modal for all logs
    document.querySelectorAll('.btn-show-log-modal, [data-bs-target="#sharedLogModal"]').forEach(function(btn){
        btn.addEventListener('click', function(e){
            let log = btn.getAttribute('data-log');
            let title = btn.getAttribute('data-title') || 'Log';
            document.getElementById('sharedLogContent').textContent = log;
            document.getElementById('sharedLogModalLabel').textContent = title;
        });
    });

    // One modal for all command
    document.querySelectorAll('.btn-show-command-modal').forEach(function(btn){
        btn.addEventListener('click', function(e){
            let command = btn.getAttribute('data-command');
            let name = btn.getAttribute('data-name');
            document.getElementById('sharedCommandContent').textContent = command;
            document.getElementById('sharedCommandModalLabel').textContent = 'Command: ' + (name ?? 'Command');
        });
    });

    // One modal for all errors
    document.querySelectorAll('.btn-show-error-modal').forEach(function(btn){
        btn.addEventListener('click', function(e){
            let error = btn.getAttribute('data-error');
            let name = btn.getAttribute('data-name');
            document.getElementById('sharedErrorContent').textContent = error;
            document.getElementById('sharedErrorModalLabel').textContent = 'Task Error: ' + (name ?? 'Task Error');
        });
    });
});

</script>
@endpush

@endsection
