@extends('app.template')
@section('title', 'Create Task - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="card-title mb-0">
                        <i class="mdi mdi-checkbox-marked-outline me-2"></i>
                        Create New Task
                    </h3>
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

                <form action="{{ route('tasks.store') }}" method="post" class="mt-4">
                    @csrf
                    <div class="row g-4">

                        <!-- Name Field -->
                        <div class="col-md-12">
                            <label for="name" class="form-label">Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-format-title"></i>
                                </span>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter task name..." value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-text-box-outline"></i>
                                </span>
                                <textarea class="form-control" id="description" name="description" placeholder="Enter task description..." rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Result Field -->
                        <div class="col-md-12">
                            <label for="result" class="form-label">Result File Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-file-document-outline"></i>
                                </span>
                                <input type="text" class="form-control" id="result" name="result" placeholder="Enter result file name..." value="{{ old('result') }}" required>
                            </div>
                        </div>

                        <!-- From Target Command Field -->
                        <div class="col-md-12">
                            <label for="from_target_command" class="form-label">Target Command</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-console"></i>
                                </span>
                                <textarea class="form-control" id="from_target_command" name="from_target_command" placeholder="Enter target command..." rows="3" required>{{ old('from_target_command') }}</textarea>
                            </div>
                        </div>

                        <!-- From Parent Result Command Field -->
                        <div class="col-md-12">
                            <label for="from_parent_result_command" class="form-label">Parent Result Command</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-console-network"></i>
                                </span>
                                <textarea class="form-control" id="from_parent_result_command" name="from_parent_result_command" placeholder="Enter parent result command..." rows="3" required>{{ old('from_parent_result_command') }}</textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 mt-4 d-flex align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i>
                                Create Task
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-light ms-2">
                                <i class="mdi mdi-arrow-left-bold-outline me-1"></i>
                                Back to List
                            </a>
                        </div>

                       
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
