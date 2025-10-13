@extends('templates.v1')
@section('content')
@section('title', 'Workflows - Edit Script')
<div class="container-fluid" >
    <div class="card">
        <div class="card-body">
            {{-- Error Handling --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('workflows.update.script', $workflow->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="script">
                <div class="mb-3">
                    <label for="workflow_name" class="form-label fw-bold">Workflow Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="workflow_name" name="name" required placeholder="Enter workflow name" value="{{ old('name', $workflow->name ?? '') }}">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="workflow_description" class="form-label fw-bold">Workflow Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="workflow_description" name="description" rows="4" placeholder="Enter workflow description">{{ old('description', $workflow->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="workflow_script" class="form-label fw-bold">Replace Workflow Script (Yaml / Json)</label>
                    <input type="file" class="form-control @error('script') is-invalid @enderror" id="workflow_script" name="script" accept=".yaml,.json">
                    @error('script')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary"> <i class="fas fa-save me-2"></i> Update Workflow</button>
                <a href="{{ route('workflows') }}" class="btn btn-secondary ms-2"><i class="fas fa-times me-2"></i> Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
