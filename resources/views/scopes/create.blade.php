@extends('app.template')
@section('title', 'Create Scope - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="card-title mb-0">
                        <i class="mdi mdi-target me-2"></i>
                        Create New Scope
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

                <form action="{{ route('scopes.store') }}" method="post" class="mt-4">
                    @csrf
                    <div class="row g-4">

                        <!-- Type Field -->
                        <div class="col-md-12">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="domain" {{ old('type') == 'domain' ? 'selected' : '' }}>Domain</option>
                                <option value="ip" {{ old('type') == 'ip' ? 'selected' : '' }}>IP</option>
                                <option value="cidr" {{ old('type') == 'cidr' ? 'selected' : '' }}>CIDR</option>
                                <option value="url" {{ old('type') == 'url' ? 'selected' : '' }}>URL</option>
                                <option value="mobile_app" {{ old('type') == 'mobile_app' ? 'selected' : '' }}>Mobile App</option>
                                <option value="others" {{ old('type') == 'others' ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>

                        <!-- Target Field -->
                        <div class="col-md-12">
                            <label for="target" class="form-label">
                                Target
                                <a href="#" data-bs-toggle="modal" data-bs-target="#targetTipsModal" class="ms-1" title="Target tips">
                                    <i class="mdi mdi-information-outline"></i>
                                </a>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-crosshairs-gps"></i>
                                </span>
                                <textarea class="form-control" id="target" name="target" placeholder="Enter target..." rows="3" required>{{ old('target') }}</textarea>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-text-box-outline"></i>
                                </span>
                                <textarea class="form-control" id="description" name="description" placeholder="Enter description..." rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Contact Field -->
                        <div class="col-md-12">
                            <label for="contact" class="form-label">Contact Information</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-account-box-multiple"></i>
                                </span>
                                <textarea class="form-control" id="contact" name="contact" rows="3" placeholder="Enter contact details...">{{ old('contact') }}</textarea>
                            </div>
                            <div class="form-text text-muted">
                                Optional: Add contact information for this scope
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 mt-4 d-flex align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i>
                                Create Scope
                            </button>
                            <a href="{{ route('scopes.index') }}" class="btn btn-light ms-2">
                                <i class="mdi mdi-arrow-left-bold-outline me-1"></i>
                                Back to List
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Modal for target tips -->
                <div class="modal fade" id="targetTipsModal" tabindex="-1" aria-labelledby="targetTipsModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="targetTipsModalLabel">Tips for the "Target" field</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <ul>
                                    <li>Enter a domain, IP address, CIDR, URL, or mobile app path according to the selected type.</li>
                                    <li>Press Enter to add multiple targets. Each entry will be saved as a separate scope item.</li>
                                    <li>Examples:
                                        <ul>
                                            <li><strong>Domain:</strong> example.com</li>
                                            <li><strong>IP:</strong> 192.168.1.1</li>
                                            <li><strong>CIDR:</strong> 192.168.1.0/24</li>
                                            <li><strong>URL:</strong> https://example.com/path</li>
                                            <li><strong>Mobile App:</strong> [HUNT_AREA]/path/to/app.apk</li>
                                        </ul>
                                    </li>
                                    <li>Make sure the target is properly formatted to avoid errors when creating the scope.</li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->

            </div>
        </div>
    </div>
</div>

@endsection
