<nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded shadow mb-4 px-2 py-2 ">
        <div class="container-fluid d-flex align-items-center ">
            <!-- Brand -->
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{url('/')}}">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 80px; height: auto;">
            </a>
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav w-100 justify-content-lg-center gap-2 gap-lg-4 mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-grip" style="color: var(--bs-primary);"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold d-flex align-items-center gap-2 {{ request()->routeIs('scans') ? 'active' : '' }}" href="{{ route('scans') }}">
                            <i class="fa-solid fa-microchip" style="color: var(--bs-primary);"></i>
                            <span>Scans</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold d-flex align-items-center gap-2 {{ request()->routeIs('workflows') ? 'active' : '' }}" href="{{ route('workflows') }}">
                            <i class="fa-solid fa-chart-diagram" style="color: var(--bs-primary);"></i>
                            <span>Workflows</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold d-flex align-items-center gap-2 {{ request()->routeIs('console') ? 'active' : '' }}" href="{{ route('console') }}">
                            <i class="fa-solid fa-terminal" style="color: var(--bs-primary);"></i>
                            <span>Console</span>
                        </a>
                    </li>
                </ul>
                <!-- Profile Dropdown -->
                <ul class="navbar-nav ms-auto mt-3 mt-lg-0">
                    <!-- Desktop (lg and up): show dropdown; Mobile: show as simple links -->
                    <li class="nav-item d-none d-lg-block dropdown">
                        <a class="nav-link py-0 px-2 dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-inline-block rounded-circle bg-primary text-white fw-bold text-center me-2" style="width:32px; height:32px; line-height:32px; font-size:1.1rem;">
                                {{ strtoupper(substr('Admin', 0, 1)) }}
                            </span>
                            <span class="fw-medium">Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-1" href="{{route('profile')}}">
                                    <i class="fa-solid fa-user-circle"></i> Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-1" id="btn-logout">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <!-- Mobile: directly show links, no dropdown -->
                    <li class="nav-item d-lg-none">
                        <a class="nav-link d-flex align-items-center gap-2 " href="{{route('profile')}}">
                            <i class="fa-solid fa-user-circle"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li class="nav-item d-lg-none">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="button" class="nav-link btn btn-link d-flex align-items-center gap-2 py-1" id="btn-logout2">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

@push('scripts')
    <script>
      $(document).ready(function() {
        $('#btn-logout').on('click', function() {
          Swal.fire({
              icon: 'warning',
              title: 'Logout',
              text: 'Are you sure you want to logout?',
              showCancelButton: true,
              confirmButtonText: '<i class="fas fa-arrow-right-from-bracket me-1"></i> Logout',
              cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
              background: '#212529',
              color: '#fff',
              customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if(result.isConfirmed) {
                  $('#btn-logout').closest('form').submit();
              }
          });
        });
        $('#btn-logout2').on('click', function() {
          Swal.fire({
              icon: 'warning',
              title: 'Logout',
              text: 'Are you sure you want to logout?',
              showCancelButton: true,
              confirmButtonText: '<i class="fas fa-arrow-right-from-bracket me-1"></i> Logout',
              cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
              background: '#212529',
              color: '#fff',
              customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if(result.isConfirmed) {
                  $('#btn-logout2').closest('form').submit();
              }
          });
        });
      });
    </script>
@endpush