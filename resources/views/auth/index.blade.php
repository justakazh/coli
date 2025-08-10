@extends('app.template')
@section('title', 'Login - COLI')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card mt-5 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="logo" class="img-fluid mb-4 logo-mini-light" style="width: 40%;">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo" class="img-fluid mb-4 logo-mini-dark" style="width: 40%; display: none;">
                    </div>

                    {{-- Success message --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Error message --}}
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Validation errors --}}
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

                    <form method="POST" action="{{ route('auth.login') }}" autocomplete="off" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="username"
                                maxlength="255"
                                placeholder="Enter your email"
                            >
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                required 
                                minlength="6"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                            >
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="captcha" class="form-label">Captcha</label>
                            <div class="d-flex align-items-center mb-2">
                                <span id="captcha-img">{!! captcha_img('flat') !!}</span>
                                <button type="button" class="btn btn-link btn-sm ms-2" id="refresh-captcha" title="Refresh captcha">
                                    <i class="mdi mdi-refresh"></i>
                                </button>
                            </div>
                            <input 
                                type="text" 
                                class="form-control @error('captcha') is-invalid @enderror" 
                                id="captcha" 
                                name="captcha" 
                                required
                                placeholder="Enter the captcha code"
                                autocomplete="off"
                            >
                            @error('captcha')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-login-variant me-1"></i> Login
                            </button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            &copy; {{ date('Y') }} COLI. All rights reserved.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoLight = document.querySelector('.logo-mini-light');
        const logoDark = document.querySelector('.logo-mini-dark');
        function updateLogo() {
            if (localStorage.getItem('theme') === 'dark') {
                if (logoLight) logoLight.style.display = 'none';
                if (logoDark) logoDark.style.display = 'block';
            } else {
                if (logoLight) logoLight.style.display = 'block';
                if (logoDark) logoDark.style.display = 'none';
            }
        }
        updateLogo();
        // If the switcher exists on this page, listen for changes
        const themeSwitcher = document.getElementById('theme-switcher');
        if (themeSwitcher) {
            themeSwitcher.addEventListener('change', function() {
                setTimeout(updateLogo, 10);
            });
        }

        // Refresh the captcha and set the image size
        document.getElementById('refresh-captcha').addEventListener('click', function(e) {
            e.preventDefault();
            // Assume the route returns the captcha image directly (binary)
            // We will simply change the source of the captcha image
            const captchaImg = document.querySelector('#captcha-img img');
            if (captchaImg) {
                // Add a parameter to avoid caching
                captchaImg.src = "{{ route('captcha.refresh') }}?t=" + new Date().getTime();
                // Set the image size
                captchaImg.width = 160; // width in pixels
                captchaImg.height = 46; // height in pixels
                captchaImg.style.objectFit = 'contain';
            } else {
                // If it's not an <img> tag, replace the HTML with a new <img> tag with fixed size
                const captchaContainer = document.getElementById('captcha-img');
                if (captchaContainer) {
                    captchaContainer.innerHTML = '<img src="{{ route('captcha.refresh') }}?t=' + new Date().getTime() + '" alt="captcha" width="150" height="50" style="object-fit:contain;">';
                }
            }
            // Reset the captcha input field
            const captchaInput = document.getElementById('captcha');
            if (captchaInput) captchaInput.value = '';
        });
    });
</script>
@endsection