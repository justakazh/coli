@extends('app.template')
@section('title', 'Bulk Create Scan - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="mdi mdi-radar me-2"></i>
                    Create Bulk Scan
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

                <form action="{{ route('scans.bulk-store') }}" method="post" id="bulk-scan-form">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2">Workflow Selection Mode</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="workflow_mode" id="workflow_mode_same" value="same" checked>
                            <label class="form-check-label" for="workflow_mode_same">
                                Use the same workflow for every target
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="workflow_mode" id="workflow_mode_different" value="different">
                            <label class="form-check-label" for="workflow_mode_different">
                                Use a different workflow for each target
                            </label>
                        </div>
                    </div>

                    <div id="same-workflow-section" class="mb-3">
                        <label for="same_workflow" class="form-label">Select Workflow for All Targets</label>
                        <select name="same_workflow" id="same_workflow" class="form-select @error('same_workflow') is-invalid @enderror" required>
                            <option value="" disabled selected>Choose a workflow...</option>
                            @if($workflows->count() > 0)
                                @foreach($workflows->groupBy('category') as $category => $groupedWorkflows)
                                    <optgroup label="{{ ucfirst($category) }}">
                                        @foreach($groupedWorkflows as $workflow)
                                            <option value="{{ $workflow->id }}"
                                                {{ old('same_workflow') == $workflow->id ? 'selected' : '' }}>
                                                {{ ucfirst($workflow->category) }} - {{ ucfirst($workflow->name) }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @else
                                <option value="" disabled>No workflows available</option>
                            @endif
                        </select>
                        @error('same_workflow')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card mb-3" id="targets-table-section">
                        <div class="card-header py-2">
                            <h6 class="card-title mb-0">
                                <i class="mdi mdi-crosshairs-gps me-1"></i>
                                {{ $scopes->count() }} Selected Targets
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th width="120px">
                                                <i class="mdi mdi-shape-outline"></i> Type
                                            </th>
                                            <th>
                                                <i class="mdi mdi-target-variant"></i> Target
                                            </th>
                                            <th id="workflow-column-header">
                                                <i class="mdi mdi-cog-outline"></i> Workflow
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($scopes as $scope)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ ucfirst($scope->type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="mdi mdi-file-document-outline me-1"></i>
                                                    {{ $scope->target }}
                                                    <input type="hidden" name="scope_ids[]" value="{{ $scope->id }}">
                                                </td>
                                                <td class="workflow-select-cell">
                                                    <div class="same-workflow-placeholder" style="display:;">
                                                        <span class="text-muted">Will use the selected workflow above</span>
                                                    </div>
                                                    <select name="workflows[{{ $scope->id }}]" class="form-select @error('workflows.' . $scope->id) is-invalid @enderror workflow-per-target-select" required style="display:none;">
                                                        <option value="" disabled selected>Choose a workflow...</option>
                                                        @if($workflows->count() > 0)
                                                            @foreach($workflows->groupBy('category') as $category => $groupedWorkflows)
                                                                <optgroup label="{{ ucfirst($category) }}">
                                                                    @foreach($groupedWorkflows as $workflow)
                                                                        <option value="{{ $workflow->id }}"
                                                                            {{ old('workflows.' . $scope->id) == $workflow->id ? 'selected' : '' }}>
                                                                            {{ ucfirst($workflow->category) }} - {{ ucfirst($workflow->name) }}
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        @else
                                                            <option value="" disabled>No workflows available</option>
                                                        @endif
                                                    </select>
                                                    @error('workflows.' . $scope->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    <i class="mdi mdi-alert-outline me-1"></i>
                                                    No targets selected.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-text text-muted mb-3">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Please select a workflow for each target or use the same workflow for all. Workflows are grouped by category for easier navigation.
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-end gap-3 mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center">
                            <i class="mdi mdi-plus-circle-outline me-2"></i>
                            <span class="fw-semibold">Create Scans</span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateWorkflowMode() {
        var mode = document.querySelector('input[name="workflow_mode"]:checked').value;
        var sameSection = document.getElementById('same-workflow-section');
        var workflowCells = document.querySelectorAll('.workflow-select-cell .workflow-per-target-select');
        var samePlaceholders = document.querySelectorAll('.workflow-select-cell .same-workflow-placeholder');
        var sameWorkflowSelect = document.getElementById('same_workflow');
        if (mode === 'same') {
            sameSection.style.display = '';
            sameWorkflowSelect.required = true;
            workflowCells.forEach(function(sel) {
                sel.style.display = 'none';
                sel.required = false;
            });
            samePlaceholders.forEach(function(ph) {
                ph.style.display = '';
            });
        } else {
            sameSection.style.display = 'none';
            sameWorkflowSelect.required = false;
            workflowCells.forEach(function(sel) {
                sel.style.display = '';
                sel.required = true;
            });
            samePlaceholders.forEach(function(ph) {
                ph.style.display = 'none';
            });
        }
    }

    // Initial state
    updateWorkflowMode();

    // Listen for radio changes
    document.getElementById('workflow_mode_same').addEventListener('change', updateWorkflowMode);
    document.getElementById('workflow_mode_different').addEventListener('change', updateWorkflowMode);

    // On submit, remove unused workflow fields
    document.getElementById('bulk-scan-form').addEventListener('submit', function(e) {
        var mode = document.querySelector('input[name="workflow_mode"]:checked').value;
        var sameWorkflowSelect = document.getElementById('same_workflow');
        if (mode === 'same') {
            // Remove all per-target workflow selects so only same_workflow is submitted
            document.querySelectorAll('.workflow-per-target-select').forEach(function(sel) {
                sel.disabled = true;
            });
            sameWorkflowSelect.required = true;
        } else {
            // Remove the same_workflow select so only per-target workflows are submitted
            sameWorkflowSelect.disabled = true;
            sameWorkflowSelect.required = false;
        }
    });
});
</script>

@endsection
