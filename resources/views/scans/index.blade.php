@extends('app.template')
@section('title', 'Scans Management - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="card-title mb-0">
                        <i class="mdi mdi-radar me-2"></i>
                        Scan Management
                    </h3>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-check-circle-outline me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('scans.search') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="target" class="form-label">Target</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-crosshairs-gps"></i>
                                    </span>
                                    <input type="text" class="form-control" id="target" name="target" placeholder="Search by target..." value="{{ request('target') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="workflow" class="form-label">Workflow</label>
                                <select class="workflow-select2 form-select" id="workflow" name="workflow">
                                    <option value="">All Workflows</option>
                                    @forelse($workflows ?? [] as $workflow)
                                        <option value="{{ $workflow->id }}" {{ request('workflow') == $workflow->id ? 'selected' : '' }}>
                                            {{ $workflow->name }}
                                        </option>
                                    @empty
                                        <option disabled>No workflows available</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="running" {{ request('status') == 'running' ? 'selected' : '' }}>Running</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="stopped" {{ request('status') == 'stopped' ? 'selected' : '' }}>Stopped</option>
                                    <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify me-1"></i>
                                    Search
                                </button>
                                <a href="{{ route('scans.index') }}" class="btn btn-light">
                                    <i class="mdi mdi-refresh me-1"></i>
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <form id="bulk-action-form" method="POST">
                    @csrf
                    {{-- Change: use scan_ids[] for array --}}
                    <input type="hidden" name="scan_ids[]" id="bulk-scan-ids" value="">
                    <div class="mb-3 d-flex gap-2 align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="bulkActionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulkActionDropdown">
                                <li>
                                    <a class="dropdown-item" href="#" id="bulk-run-dropdown">
                                        <i class="mdi mdi-play-circle-outline"></i> Run Selected
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" id="bulk-stop-dropdown">
                                        <i class="mdi mdi-stop-circle-outline"></i> Stop Selected
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" id="bulk-delete-dropdown">
                                        <i class="mdi mdi-trash-can-outline"></i> Delete Selected
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
                <div class="table-responsive mt-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="select-all">
                                </th>
                                <th>Target</th>
                                <th>Type</th>
                                <th>Workflow</th>
                                <th>Started At</th>
                                <th>Finished At</th>
                                <th>Status</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scans ?? [] as $scan)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input scan-checkbox" value="{{ $scan->id }}">
                                </td>
                                <td class="text-wrap">
                                    @php
                                        $target = $scan->target;
                                        if (strpos($target, '/scans/outputs/') !== false && $scan->type == 'output') {
                                            $target = explode('/', $target);
                                            $target = array_slice($target, -2);
                                            $target = implode('/', $target);
                                            $target = $scan->scope->target . ' (' . $target . ')';
                                        }
                                        echo $target;
                                    @endphp
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($scan->type) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $scan->workflow->name }}
                                </td>
                                <td>
                                    {{ $scan->started_at ? \Carbon\Carbon::parse($scan->started_at)->format('d/m/Y H:i:s') : '-' }}
                                </td>
                                <td>
                                    @if($scan->finished_at && $scan->started_at)
                                        @if(\Carbon\Carbon::parse($scan->finished_at)->greaterThan(\Carbon\Carbon::parse($scan->started_at)))
                                            {{ \Carbon\Carbon::parse($scan->finished_at)->format('d/m/Y H:i:s') }}
                                        @else
                                            -
                                        @endif
                                    @elseif($scan->finished_at)
                                        {{ \Carbon\Carbon::parse($scan->finished_at)->format('d/m/Y H:i:s') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusIcons = [
                                            'done' => 'mdi-check-circle-outline',
                                            'running' => 'mdi-progress-clock',
                                            'pending' => 'mdi-timer-sand',
                                            'stopped' => 'mdi-cancel',
                                            'error' => 'mdi-alert-circle-outline'
                                        ];
                                        $statusColors = [
                                            'done' => 'success',
                                            'running' => 'warning',
                                            'pending' => 'secondary',
                                            'stopped' => 'secondary',
                                            'error' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$scan->status] ?? 'secondary';
                                        $statusIcon = $statusIcons[$scan->status] ?? 'mdi-help-circle-outline';
                                    @endphp
                                    @if($scan->status === 'error')
                                        <span class="badge bg-{{ $statusColor }} cursor-pointer" data-bs-toggle="modal" data-bs-target="#errorModal{{ $scan->id }}">
                                            <i class="mdi {{ $statusIcon }} me-1"></i>
                                            {{ ucfirst($scan->status) }}
                                        </span>

                                        <!-- Error Modal -->
                                        <div class="modal fade" id="errorModal{{ $scan->id }}" tabindex="-1" aria-labelledby="errorModalLabel{{ $scan->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="errorModalLabel{{ $scan->id }}">
                                                            <i class="mdi mdi-alert-circle-outline text-danger me-1"></i>
                                                            Error Details
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <pre class="alert alert-danger mb-0">{{ $scan->error }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-{{ $statusColor }}">
                                            <i class="mdi {{ $statusIcon }} me-1"></i>
                                            {{ ucfirst($scan->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-row flex-nowrap gap-2 align-items-center">
                                        @if($scan->status === 'pending')
                                            <button type="button" class="btn btn-outline-success btn-sm run-scan-btn" data-url="{{ route('scans.run', $scan->id) }}" title="Run Scan" data-bs-toggle="tooltip">
                                                <i class="mdi mdi-play-circle-outline"></i>
                                                <span class="d-none d-sm-inline">Start</span>
                                            </button>
                                        @endif

                                        @if($scan->status === 'running')
                                            <button type="button" class="btn btn-outline-warning btn-sm stop-scan-btn" data-url="{{ route('scans.stop', $scan->id) }}" title="Stop Scan" data-bs-toggle="tooltip">
                                                <i class="mdi mdi-stop-circle-outline"></i>
                                                <span class="d-none d-sm-inline">Stop</span>
                                            </button>
                                        @endif

                                        @if(in_array($scan->status, ['done', 'stopped', 'error']))
                                            <button type="button" class="btn btn-outline-primary btn-sm rerun-scan-btn" data-url="{{ route('scans.run', $scan->id) }}" title="Re-run Scan" data-bs-toggle="tooltip">
                                                <i class="mdi mdi-replay"></i>
                                                <span class="d-none d-sm-inline">Re-run</span>
                                            </button>
                                        @endif

                                        <a href="{{ route('scans.output', $scan->id) }}" class="btn btn-outline-info btn-sm" title="View Output" data-bs-toggle="tooltip">
                                            <i class="mdi mdi-folder-open-outline"></i>
                                            <span class="d-none d-sm-inline">Output</span>
                                        </a>

                                        <a href="{{ route('scans.logs', $scan->id) }}" class="btn btn-outline-secondary btn-sm" title="View Logs" data-bs-toggle="tooltip">
                                            <i class="mdi mdi-file-document-multiple-outline"></i>
                                            <span class="d-none d-sm-inline">Logs</span>
                                        </a>
                                        
                                        <form action="{{ route('scans.delete', $scan->id) }}" method="POST" class="d-inline delete-scan-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Scan" data-bs-toggle="tooltip">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                                <span class="d-none d-sm-inline">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="mdi mdi-information-outline h4 mb-2"></i>
                                        <p class="mb-0">No scans found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $scans->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Manager Modal -->
<div class="modal fade" id="file-manager-modal" tabindex="-1" aria-labelledby="fileManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileManagerModalLabel">
                    <i class="mdi mdi-folder-open-outline me-1"></i>
                    File Manager
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe src="" frameborder="0" style="width: 100%; height: 600px;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Logs Modal -->
<div class="modal fade" id="logs-modal" tabindex="-1" aria-labelledby="logsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logsModalLabel">
                    <i class="mdi mdi-file-document-multiple-outline me-1"></i>
                    Logs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe src="" frameborder="0" style="width: 100%; height: 600px;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // File Manager Modal
    $('#file-manager-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var path = button.data('path');
        $(this).find('iframe').attr('src', '{{ env('APP_URL') }}/assets/vendors/file-manager/filemanager.php?p=' + path +"&hash={{md5(env('FILE_MANAGER_USER').':'.env('FILE_MANAGER_PASS'))}}");
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Select/Deselect all
    $('#select-all').on('change', function() {
        $('.scan-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Update hidden input for bulk actions
    function updateBulkScanIds() {
        var selected = $('.scan-checkbox:checked').map(function(){ return this.value; }).get();
        // Change: set hidden input as array values
        // Remove previous hidden inputs
        $('#bulk-action-form input[name="scan_ids[]"]').remove();
        // Add new hidden inputs for each selected id
        selected.forEach(function(id) {
            $('#bulk-action-form').append('<input type="hidden" name="scan_ids[]" value="'+id+'">');
        });
    }
    $('.scan-checkbox, #select-all').on('change', updateBulkScanIds);

    // SweetAlert for Run Scan
    $('.run-scan-btn').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        Swal.fire({
            title: 'Start this scan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, start',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    // SweetAlert for Stop Scan
    $('.stop-scan-btn').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        Swal.fire({
            title: 'Stop this scan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, stop',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    // SweetAlert for Re-run Scan
    $('.rerun-scan-btn').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        Swal.fire({
            title: 'Re-run this scan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, re-run',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    // SweetAlert for Delete Scan
    $('.delete-scan-form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Are you sure you want to delete this scan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Bulk run from dropdown
    $('#bulk-run-dropdown').on('click', function(e) {
        e.preventDefault();
        var selected = $('.scan-checkbox:checked').map(function(){ return this.value; }).get();
        if(selected.length === 0) {
            Swal.fire('No scan selected', 'Please select at least one scan to run.', 'warning');
            return;
        }
        Swal.fire({
            title: 'Run selected scans?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, run',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('#bulk-action-form');
                form.attr('action', "{{ route('scans.bulk-run') }}");
                form.attr('method', 'POST');
                form.find('input[name="_method"]').remove();
                updateBulkScanIds();
                form.submit();
            }
        });
    });

    // Bulk stop from dropdown
    $('#bulk-stop-dropdown').on('click', function(e) {
        e.preventDefault();
        var selected = $('.scan-checkbox:checked').map(function(){ return this.value; }).get();
        if(selected.length === 0) {
            Swal.fire('No scan selected', 'Please select at least one scan to stop.', 'warning');
            return;
        }
        Swal.fire({
            title: 'Stop selected scans?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, stop',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('#bulk-action-form');
                form.attr('action', "{{ route('scans.bulk-stop') }}");
                form.attr('method', 'POST');
                form.find('input[name="_method"]').remove();
                updateBulkScanIds();
                form.submit();
            }
        });
    });

    // Bulk delete from dropdown
    $('#bulk-delete-dropdown').on('click', function(e) {
        e.preventDefault();
        var selected = $('.scan-checkbox:checked').map(function(){ return this.value; }).get();
        if(selected.length === 0) {
            Swal.fire('No scan selected', 'Please select at least one scan to delete.', 'warning');
            return;
        }
        Swal.fire({
            title: 'Delete selected scans?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('#bulk-action-form');
                form.attr('action', "{{ route('scans.bulk-delete') }}");
                form.attr('method', 'POST');
                if(form.find('input[name="_method"]').length === 0) {
                    form.append('<input type="hidden" name="_method" value="DELETE">');
                } else {
                    form.find('input[name="_method"]').val('DELETE');
                }
                updateBulkScanIds();
                form.submit();
            }
        });
    });

    // Add SweetAlert confirmation for all single delete scan forms
    $(document).on('submit', '.delete-scan-form', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Are you sure you want to delete this scan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

@endsection
