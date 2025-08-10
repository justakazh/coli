@extends('app.template')
@section('title', 'Scope Management - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="card-title mb-0">
                        <i class="mdi mdi-target me-2"></i>
                        Scope Management
                    </h3>
                    <a href="{{ route('scopes.create') }}" class="btn btn-success">
                        <i class="mdi mdi-plus me-1"></i>
                        Add New Scope
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                        <form action="{{ route('scopes.search') }}" method="GET" class="row g-3">
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
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="ip" {{ request('type') == 'ip' ? 'selected' : '' }}>IP</option>
                                    <option value="domain" {{ request('type') == 'domain' ? 'selected' : '' }}>Domain</option>
                                    <option value="url" {{ request('type') == 'url' ? 'selected' : '' }}>URL</option>
                                    <option value="cidr" {{ request('type') == 'cidr' ? 'selected' : '' }}>CIDR</option>
                                    <option value="mobile_app" {{ request('type') == 'mobile_app' ? 'selected' : '' }}>Mobile App</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="description" class="form-label">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-text-box-outline"></i>
                                    </span>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Search by description..." value="{{ request('description') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify me-1"></i>
                                    Search
                                </button>
                                <a href="{{ route('scopes.index') }}" class="btn btn-light">
                                    <i class="mdi mdi-refresh me-1"></i>
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <form id="bulk-action-form" method="POST">
                    @csrf
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="bulk-action-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulk-action-dropdown">
                                <li>
                                    <a class="dropdown-item" href="#" id="bulk-scan-dropdown">
                                        <i class="mdi mdi-shield-search me-1"></i> Create Scan for Selected
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" id="bulk-delete-dropdown">
                                        <i class="mdi mdi-delete-outline me-1"></i> Delete Selected
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- The checkboxes for selected scopes are outside this form, so we will append them dynamically in JS -->
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="select-all-scopes">
                                </th>
                                <th>Target</th>
                                <th>Type</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scopes ?? [] as $scope)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input scope-checkbox" value="{{ $scope->id }}">
                                </td>
                                <td class="text-wrap">{{ $scope->target }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($scope->type) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 align-items-center">
                                        <a class="btn btn-outline-info btn-sm d-flex align-items-center" href="{{ route('scans.create', $scope->id) }}" title="New Scan" data-bs-toggle="tooltip">
                                            <i class="mdi mdi-shield-search me-1"></i> <span class="d-none d-sm-none d-md-inline">Scan</span>
                                        </a>
                                        <a class="btn btn-outline-warning btn-sm d-flex align-items-center" href="{{ route('scans.search') }}?scope_id={{ $scope->id }}" title="Scan History" data-bs-toggle="tooltip">
                                            <i class="mdi mdi-history me-1"></i> <span class="d-none d-sm-none d-md-inline">History</span>
                                        </a>
                                        <a class="btn btn-outline-primary btn-sm d-flex align-items-center" href="{{ route('scopes.edit', $scope->id) }}" title="Edit Scope" data-bs-toggle="tooltip">
                                            <i class="mdi mdi-pencil-outline me-1"></i> <span class="d-none d-sm-none d-md-inline">Edit</span>
                                        </a>
                                        <form action="{{ route('scopes.delete', $scope->id) }}" method="POST" class="d-inline delete-scope-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center" title="Delete Scope" data-bs-toggle="tooltip">
                                                <i class="mdi mdi-delete-outline me-1"></i> <span class="d-none d-sm-none d-md-inline">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="mdi mdi-information-outline h4 mb-2"></i>
                                        <p class="mb-0">No scopes found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $scopes->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // SweetAlert for Delete Scope
    $(document).on('submit', '.delete-scope-form', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Are you sure you want to delete this scope?',
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

    // Select/Deselect all checkboxes
    $('#select-all-scopes').on('change', function() {
        $('.scope-checkbox').prop('checked', this.checked);
    });

    // If any checkbox is unchecked, uncheck the "select all"
    $(document).on('change', '.scope-checkbox', function() {
        if (!this.checked) {
            $('#select-all-scopes').prop('checked', false);
        } else if ($('.scope-checkbox:checked').length === $('.scope-checkbox').length) {
            $('#select-all-scopes').prop('checked', true);
        }
    });

    // Helper: append selected checkboxes to the bulk form
    function appendSelectedToBulkForm() {
        var form = $('#bulk-action-form');
        // Remove any previously appended hidden inputs
        form.find('input[name="scope_ids[]"]').remove();
        $('.scope-checkbox:checked').each(function() {
            form.append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'scope_ids[]')
                    .val($(this).val())
            );
        });
    }

    // Bulk delete from dropdown
    $('#bulk-delete-dropdown').on('click', function(e) {
        e.preventDefault();
        var selected = $('.scope-checkbox:checked').map(function(){ return this.value; }).get();
        if(selected.length === 0) {
            Swal.fire('No scope selected', 'Please select at least one scope to delete.', 'warning');
            return;
        }
        Swal.fire({
            title: 'Delete selected scopes?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('#bulk-action-form');
                form.attr('action', "{{ route('scopes.bulk-delete') }}");
                form.attr('method', 'POST');
                // Remove any previous _method
                form.find('input[name="_method"]').remove();
                // Add _method DELETE
                form.append('<input type="hidden" name="_method" value="DELETE">');
                appendSelectedToBulkForm();
                form.submit();
            }
        });
    });

    // Bulk scan from dropdown
    $('#bulk-scan-dropdown').on('click', function(e) {
        e.preventDefault();
        var selected = $('.scope-checkbox:checked').map(function(){ return this.value; }).get();
        if(selected.length === 0) {
            Swal.fire('No scope selected', 'Please select at least one scope to create scan.', 'warning');
            return;
        }
        Swal.fire({
            title: 'Create scan for selected scopes?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, create scan',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('#bulk-action-form');
                form.attr('action', "{{ route('scans.bulk-create') }}");
                form.attr('method', 'POST');
                // Remove any previous _method
                form.find('input[name="_method"]').remove();
                appendSelectedToBulkForm();
                form.submit();
            }
        });
    });
});
</script>

@endsection
