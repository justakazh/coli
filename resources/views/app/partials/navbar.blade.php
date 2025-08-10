<nav class="navbar">
    <div class="navbar-content">

        <div class="logo-mini-wrapper">
            <img src="{{ asset('assets/images/logo-light.png') }}" class="logo-mini logo-mini-light" alt="logo" style="width: 50% !important;">
            <img src="{{ asset('assets/images/logo-dark.png') }}" class="logo-mini logo-mini-dark" alt="logo" style="width: 50% !important;">
        </div>

        <ul class="navbar-nav">
            <li class="theme-switcher-wrapper nav-item">
                <input type="checkbox" value="" id="theme-switcher" {{ session('theme', 'light') == 'dark' ? 'checked' : '' }}>
                <label for="theme-switcher">
                    <div class="box">
                        <div class="ball"></div>
                        <div class="icons">
                            <i class="feather icon-sun"></i>
                            <i class="feather icon-moon"></i>
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

        <a href="#" class="sidebar-toggler">
            <i data-feather="menu"></i>
        </a>

    </div>
</nav>