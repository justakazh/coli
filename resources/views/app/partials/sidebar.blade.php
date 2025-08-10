<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('assets/images/logo-light.png') }}" class="logo-mini logo-mini-light" alt="logo" style="width: 70px !important;">
            <img src="{{ asset('assets/images/logo-dark.png') }}" class="logo-mini logo-mini-dark" alt="logo" style="width: 70px !important; display: none;">
        </a>
        
        <div class="sidebar-toggler">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav" id="sidebarNav">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
                <a href="{{ url('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="layout"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>   
            <li class="nav-item nav-category">Management</li>
            <li class="nav-item">
                <a href="{{ url('scopes') }}" class="nav-link {{ request()->is('scopes') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="crosshair"></i>
                    <span class="link-title">Scopes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('scans') }}" class="nav-link {{ request()->is('scans') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="activity"></i>
                    <span class="link-title">Scans</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('workflows') }}" class="nav-link {{ request()->is('workflows') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="shuffle"></i>
                    <span class="link-title">Workflows</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('terminal') }}" class="nav-link {{ request()->is('terminal') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="terminal"></i>
                    <span class="link-title">Terminal</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('file-manager') }}" class="nav-link {{ request()->is('file-manager') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="folder"></i>
                    <span class="link-title">File Manager</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
<!-- End of Sidebar Navigation -->