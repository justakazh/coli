@extends('app.template')
@section('title', 'Scan Output - COLI')

@section('content')

<div class="row" style="height:100%;">
    <div class="col-12" style="height:100%;">
        <div class="card" style="height:100%;">
            <div class="card-body d-flex flex-column" style="height:100%; width:100%;">
                <div class="d-flex align-items-center mb-3">
                    <i class="mdi mdi-file" style=""></i>
                    <h3 class="card-title mb-0 ms-2" style="font-weight: 600;">Scan Output - {{ $scan->target }}</h3>
                    <div class="ms-auto d-flex gap-2">
                        <a href="{{ route('scans.index') }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('scans.review', $scan->id) }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-eye"></i> Review
                        </a>
                    </div>
                </div>
                <hr class="mb-3 mt-0">
                <div class="flex-grow-1" style="min-height:0;  overflow: hidden; ">
                    <iframe 
                        src="{{ env('APP_URL') }}/assets/vendors/file-manager/filemanager.php?p={{ str_replace(env('HUNT_PATH'), '', $scan->output_path) }}&hash={{ md5(env("FILE_MANAGER_USER").":".env("FILE_MANAGER_PASS")) }}" 
                        frameborder="0" 
                        style="width:100%; height:100%; min-height:500px; display:block; border:none;"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
