@extends('app.template')
@section('title', 'Workflows Management - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cog me-1"></i> Workflows
                    </h5>
                    <button id="add-workflow-btn" class="btn btn-success">
                        <i class="mdi mdi-plus me-1"></i> Add New Workflow
                    </button>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('workflows.search') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Search by name..." value="{{ request('name') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="tags" class="form-label">Tags</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-tag-multiple"></i>
                                    </span>
                                    <input type="text" class="form-control" id="tags" name="tags" placeholder="Search by tags..." value="{{ request('tags') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <option value="single" {{ request('category') == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="multiple" {{ request('category') == 'multiple' ? 'selected' : '' }}>Multiple</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="description" class="form-label">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-file-document-multiple-outline"></i>
                                    </span>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Search by description..." value="{{ request('description') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify me-1"></i> Search
                                </button>
                                <a href="{{ route('workflows.index') }}" class="btn btn-light">
                                    <i class="mdi mdi-refresh me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>Name</th>
                                <th>Tags</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th style="width: 120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workflows as $workflow)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $workflow->name }}</strong>
                                    </td>
                                    <td>{{ $workflow->tags }}</td>
                                    <td>{{ ucfirst($workflow->category) }}</td>
                                    <td>{{ Str::limit($workflow->description, 50) }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('workflows.edit', $workflow->id) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip"
                                               title="Edit workflow">
                                                <i class="mdi mdi-pencil"></i>
                                                <span class="d-none d-sm-inline">Edit</span>
                                            </a>
                                            <a href="{{ route('workflows.download', $workflow->id) }}" 
                                               class="btn btn-sm btn-outline-secondary"
                                               data-bs-toggle="tooltip"
                                               title="Download workflow">
                                                <i class="mdi mdi-download"></i>
                                                <span class="d-none d-sm-inline">Download</span>
                                            </a>
                                            <form action="{{ route('workflows.delete', $workflow->id) }}" 
                                                  method="POST" 
                                                  class="d-inline delete-workflow-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete workflow">
                                                    <i class="mdi mdi-delete"></i>
                                                    <span class="d-none d-sm-inline">Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-folder-open mdi-24px d-block mb-2"></i>
                                        No workflows found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $workflows->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // SweetAlert for Delete Workflow
    $('.delete-workflow-form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Are you sure you want to delete this workflow?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger mx-2',
                cancelButton: 'btn btn-light mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // SweetAlert for Add New Workflow
    $('#add-workflow-btn').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Select Create Workflow Type',
            html: `
                <div class="mb-3">
                    <select id="swal-workflow-type" class="form-select">
                        <option value="" selected disabled>Select type...</option>
                        <option value="design">Design</option>
                        <option value="json">JSON Script</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Continue',
            cancelButtonText: 'Cancel',
            focusConfirm: false,
            preConfirm: () => {
                const type = $('#swal-workflow-type').val();
                if (!type) {
                    Swal.showValidationMessage('Please select a workflow type');
                }
                return type;
            },
            customClass: {
                confirmButton: 'btn btn-primary mx-2',
                cancelButton: 'btn btn-light mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                if (result.value === 'design') {
                    window.location.href = "{{ route('workflows.create') }}?type=design";
                } else if (result.value === 'json') {
                    window.location.href = "{{ route('workflows.create') }}?type=json";
                }
            }
        });
    });
});
</script>

@endsection