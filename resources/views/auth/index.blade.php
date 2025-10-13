@extends('templates.v1')
@section('content')
@section('title', 'Login')


<div class="d-flex justify-content-center align-items-center min-vh-100 ">
    <div class="card shadow-lg border-0" style="max-width: 26rem; width:100%;">
        <div class="card-body p-5">
            <div class="d-flex flex-column align-items-center mb-4">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 150px; height: auto;">
            </div>
            
            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Display Session Error (for e.g., authentication failed) --}}
            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        autofocus
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="you@email.com"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Password"
                    >
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
