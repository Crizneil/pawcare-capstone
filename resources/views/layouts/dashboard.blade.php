<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PawCare</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/newicon.png') }}">
    <link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/flaticon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sass/style.css') }}" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background-color: #fce7d6;
            font-family: 'Outfit', sans-serif;
        }

        #sidebar {
            min-height: 100vh;
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .main-content-wrapper {
            padding: 30px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 15px 20px;
            border: none;
            background: none;
            color: #666;
            font-weight: 600;
            transition: all 0.3s;
            border-radius: 10px;
            margin-bottom: 5px;
            text-decoration: none;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #fce7d6;
            color: #d35400;
        }

        .nav-link i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .role-badge {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            background: #fce7d6;
            color: #d35400;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 10px;
        }

        /* Mobile Sidebar Toggle */
        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                left: -280px;
                z-index: 1000;
            }

            #sidebar.active {
                left: 0;
            }

            .main-content-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="d-md-flex flex-column p-3"
            style="width: 280px; position: fixed; left: 0; top: 0; bottom: 0; z-index: 100;">
            <div class="d-flex align-items-center mb-4 px-2">
                <img src="{{ asset('assets/images/newlogo.png') }}" alt="PawCare" style="height: 50px;">
            </div>

            <nav class="nav flex-column flex-grow-1">
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard"></i> Overview
                    </a>
                    <a href="{{ route('admin.pet-records') }}"
                        class="nav-link {{ request()->routeIs('admin.pet-records') ? 'active' : '' }}">
                        <i data-lucide="shield-check"></i> Pet Database
                    </a>
                    <a href="{{ route('admin.employees') }}"
                        class="nav-link {{ request()->routeIs('admin.employees') ? 'active' : '' }}">
                        <i data-lucide="users"></i> Staff
                    </a>
                    <a href="{{ route('admin.vaccination-status') }}"
                        class="nav-link {{ request()->routeIs('admin.vaccination-status') ? 'active' : '' }}">
                        <i data-lucide="syringe"></i> Vaccination Status
                    </a>
                    <a href="{{ route('admin.vaccinations') }}"
                        class="nav-link {{ request()->routeIs('admin.vaccinations') ? 'active' : '' }}">
                        <i data-lucide="boxes"></i> Vaccine Inventory
                    </a>
                @elseif(Auth::user()->role === 'vet')
                    <a href="{{ route('vet.dashboard') }}"
                        class="nav-link {{ request()->routeIs('vet.dashboard') ? 'active' : '' }}">
                        <i data-lucide="scan-line"></i> Scanner
                    </a>
                    <a href="{{ route('vet.pet-records') }}"
                        class="nav-link {{ request()->routeIs('vet.pet-records') ? 'active' : '' }}">
                        <i data-lucide="file-text"></i> Pet Records
                    </a>
                    <a href="{{ route('vet.vaccination-status') }}"
                        class="nav-link {{ request()->routeIs('vet.vaccination-status') ? 'active' : '' }}">
                        <i data-lucide="syringe"></i> Vaccination Status
                    </a>
                    <a href="{{ route('vet.vaccinations') }}"
                        class="nav-link {{ request()->routeIs('vet.vaccinations') ? 'active' : '' }}">
                        <i data-lucide="boxes"></i> Vaccine Inventory
                    </a>
                @elseif(Auth::user()->role === 'owner')
                    <a href="{{ route('pet-owner.dashboard') }}"
                        class="nav-link {{ request()->routeIs('pet-owner.dashboard') ? 'active' : '' }}">
                        <i data-lucide="home"></i> My Pets
                    </a>
                    <!-- Book Appointment Placeholder -->
                    <a href="#" class="nav-link"><i data-lucide="calendar"></i> Book Appointment</a>
                @endif
            </nav>

            <div class="mt-auto pt-4 border-top">
                <div class="d-flex align-items-center px-2 mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=random"
                        class="rounded-circle mr-3" style="width: 40px; height: 40px;">
                    <div>
                        <div class="font-weight-bold" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                        <div class="d-flex align-items-center">
                            <small class="text-muted"
                                style="text-transform: capitalize;">{{ Auth::user()->role }}</small>
                        </div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 rounded-pill font-weight-bold">
                        <i data-lucide="log-out" style="width: 16px; margin-right: 5px;"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-grow-1 main-content-container">
            <header class="bg-white shadow-sm p-3 d-flex justify-content-between align-items-center d-md-none">
                <img src="{{ asset('assets/images/newlogo.png') }}" style="height: 40px;">
                <button class="btn btn-light" onclick="toggleSidebar()">
                    <i data-lucide="menu"></i>
                </button>
            </header>

            <div class="main-content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Additional Styles for Layout -->
    <style>
        .main-content-container {
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: all 0.3s;
        }

        @media (max-width: 768px) {
            .main-content-container {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
    @stack('scripts')
</body>

</html>