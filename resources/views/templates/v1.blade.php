<!doctype html >
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | COLI</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('assets/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ url('assets/img/site.webmanifest') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url('assets/img/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ url('assets/img/android-chrome-512x512.png') }}">
    <link rel="shortcut icon" href="{{ url('assets/img/favicon.ico') }}">
    <meta name="msapplication-TileColor" content="#212529">
    <meta name="theme-color" content="#212529">
    <link rel="stylesheet" href="{{ url('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/codemirror.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/dracula.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/drawflow.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/xterm.css') }}">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ url('assets/css/v1.css') }}">
@stack('styles')
</head>
  <body>
@if(auth()->check())
<div class="container-fluid mt-4">
    @include('templates.partial-v1.navbar')
</div>
@endif
    
@yield('content')




    <script src="{{ url('assets/js/sweetalert.min.js') }}"></script>
    <script src="{{ url('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ url('assets/js/apexcharts.min.js') }}"></script>
    <script src="{{ url('assets/js/mermaid.min.js') }}"></script>
    <script src="{{ url('assets/js/codemirror.js') }}"></script>
    <script src="{{ url('assets/js/shell.js') }}"></script>
    <script src="{{ url('assets/js/drawflow.min.js') }}"></script>
    <script src="{{ url('assets/js/xterm.min.js') }}"></script>
    <script src="{{ url('assets/js/xterm-addon-fit.js') }}"></script>
    <script src="{{ url('assets/js/xterm-addon-attach.min.js') }}"></script>
    <script src="{{ url('assets/js/socket.io.min.js') }}"></script>

    @stack('scripts')

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>