<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="COLI - Chain Of Logic Intelligence">
    <meta name="author" content="coli">
    <meta name="keywords" content="coli, admin, dashboard, automation, security, responsive, cyber, threat, detection, template, workflow, cluster, task">

    <title>@yield('title', 'COLI - Automation')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{url('')}}/assets/vendors/sweetalert2/sweetalert2.min.css">

    <!-- color-modes:js -->
    <script src="{{url('')}}/assets/js/color-modes.js"></script>
    <!-- endinject -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- core:css -->
    <link rel="stylesheet" href="{{url('assets/vendors/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{url('')}}/assets/vendors/core/core.css">
    <!-- endinject -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{url('')}}/assets/fonts/feather-font/css/iconfont.css">
    <link rel="stylesheet" href="{{url('')}}/assets/css/demo1/style.css">
    <link rel="stylesheet" href="{{url('')}}/assets/vendors/mdi/css/materialdesignicons.min.css">
    
    <!-- endinject -->

    <script src="{{url('')}}/assets/vendors/jquery/jquery.min.js"></script>
    <script src="{{url('')}}/assets/vendors/select2/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.workflow-select2').select2();
        });
    </script>
    
    


    <link rel="shortcut icon" href="{{url('')}}/assets/images/favicon.png" />
</head>
<body>
    <div class="main-wrapper">

        @if(Auth::check())
            
        @include('app.partials.sidebar')

        <div class="page-wrapper">

                @include('app.partials.navbar')

            <div class="page-content">
                @yield('content')
            </div>

            <footer class="footer d-flex flex-row align-items-center justify-content-between px-4 py-3 border-top small">
                <p class="text-secondary mb-1 mb-md-0"><a href="https://github.com/justakazh/coli" target="_blank">COLI</a> - Command Orchestration & Logic Interface.</p>
            </footer>

        </div>
        @else
            @yield('content')
        @endif

    </div>

    <!-- core:js -->
    <script src="{{url('')}}/assets/vendors/core/core.js"></script>
    <!-- endinject -->


    <!-- Logo switcher -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeSwitcher = document.getElementById('theme-switcher');
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

            if (themeSwitcher) {
                themeSwitcher.addEventListener('change', function() {
                    setTimeout(updateLogo, 10); 
                });
            }
        });
    </script>
    <!-- End logo switcher -->

    <!-- inject:js -->
    <script src="{{url('')}}/assets/vendors/feather-icons/feather.min.js"></script>
    <script src="{{url('')}}/assets/js/app.js"></script>
    <script src="{{url('')}}/assets/vendors/sweetalert2/sweetalert2.min.js"></script>
    <!-- endinject -->

</body>
</html>