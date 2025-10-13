@extends('templates.v1')
@section('content')
@section('title', 'Scans')



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

    <div class="card mb-3">
        <div class="card-body ">
            <form action="{{ url()->current() }}" method="GET">
                <div class="row g-3 align-items-end mb-1">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                        <label for="target_search" class="form-label mb-1 fw-bold">Target</label>
                        <input type="text" id="target_search" class="form-control shadow-sm" name="target" placeholder="Search target..." value="{{ request('target') }}" autocomplete="off">
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                        <label for="workflow_search" class="form-label mb-1 fw-bold">Workflow</label>
                        <select id="workflow_search" class="form-select shadow-sm" name="workflow">
                            <option value="">All workflows</option>
                            @foreach($data['workflows'] as $workflow)
                                <option value="{{ $workflow->id }}" {{ request('workflow') == $workflow->id ? 'selected' : '' }}>
                                    {{ $workflow->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                        <label for="status_search" class="form-label mb-1 fw-bold">Status</label>
                        <select id="status_search" class="form-select shadow-sm" name="status">
                            <option value="">All statuses</option>
                            @foreach(['pending' => 'Pending', 'running' => 'Running', 'finished' => 'Finished', 'failed' => 'Failed', 'stopped' => 'Stopped'] as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 d-grid mb-2">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 d-grid mb-2">
                        <a href="#" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createScanModal">
                            <i class="fas fa-plus me-1"></i> Create Scan
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="card shadow ">
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
                        @forelse($data['scans'] as $scan)
                            <tr>
                                <td class="text-center fw-semibold" style="white-space:nowrap;">{{ $scan->id }}</td>
                                <td style="white-space:nowrap;max-width:220px;overflow:hidden;text-overflow:ellipsis;">
                                    <span class="d-inline-block text-truncate w-100" style="max-width:220px;">{{ $scan->scope->target }}</span>
                                </td>
                                <td style="white-space:nowrap;">
                                    @php $workflow_error = false; @endphp
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
                                            @if($scan->status != "pending")
                                            <a href="{{ route('scans.track', $scan->id) }}" class="btn btn-primary btn-sm d-flex align-items-center flex-shrink-0" title="View Scan" style="white-space:nowrap;">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                                <span class="d-none d-sm-inline ms-1">Track</span>
                                            </a>
                                            <a href="{{ route('explorer', $scan->id) }}" class="btn btn-primary btn-sm d-flex align-items-center" title="View Scan" style="white-space:nowrap;">
                                                <i class="fas fa-folder-open"></i>
                                                <span class="d-none d-sm-inline ms-1">Explorer</span>
                                            </a>
                                            @endif
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
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted border-0">
                                    <div class="mb-2">
                                        <i class="fas fa-inbox fa-2x"></i>
                                    </div>
                                    <span class="fs-5">No scans available yet.</span>
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
            <label for="target" class="col-form-label">Target:</label>
            <input type="text" class="form-control" id="target" name="target" required placeholder="Enter target...">
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
