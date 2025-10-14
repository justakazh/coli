@extends('templates.v1')
@section('content')

@section("title", "Explorer")


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


    {{-- Breadcrumb --}}
    @php
        $scanId = $data['scan']->id;
        $baseRoute = route('explorer', $scanId);
        $path = request('path', '');
        $segments = $path ? explode('/', $path) : [];
        $constructedPath = '';
    @endphp


    <div class="card shadow mb-3">
        <div class="card-body ">
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width:1000px;">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="white-space:nowrap;">#</th>
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
                                <td class="text-center fw-semibold" style="white-space:nowrap;">{{ $scan->id }}</td>
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
                                                    <pre style="background-color:#212529;padding:16px;border-radius:4px;font-size:0.95em;color:#fff;white-space:pre-wrap;word-break:break-all;">
{{ $scan->log ?? 'No log available.' }}
                                                    </pre>
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
            </div>
        </div>
    </div>


    <div class="card shadow ">
        <div class="card-body ">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb" class="flex-grow-1">
                    <ol class="breadcrumb p-2 rounded  mb-0 align-items-center">
                        <li class="breadcrumb-item">
                            <a href="{{ $baseRoute }}" class="text-decoration-none d-flex align-items-center">
                                <i class="fa fa-hdd me-1 text-warning"></i> <span class="fw-bold text-warning">Root</span>
                            </a>
                        </li>
                        @foreach($segments as $i => $segment)
                            @php
                                $constructedPath .= ($i === 0 ? '' : '/') . $segment;
                                $link = $baseRoute . '?path=' . urlencode($constructedPath);
                            @endphp
                            @if ($i < count($segments) - 1)
                                <li class="breadcrumb-item">
                                    <a href="{{ $link }}" class="text-decoration-none text-warning">{{ $segment }}</a>
                                </li>
                            @else
                                <li class="breadcrumb-item active fw-semibold text-warning" aria-current="page">{{ $segment }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
                <div class="ms-2">
                    <a href="{{ route('explorer.export', $data['scan']->id) }}" class="btn btn-primary btn-sm d-flex align-items-center" title="Export as ZIP">
                        <i class="fas fa-file-archive me-1"></i>
                        <span class="d-none d-sm-inline">Export ZIP</span>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width:768px;">
                        <thead class="table-dark align-middle">
                            <tr>
                                <th class="text-center" style="width:36px">#</th>
                                <th style="white-space:nowrap;">Name</th>
                                <th style="white-space:nowrap;">Type</th>
                                <th class="d-none d-sm-table-cell" style="white-space:nowrap;">Permissions</th>
                                <th class="d-none d-md-table-cell" style="white-space:nowrap;">Size</th>
                                <th class="d-none d-lg-table-cell" style="white-space:nowrap;">Modified</th>
                                <th class="text-end" style="white-space:nowrap;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Separate directories and files for sorting
                                $directories = [];
                                $files = [];
                                foreach($data['items'] as $item) {
                                    if($item['type'] === 'directory') {
                                        $directories[] = $item;
                                    } else {
                                        $files[] = $item;
                                    }
                                }
                                $sorted_items = array_merge($directories, $files);
                            @endphp

                            @forelse($sorted_items as $item)
                                <tr>
                                    <td class="text-center">
                                        @if($item['type'] == 'directory')
                                            <i class="fa fa-folder text-warning fa-lg"></i>
                                        @else
                                            <i class="fa fa-file text-secondary"></i>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-break">
                                        {{ $item['name'] }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item['type'] === 'directory' ? 'warning text-dark' : 'secondary' }} text-capitalize px-2 py-1">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td class="d-none d-sm-table-cell"><span class="font-monospace">{{ $item['permissions'] }}</span></td>
                                    <td class="d-none d-md-table-cell">
                                        @if($item['type'] == 'file') 
                                            {{ number_format($item['size']) }} 
                                        @else 
                                            <span class="text-muted">-</span> 
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($item['type'] == 'file') 
                                            {{ \Carbon\Carbon::createFromTimestamp($item['modified'])->format('Y-m-d H:i') }}
                                        @else 
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-1 flex-nowrap flex-wrap">
                                            @if($item['type'] == 'file')
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#createScanModal" data-file="@if(request('path')){{ request('path') }}/{{ $item['name'] }}@else{{ $item['name'] }}@endif" data-path="{{$data['scan']->output}}/@if(request('path')){{ request('path') }}/{{ $item['name'] }}@else{{ $item['name'] }}@endif" class="btn btn-primary btn-sm my-1" title="Create Scan">
                                                    <i class="fas fa-plus"></i><span class="d-none d-sm-inline"> Create Scan</span>
                                                </a>
                                                <a href="{{ route('explorer.view', $data['scan']->id) }}?path=@if(request('path')){{ request('path') }}/{{ $item['name'] }}@else{{ $item['name'] }}@endif" 
                                                   class="btn btn-primary btn-sm my-1" title="View">
                                                    <i class="fas fa-eye"></i><span class="d-none d-sm-inline"> View</span>
                                                </a>
                                                <a href="{{ route('explorer.download', $data['scan']->id) }}?path=@if(request('path')){{ request('path') }}/{{ $item['name'] }}@else{{ $item['name'] }}@endif" class="btn btn-primary btn-sm my-1" title="Download">
                                                    <i class="fas fa-download"></i><span class="d-none d-sm-inline"> Download</span>
                                                </a>
                                            @endif
                                            @if($item['type'] == 'directory')
                                                <a href="{{ route('explorer', $data['scan']->id) }}?path=@if(request('path')){{ request('path') }}/{{ $item['name'] }}@else{{ $item['name'] }}@endif" 
                                                   class="btn btn-primary btn-sm my-1" title="Open Directory">
                                                    <i class="fas fa-folder-open"></i><span class="d-none d-sm-inline"> Open</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <div>No files or directories found in this location.</div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="createScanModal" tabindex="-1" aria-labelledby="createScanModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createScanModalLabel"> <i class="fas fa-plus me-1"></i> Create New Scan</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('scans.create') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="scan-filename" class="form-label">File</label>
            <input type="text" class="form-control" id="scan-filename-view" name="target_view" readonly>
            <input type="hidden" class="form-control" id="scan-filename" name="target" readonly>
          </div>
          <div class="mb-3">
            <label for="workflow_id" class="col-form-label">Workflow:</label>
            <select class="form-control" id="workflow_id" name="workflow_id" required placeholder="Select workflow">
              <option value=""  selected>Select workflow</option>
              @foreach($data['workflows'] as $workflow)
                <option value="{{ $workflow->id }}">{{ $workflow->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create Scan</button>
        </div>
      </form>
    </div>
  </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var createScanModal = document.getElementById('createScanModal');
    createScanModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var filePath = button.getAttribute('data-path') || '';
        var fileName = button.getAttribute('data-file') || '';
        document.getElementById('scan-filename-view').value = fileName;
        document.getElementById('scan-filename').value = filePath;
    });
});
</script>
@endpush



@push('scripts')
{{-- SweetAlert2 Delete Handler --}}
<script>
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
