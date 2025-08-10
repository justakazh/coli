@extends('app.template')
@section('title', 'Review Result - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="mdi mdi-radar me-2"></i>
                    Review Result
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
                <div class="alert alert-info mb-3" role="alert">
                                <i class="mdi mdi-information-outline me-1"></i>
                                To perform a review, the selected file must be in <strong>CSV</strong> format.
                                If you do not have a CSV file yet, you can create one by adding a new workflow task.
                            </div>

                <form action="{{ route('scans.review-result') }}" method="GET">

                    <div class="card mb-3">
                        <div class="card-header py-2">
                            <h6 class="card-title mb-0">
                                <i class="mdi mdi-crosshairs-gps me-1"></i>
                                Select CSV
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
                                                <i class="mdi mdi-target-variant"></i> Size
                                            </th>
                                            <th>
                                                <i class="mdi mdi-target-variant"></i> Target
                                            </th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($files as $file)
                                            @php
                                                $relativeFile = str_replace(env('HUNT_PATH'), '', $file['path']);
                                                $fileSize = number_format($file['size'] / 1024, 2) . ' KB';
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    <input type="radio" name="output" class="form-check-input" value="{{ $relativeFile }}"
                                                        {{ old('output', '') == $relativeFile ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        Output
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $fileSize }}
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

                    <div class="d-flex flex-wrap align-items-center justify-content-end gap-3 mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center">
                            <i class="mdi mdi-eye-check-outline me-2"></i>
                            <span class="fw-semibold">Review</span>
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
