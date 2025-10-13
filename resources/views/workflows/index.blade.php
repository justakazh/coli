@extends('templates.v1')
@section('content')
@section('title', 'Workflows')

<div class="container-fluid" >
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show my-2" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show my-2" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body ">
            <form action="{{ url()->current() }}" method="GET">
                <div class="row g-3 align-items-end mb-1">
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                        <label for="workflow_search" class="form-label mb-1 fw-bold">Workflow Name</label>
                        <input type="text" id="workflow_search" class="form-control shadow-sm" name="name" placeholder="Search workflow..." value="{{ request('workflow') }}" autocomplete="off">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                        <label for="description_search" class="form-label mb-1 fw-bold">Description</label>
                        <input type="text" id="description_search" class="form-control shadow-sm" name="description" placeholder="Search description..." value="{{ request('description') }}" autocomplete="off">
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                        <label for="type_search" class="form-label mb-1 fw-bold">Type</label>
                        <select id="type_search" name="type" class="form-select shadow-sm">
                            <option value="">All Types</option>
                            <option value="diagram" {{ request('type') === 'diagram' ? 'selected' : '' }}>Diagram</option>
                            <option value="script" {{ request('type') === 'script' ? 'selected' : '' }}>Script</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 d-grid mb-2">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 d-grid mb-2">
                        <a href="#" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createWorkflowModal">
                            <i class="fas fa-plus me-1"></i> Create Workflow
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
                                <th style="white-space:nowrap;">Workflow Name</th>
                                <th style="white-space:nowrap;">Type</th>
                                <th style="white-space:nowrap;">Description</th>
                                <th style="white-space:nowrap;">Created At</th>
                                <th class="text-end" style="white-space:nowrap;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workflows as $workflow)
                                <tr>
                                    <td class="text-center fw-semibold" style="white-space:nowrap;">{{ $workflow->id }}</td>
                                    <td style="white-space:nowrap;max-width:220px;overflow:hidden;text-overflow:ellipsis;">
                                        <span class="d-inline-block text-truncate w-100" style="max-width:220px;">{{ $workflow->name }}</span>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        <span class="badge bg-secondary bg-opacity-75">
                                            {{ $workflow->type }}
                                        </span>
                                    </td>
                                    <td style="white-space:nowrap;max-width:300px;overflow:hidden;text-overflow:ellipsis;">
                                        <span class="d-inline-block text-truncate w-100" style="max-width:300px;">{{ $workflow->description ?? '-' }}</span>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        {{ $workflow->created_at ? $workflow->created_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="text-center" style="min-width:230px;white-space:nowrap;">
                                        <div class="d-flex justify-content-end align-items-center gap-2 flex-nowrap">
                                            <a href="@if($workflow->type == 'script') {{ route('workflows.edit.script', $workflow->id) }} @else {{ route('workflows.edit.diagram', $workflow->id) }} @endif" class="btn btn-primary btn-sm d-flex align-items-center" title="Edit Workflow">
                                                <i class="fas fa-edit"></i>
                                                <span class="d-none d-sm-inline ms-1">Edit</span>
                                            </a>
                                            <a href="{{ route('workflows.download', $workflow->id) }}" class="btn btn-primary btn-sm d-flex align-items-center" title="Download Workflow">
                                                <i class="fas fa-download"></i>
                                                <span class="d-none d-sm-inline ms-1">Download</span>
                                            </a>
                                            <form action="{{ route('workflows.destroy', $workflow->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this workflow?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center" title="Delete Workflow">
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
                                        <span class="fs-5">No workflows available yet.</span>
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



<div class="modal fade" id="createWorkflowModal" tabindex="-1" aria-labelledby="createWorkflowModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createWorkflowModalLabel">Create Workflow</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="workflow-type" class="col-form-label">Type:</label>
            <select class="form-control" id="workflow-type" name="type" required>
                <option value="" disabled selected>Select Type</option>
                <option value="diagram">Diagram</option>
                <option value="script">Script</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="createWorkflowButton">Create Workflow</button>
        </div>
    </div>
  </div>
</div>


@push('scripts')
<script>
    $(document).ready(function() {
    $('#createWorkflowButton').on('click', function(e) {
        e.preventDefault();
        if($('#workflow-type').val() == 'diagram') {
            //clear local storage
            localStorage.clear();
            window.location.href = '{{ route('workflows.create.diagram') }}';
        }
        else if($('#workflow-type').val() == 'script') {
            window.location.href = '{{ route('workflows.create.script') }}';
            }
        else {
            
        }
        });
    });
</script>
@endpush

@endsection
