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
    <link rel="stylesheet" href="{{ url('') }}/assets/vendors/sweetalert2/sweetalert2.min.css">

    <!-- color-modes:js -->
    <script src="{{ url('') }}/assets/js/color-modes.js"></script>
    <!-- endinject -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- core:css -->
    <link rel="stylesheet" href="{{ url('assets/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('') }}/assets/vendors/core/core.css">
    <!-- endinject -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('') }}/assets/fonts/feather-font/css/iconfont.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/demo2/style.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
    <!-- endinject -->

    <script src="{{ url('') }}/assets/vendors/jquery/jquery.min.js"></script>
    <script src="{{ url('') }}/assets/vendors/select2/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/drawflow/dist/drawflow.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/drawflow/dist/drawflow.min.js"></script>
    <style>
    html, body {
        overflow: hidden !important;
        height: 100%;
    }
    #drawflow {
        height: 100%;
        min-height: 100vh;
        position: fixed;
        width: 100vw;
        height: 100vh;
        z-index: 10;
        border: none;
        border-radius: 0;
        background-color: transparent !important;
        background-image: radial-gradient(circle, #bdbdbd 1px, transparent 1px), radial-gradient(circle, #bdbdbd 1px, transparent 1px) !important;
        background-size: 20px 20px !important;
        background-position: 0 0, 0 0 !important;
        overflow: hidden !important;
    }

    #drawflow_data{
        display: none;
    }

    /* Add Task Button */
    #btn-insert-task {
        position: fixed;
        right: 24px;
        bottom: 120px;
        z-index: 100;
        cursor: pointer;
    }
    /* Lock Drawflow Button */
    #btn-lock-drawflow {
        position: fixed;
        right: 24px;
        bottom: 72px;
        z-index: 100;
        cursor: pointer;
    }
    /* Help Drawflow Button */
    #btn-help-drawflow {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 100;
        cursor: pointer;
    }

    .node-action-btns {
        display: flex;
        gap: 4px;
        margin-bottom: 4px;
        justify-content: flex-end;
    }
    .node-action-btns button {
        border: none;
        background: transparent;
        padding: 0 2px;
        color: #333;
        font-size: 1rem;
        cursor: pointer;
    }
    .node-action-btns button:hover {
        color: #0d6efd;
    }

    :root {
  --dfBackgroundColor: #ffffff;
  --dfBackgroundSize: 0px;
  --dfBackgroundImage: none;

  --dfNodeType: flex;
  --dfNodeTypeFloat: none;
  --dfNodeBackgroundColor: #ffffff;
  --dfNodeTextColor: #000000;
  --dfNodeBorderSize: 2px;
  --dfNodeBorderColor: rgba(86, 114, 222, 1);
  --dfNodeBorderRadius: 4px;
  --dfNodeMinHeight: 40px;
  --dfNodeMinWidth: 160px;
  --dfNodePaddingTop: 15px;
  --dfNodePaddingBottom: 15px;
  --dfNodeBoxShadowHL: 0px;
  --dfNodeBoxShadowVL: 2px;
  --dfNodeBoxShadowBR: 15px;
  --dfNodeBoxShadowS: 2px;
  --dfNodeBoxShadowColor: rgba(86, 114, 222, 1);

  --dfNodeHoverBackgroundColor: #ffffff;
  --dfNodeHoverTextColor: #000000;
  --dfNodeHoverBorderSize: 2px;
  --dfNodeHoverBorderColor: rgba(127, 147, 224, 1);
  --dfNodeHoverBorderRadius: 4px;

  --dfNodeHoverBoxShadowHL: 0px;
  --dfNodeHoverBoxShadowVL: 2px;
  --dfNodeHoverBoxShadowBR: 15px;
  --dfNodeHoverBoxShadowS: 2px;
  --dfNodeHoverBoxShadowColor: rgba(127, 147, 224, 1);

  --dfNodeSelectedBackgroundColor: #6571FF;
  --dfNodeSelectedTextColor: #ffffff;
  --dfNodeSelectedBorderSize: 2px;
  --dfNodeSelectedBorderColor: #0C1427;
  --dfNodeSelectedBorderRadius: 4px;

  --dfNodeSelectedBoxShadowHL: 0px;
  --dfNodeSelectedBoxShadowVL: 2px;
  --dfNodeSelectedBoxShadowBR: 15px;
  --dfNodeSelectedBoxShadowS: 2px;
  --dfNodeSelectedBoxShadowColor: rgba(86, 114, 222, 1);

  --dfInputBackgroundColor: #ffffff;
  --dfInputBorderSize: 2px;
  --dfInputBorderColor: #000000;
  --dfInputBorderRadius: 50px;
  --dfInputLeft: -27px;
  --dfInputHeight: 20px;
  --dfInputWidth: 20px;

  --dfInputHoverBackgroundColor: #ffffff;
  --dfInputHoverBorderSize: 2px;
  --dfInputHoverBorderColor: #000000;
  --dfInputHoverBorderRadius: 50px;

  --dfOutputBackgroundColor: #ffffff;
  --dfOutputBorderSize: 2px;
  --dfOutputBorderColor: #000000;
  --dfOutputBorderRadius: 50px;
  --dfOutputRight: -3px;
  --dfOutputHeight: 20px;
  --dfOutputWidth: 20px;

  --dfOutputHoverBackgroundColor: #ffffff;
  --dfOutputHoverBorderSize: 2px;
  --dfOutputHoverBorderColor: #000000;
  --dfOutputHoverBorderRadius: 50px;

  --dfLineWidth: 5px;
  --dfLineColor: rgba(148, 154, 237, 1);
  --dfLineHoverColor: rgba(113, 120, 254, 1);
  --dfLineSelectedColor: rgba(113, 120, 254, 1);

  --dfRerouteBorderWidth: 2px;
  --dfRerouteBorderColor: #000000;
  --dfRerouteBackgroundColor: #ffffff;

  --dfRerouteHoverBorderWidth: 2px;
  --dfRerouteHoverBorderColor: #000000;
  --dfRerouteHoverBackgroundColor: #ffffff;

  --dfDeleteDisplay: block;
  --dfDeleteColor: #ffffff;
  --dfDeleteBackgroundColor: #000000;
  --dfDeleteBorderSize: 2px;
  --dfDeleteBorderColor: #ffffff;
  --dfDeleteBorderRadius: 50px;
  --dfDeleteTop: -15px;

  --dfDeleteHoverColor: #000000;
  --dfDeleteHoverBackgroundColor: #ffffff;
  --dfDeleteHoverBorderSize: 2px;
  --dfDeleteHoverBorderColor: #000000;
  --dfDeleteHoverBorderRadius: 50px;

}

#drawflow {
  background: var(--dfBackgroundColor);
  background-size: var(--dfBackgroundSize) var(--dfBackgroundSize);
  background-image: var(--dfBackgroundImage);
}

.drawflow .drawflow-node {
  display: var(--dfNodeType);
  background: var(--dfNodeBackgroundColor);
  color: var(--dfNodeTextColor);
  border: var(--dfNodeBorderSize)  solid var(--dfNodeBorderColor);
  border-radius: var(--dfNodeBorderRadius);
  min-height: var(--dfNodeMinHeight);
  width: auto;
  min-width: var(--dfNodeMinWidth);
  padding-top: var(--dfNodePaddingTop);
  padding-bottom: var(--dfNodePaddingBottom);
  -webkit-box-shadow: var(--dfNodeBoxShadowHL) var(--dfNodeBoxShadowVL) var(--dfNodeBoxShadowBR) var(--dfNodeBoxShadowS) var(--dfNodeBoxShadowColor);
  box-shadow:  var(--dfNodeBoxShadowHL) var(--dfNodeBoxShadowVL) var(--dfNodeBoxShadowBR) var(--dfNodeBoxShadowS) var(--dfNodeBoxShadowColor);
}

.drawflow .drawflow-node:hover {
  background: var(--dfNodeHoverBackgroundColor);
  color: var(--dfNodeHoverTextColor);
  border: var(--dfNodeHoverBorderSize)  solid var(--dfNodeHoverBorderColor);
  border-radius: var(--dfNodeHoverBorderRadius);
  -webkit-box-shadow: var(--dfNodeHoverBoxShadowHL) var(--dfNodeHoverBoxShadowVL) var(--dfNodeHoverBoxShadowBR) var(--dfNodeHoverBoxShadowS) var(--dfNodeHoverBoxShadowColor);
  box-shadow:  var(--dfNodeHoverBoxShadowHL) var(--dfNodeHoverBoxShadowVL) var(--dfNodeHoverBoxShadowBR) var(--dfNodeHoverBoxShadowS) var(--dfNodeHoverBoxShadowColor);
}

.drawflow .drawflow-node.selected {
  background: var(--dfNodeSelectedBackgroundColor);
  color: var(--dfNodeSelectedTextColor);
  border: var(--dfNodeSelectedBorderSize)  solid var(--dfNodeSelectedBorderColor);
  border-radius: var(--dfNodeSelectedBorderRadius);
  -webkit-box-shadow: var(--dfNodeSelectedBoxShadowHL) var(--dfNodeSelectedBoxShadowVL) var(--dfNodeSelectedBoxShadowBR) var(--dfNodeSelectedBoxShadowS) var(--dfNodeSelectedBoxShadowColor);
  box-shadow:  var(--dfNodeSelectedBoxShadowHL) var(--dfNodeSelectedBoxShadowVL) var(--dfNodeSelectedBoxShadowBR) var(--dfNodeSelectedBoxShadowS) var(--dfNodeSelectedBoxShadowColor);
}

.drawflow .drawflow-node .input {
  left: var(--dfInputLeft);
  background: var(--dfInputBackgroundColor);
  border: var(--dfInputBorderSize)  solid var(--dfInputBorderColor);
  border-radius: var(--dfInputBorderRadius);
  height: var(--dfInputHeight);
  width: var(--dfInputWidth);
}

.drawflow .drawflow-node .input:hover {
  background: var(--dfInputHoverBackgroundColor);
  border: var(--dfInputHoverBorderSize)  solid var(--dfInputHoverBorderColor);
  border-radius: var(--dfInputHoverBorderRadius);
}

.drawflow .drawflow-node .outputs {
  float: var(--dfNodeTypeFloat);
}

.drawflow .drawflow-node .output {
  right: var(--dfOutputRight);
  background: var(--dfOutputBackgroundColor);
  border: var(--dfOutputBorderSize)  solid var(--dfOutputBorderColor);
  border-radius: var(--dfOutputBorderRadius);
  height: var(--dfOutputHeight);
  width: var(--dfOutputWidth);
}

.drawflow .drawflow-node .output:hover {
  background: var(--dfOutputHoverBackgroundColor);
  border: var(--dfOutputHoverBorderSize)  solid var(--dfOutputHoverBorderColor);
  border-radius: var(--dfOutputHoverBorderRadius);
}

.drawflow .connection .main-path {
  stroke-width: var(--dfLineWidth);
  stroke: var(--dfLineColor);
}

.drawflow .connection .main-path:hover {
  stroke: var(--dfLineHoverColor);
}

.drawflow .connection .main-path.selected {
  stroke: var(--dfLineSelectedColor);
}

.drawflow .connection .point {
  stroke: var(--dfRerouteBorderColor);
  stroke-width: var(--dfRerouteBorderWidth);
  fill: var(--dfRerouteBackgroundColor);
}

.drawflow .connection .point:hover {
  stroke: var(--dfRerouteHoverBorderColor);
  stroke-width: var(--dfRerouteHoverBorderWidth);
  fill: var(--dfRerouteHoverBackgroundColor);
}

.drawflow-delete {
  display: var(--dfDeleteDisplay);
  color: var(--dfDeleteColor);
  background: var(--dfDeleteBackgroundColor);
  border: var(--dfDeleteBorderSize) solid var(--dfDeleteBorderColor);
  border-radius: var(--dfDeleteBorderRadius);
}

.parent-node .drawflow-delete {
  top: var(--dfDeleteTop);
}

.drawflow-delete:hover {
  color: var(--dfDeleteHoverColor);
  background: var(--dfDeleteHoverBackgroundColor);
  border: var(--dfDeleteHoverBorderSize) solid var(--dfDeleteHoverBorderColor);
  border-radius: var(--dfDeleteHoverBorderRadius);
}













/* Ubah warna dasar icon */
.btn-edit-task i,
.btn-delete-task i {
    color: #555; /* Warna default icon */
    transition: color 0.2s, transform 0.2s;
}

/* Hover efek: ubah warna & scale */
.btn-edit-task:hover i {
    color: #1976d2; /* Biru */
    transform: scale(1.2);
}

.btn-delete-task:hover i {
    color: #d32f2f; /* Merah */
    transform: scale(1.2);
}

/* Tambahan efek untuk tombol */
/* .node-action-btns button {
    background: transparent;
    border: none;
    padding: 5px;
    cursor: pointer;
} */

/* Optional: buat background tombol lebih halus saat hover */
/* .node-action-btns button:hover {
    background-color: rgba(0,0,0,0.05);
    border-radius: 4px; */
/* } */


.drawflow-node.selected .btn-edit-task:hover i {
    color: #1976d2; /* Biru */
    transform: scale(1.2);
}

.drawflow-node.selected .btn-delete-task:hover i {
    color: #d32f2f; /* Merah */
    transform: scale(1.2);
}

.drawflow-node.selected .btn-edit-task i {
    color: #ffffff; /* Biru lebih terang */
}

.drawflow-node.selected .btn-delete-task i {
    color: #ffffff; /* Merah terang */
}

/* Optional: animasi kecil biar lebih responsif */
.drawflow-node.selected .btn-edit-task i,
.drawflow-node.selected .btn-delete-task i {
    transform: scale(1.1);
}
</style>

    <link rel="shortcut icon" href="{{ url('') }}/assets/images/favicon.png" />
</head>
<body>
    <div class="main-wrapper">
            <!-- partial:../../partials/_navbar.html -->
            <div class="horizontal-menu">
                <nav class="navbar top-navbar">
                    <div class="container-fluid">
                        <div class="navbar-content">
                            <a href="#" class="navbar-brand d-none d-lg-flex">
                            <img src="https://isx.pw/assets/images/logo-light.png" class="logo-mini logo-mini-light" alt="logo" style="width: 70px !important;">
                            <img src="https://isx.pw/assets/images/logo-dark.png" class="logo-mini logo-mini-dark" alt="logo" style="width: 70px !important; display: none;">
                            </a>
                            <!-- Logo-mini for small screen devices (mobile/tablet) -->
                            <div class="logo-mini-wrapper">
                                <img src="https://isx.pw/assets/images/logo-light.png" class="logo-mini logo-mini-light" alt="logo" style="width: 50% !important;">
                                <img src="https://isx.pw/assets/images/logo-dark.png" class="logo-mini logo-mini-dark" alt="logo" style="width: 50% !important;">
                            </div>
                            
                            <ul class="navbar-nav">
                                <li class="theme-switcher-wrapper nav-item">
                                    <input type="checkbox" value="" id="theme-switcher">
                                    <label for="theme-switcher">
                                        <div class="box">
                                            <div class="ball"></div>
                                            <div class="icons">
                                                <i data-lucide="sun"></i>
                                                <i data-lucide="moon"></i>
                                            </div>
                                        </div>
                                    </label>
                                </li>
                                
                                
                                
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img class="w-30px h-30px ms-1 rounded-circle" src="{{asset('assets/images/co.png')}}" alt="profile">
                                    </a>
                                    <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                                        <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                                            <div class="mb-3">
                                                <img class="w-80px h-80px rounded-circle" src="{{asset('assets/images/co.png')}}" alt="">
                                            </div>
                                            <div class="text-center">
                                                <p class="fs-16px fw-bolder">{{ Auth::user()->name ?? 'Guest' }}</p>
                                                <p class="fs-12px text-secondary">{{ Auth::user()->email ?? 'guest@autohunt.com' }}</p>
                                            </div>
                                        </div>
                                        <ul class="list-unstyled p-1">
                                            @auth
                                                <li class="dropdown-item py-2">
                                                    <a href="{{ route('change-profile') }}" class="text-body ms-0" style="background: none; border: none; padding: 0;">
                                                        <i class="me-2 icon-md" data-feather="user"></i>
                                                        <span>Change Profile</span>
                                                    </a>
                                                </li>
                                                <li class="dropdown-item py-2">
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="text-body ms-0" style="background: none; border: none; padding: 0;">
                                                            <i class="me-2 icon-md" data-feather="log-out"></i>
                                                            <span>Log Out</span>
                                                        </button>
                                                    </form>
                                                </li>
                                                @else
                                                <li class="dropdown-item py-2">
                                                    <a href="{{ route('login') }}" class="text-body ms-0" style="background: none; border: none; padding: 0;">
                                                        <i class="me-2 icon-md" data-feather="log-in"></i>
                                                        <span>Login</span>
                                                    </a>
                                                </li>
                                            @endauth
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                            <!-- navbar toggler for small devices -->
                            <div data-toggle="horizontal-menu-toggle" class="navbar-toggler navbar-toggler-right d-lg-none align-self-center">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </nav>
                    <div class="container-fluid bottom-navbar">
                        <ul class="nav nav-tabs nav-tabs-line justify-content-center" id="lineTab" role="tablist">
                            
                            <li class="nav-item">
                                <a class="nav-link active" id="workflow-line-tab" data-bs-toggle="tab" href="#workflow-designer" role="tab" aria-controls="workflow-designer" aria-selected="true">
                                <i class="link-icon" data-feather="shuffle"></i>    
                                Workflow Designer</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="workflow-information-line-tab" data-bs-toggle="tab" href="#workflow-information" role="tab" aria-controls="workflow-information" aria-selected="false">
                                <i class="link-icon" data-feather="info"></i>    
                                Workflow Information</a>
                            </li>
                        </ul>
                    </div>
            </div>
            <!-- partial -->
            @yield('content')



    </div>

    <!-- core:js -->
    <script src="{{ url('') }}/assets/vendors/core/core.js"></script>
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
    <script src="{{ url('') }}/assets/vendors/feather-icons/feather.min.js"></script>
    <script src="{{ url('') }}/assets/js/app.js"></script>
    <script src="{{ url('') }}/assets/vendors/sweetalert2/sweetalert2.min.js"></script>
    <!-- endinject -->
</body>
</html>