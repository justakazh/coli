@extends('app.template')
@section('title', 'Edit Workflow - COLI')

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Workflow</h5>
                </div>
                <div class="card-body">
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
                    <form action="{{ route('workflows.update', $workflow->id) }}" method="post">
                        @csrf
                        <!-- Hidden Data -->
                        <input type="hidden" id="type" name="type" value="json">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">
                                                Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter workflow name" value="{{ old('name', $workflow->name) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="category" class="form-label">
                                                Category <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select form-select-sm mb-3" id="category" name="category" required>
                                                <option value="" disabled {{ old('category', $workflow->category) == '' ? 'selected' : '' }}>Select category</option>
                                                <option value="single" {{ old('category', $workflow->category) == 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="multiple" {{ old('category', $workflow->category) == 'multiple' ? 'selected' : '' }}>Multiple</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="tags" class="form-label">Tags</label>
                                            <input type="text" class="form-control" id="tags" name="tags" placeholder="Enter tags (comma separated)" value="{{ old('tags', $workflow->tags) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="script" class="form-label">JSON Workflow</label>
                                            <textarea class="form-control" id="script" name="script_workflow" rows="3" placeholder="Enter workflow script">{{ old('script_workflow', file_exists($workflow->script_path ?? $workflow->script) ? file_get_contents($workflow->script_path ?? $workflow->script) : '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <button type="submit" class="btn btn-primary">
                                            Update Workflow
                                        </button>
                                        <a href="{{ route('workflows.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection