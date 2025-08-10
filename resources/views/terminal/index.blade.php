@extends('app.template')
@section('title', 'Terminal - COLI')
@section('content')

<div class="row" style="height:100%;">
    <div class="col-12" style="height:100%;">
        <div class="card" style="height:100%;">
            <div class="card-body" style="height:100%; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">
                        <i class="mdi mdi-file"></i> Terminal
                    </h3>
                    
                        @if($terminal->state == "stopped")
                        <form action="{{ route('terminal.start') }}" method="POST">
                        @csrf
                            <button type="submit" class="btn btn-success" style="min-width: 160px;">
                                Start Session
                            </button>
                        </form>
                        @else
                        <form action="{{ route('terminal.stop') }}" method="POST">
                        @csrf
                            <button type="submit" class="btn btn-danger" style="min-width: 160px;">
                                Stop Session
                            </button>
                        </form>
                        @endif
                </div>
                <br>
                <div style="flex:1 1 0; min-height:0; overflow: hidden;">
                    @if($terminal->state == "stopped")
                        <div class="alert alert-danger" style=" display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-alert"></i> Terminal is off
                        </div>
                    @else
                    <div style="width:100%; height:100%; min-height:400px; display:block; overflow:hidden;">
                        <iframe 
                            src="{{ env('APP_URL') }}/terminal/frame" 
                            frameborder="0" 
                            style="width:100%; height:100%; min-height:400px; display:block; border:0; overflow:hidden;"
                            allowfullscreen>
                        </iframe>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
