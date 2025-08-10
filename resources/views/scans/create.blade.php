@extends('app.template')
@section('title', 'Create Scans - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="mdi mdi-radar me-2"></i>
                    Create New Scan
                </h5>

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

                <form action="{{ route('scans.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="scope_id" value="{{ $scope->id }}">

                    <div class="card mb-3">
                        <div class="card-header py-2">
                            <h6 class="card-title mb-0">
                                <i class="mdi mdi-crosshairs-gps me-1"></i>
                                Select Target
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th width="60px" class="text-center">
                                                <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                                            </th>
                                            <th width="120px">
                                                <i class="mdi mdi-shape-outline"></i> Type
                                            </th>
                                            <th>
                                                <i class="mdi mdi-target-variant"></i> Target
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">
                                                <input type="radio" name="target" class="form-check-input" value="{{ $scope->target }}"
                                                    {{ old('target', '') == $scope->target ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ ucfirst($scope->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <i class="mdi mdi-crosshairs-gps me-1"></i>
                                                {{ $scope->target }}
                                            </td>
                                        </tr>
                                        @foreach($files as $file)
                                            @php
                                                $relativeFile = str_replace(env('HUNT_PATH'), '', $file);
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    <input type="radio" name="target" class="form-check-input" value="{{ $relativeFile }}"
                                                        {{ old('target', '') == $relativeFile ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        Output
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="mdi mdi-file-document-outline me-1"></i>
                                                    {{ $relativeFile }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="workflow_id" class="form-label">
                            <i class="mdi mdi-cog-outline me-1"></i>
                            Select Workflow
                        </label>
                        <select name="workflow_id" id="workflow_id" class="workflow-select2 form-select @error('workflow_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('workflow_id') ? '' : 'selected' }}>
                                <i class="mdi mdi-chevron-down"></i> Choose a workflow...
                            </option>
                            @if(count($workflows) > 0)
                                @foreach($workflows->groupBy('category') as $category => $groupedWorkflows)
                                    <optgroup label="{{ ucfirst($category) }}">
                                        @foreach($groupedWorkflows as $workflow)
                                            <option value="{{ $workflow->id }}" 
                                                {{ old('workflow_id') == $workflow->id ? 'selected' : '' }}>
                                                {{ ucfirst($workflow->category) }} - {{ ucfirst($workflow->name) }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @else
                                <option value="" disabled>
                                    <i class="mdi mdi-alert-outline"></i> No workflows available
                                </option>
                            @endif
                        </select>
                        @error('workflow_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Please select the workflow to use for this scan. Workflows are grouped by category for easier navigation.
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-end gap-3 mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center">
                            <i class="mdi mdi-plus-circle-outline me-2"></i>
                            <span class="fw-semibold">Create Scan</span>
                        </button>
                        <a href="{{ route('scans.index') }}" class="btn btn-outline-secondary px-4 py-2 shadow-sm d-flex align-items-center">
                            <i class="mdi mdi-arrow-left-bold-outline me-1"></i>
                            <span class="fw-semibold">Cancel</span>
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
