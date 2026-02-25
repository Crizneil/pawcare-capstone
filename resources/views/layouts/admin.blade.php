<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PawCare</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sass/style.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <div class="d-flex">
        <aside id="sidebar" class="d-flex flex-column px-3 py-4"
            style="width: 280px; position: fixed; height: 100vh; background: #fff;">
            <div class="mb-4 px-3">
                <img src="{{ asset('assets/images/newlogo.png') }}" alt="PawCare" style="height: 40px;">
            </div>

            <div class="sidebar-nav-scroll flex-grow-1 px-2" style="overflow-y: auto;">
                <nav class="nav flex-column">
                    {{-- ADMIN LINKS --}}
                    @if(Auth::check() && strtolower(Auth::user()->role) === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-grid"></i> Overview
                        </a>
                        <a href="{{ route('admin.pet-records') }}"
                            class="nav-link {{ request()->routeIs('admin.pet-records') ? 'active' : '' }}">
                            <i data-lucide="shield-check"></i> Pet Database
                        </a>
                        <a href="{{ route('admin.employees') }}"
                            class="nav-link {{ request()->is('admin/employees*') ? 'active' : '' }}">
                            <i data-lucide="users"></i> Staff
                        </a>
                        <a href="{{ route('admin.appointments') }}"
                            class="nav-link {{ request()->is('admin/appointments*') ? 'active' : '' }}">
                            <i data-lucide="calendar"></i> Appointments
                        </a>
                        <a href="{{ route('admin.logs') }}"
                            class="nav-link {{ request()->is('admin/logs*') ? 'active' : '' }}">
                            <i data-lucide="history"></i> Logs
                        </a>
                        <a href="{{ route('admin.vaccination-status') }}"
                            class="nav-link {{ request()->routeIs('admin.vaccination-status') ? 'active' : '' }}">
                            <i data-lucide="syringe"></i> Vaccination Status
                        </a>
                        <a href="{{ route('admin.vaccinations') }}"
                            class="nav-link {{ request()->routeIs('admin.vaccinations') ? 'active' : '' }}">
                            <i data-lucide="package"></i> Vaccine Inventory
                        </a>
                        <a href="{{ route('admin.profile') }}"
                            class="nav-link {{ request()->is('admin/profile*') ? 'active' : '' }}">
                            <i data-lucide="user-cog"></i> Profile
                        </a>

                        {{-- STAFF LINKS --}}
                    @elseif(Auth::check() && strtolower(Auth::user()->role) === 'staff')
                        <a href="{{ route('staff.dashboard') }}"
                            class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard"></i> Overview
                        </a>
                        <a href="{{ route('staff.appointments') }}"
                            class="nav-link {{ request()->is('staff/appointments*') ? 'active' : '' }}">
                            <i data-lucide="calendar"></i> Appointments
                        </a>
                        <a href="{{ route('staff.pet-records') }}"
                            class="nav-link {{ request()->routeIs('staff.pet-records') ? 'active' : '' }}">
                            <i data-lucide="file-spreadsheet"></i> Pet Records
                        </a>
                        <a href="{{ route('staff.vaccination-status') }}"
                            class="nav-link {{ request()->routeIs('staff.vaccination-status') ? 'active' : '' }}">
                            <i data-lucide="syringe"></i> Vaccination Status
                        </a>
                        <a href="{{ route('staff.vaccination-history') }}"
                            class="nav-link {{ request()->routeIs('staff.vaccination-history') ? 'active' : '' }}">
                            <i data-lucide="history"></i> Vaccination History
                        </a>
                        <a href="{{ route('staff.vaccine-inventory') }}"
                            class="nav-link {{ request()->routeIs('staff.vaccine-inventory') ? 'active' : '' }}">
                            <i data-lucide="package"></i> Vaccine Inventory
                        </a>
                        <a href="{{ route('staff.profile') }}"
                            class="nav-link {{ request()->is('staff/profile*') ? 'active' : '' }}">
                            <i data-lucide="user-cog"></i> Profile
                        </a>

                        {{-- PET OWNER LINKS --}}
                    @elseif(Auth::check() && strtolower(Auth::user()->role) === 'owner')
                        <a href="{{ route('pet-owner.dashboard') }}"
                            class="nav-link {{ request()->routeIs('pet-owner.dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard"></i> Overview
                        </a>
                        <a href="{{ route('pet-owner.appointments') }}"
                            class="nav-link {{ request()->routeIs('pet-owner.appointments') ? 'active' : '' }}">
                            <i data-lucide="calendar"></i> Appointment
                        </a>
                        <a href="{{ route('pet-owner.pet-records') }}"
                            class="nav-link {{ request()->routeIs('pet-owner.pet-records') ? 'active' : '' }}">
                            <i data-lucide="dog"></i> My Pets
                        </a>
                        <a href="{{ route('pet-owner.vaccination-history') }}"
                            class="nav-link {{ request()->routeIs('pet-owner.vaccination-history') ? 'active' : '' }}">
                            <i data-lucide="syringe"></i> Vaccination History
                        </a>
                        <a href="{{ route('pet-owner.profile') }}"
                            class="nav-link {{ request()->routeIs('pet-owner.profile') ? 'active' : '' }}">
                            <i data-lucide="user"></i> Profile
                        </a>
                    @endif
                </nav>
            </div>

            <div class="mt-auto pt-3 px-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-logout w-100 d-flex align-items-center justify-content-center">
                        <i data-lucide="log-out" class="me-2"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
        <div class="content-area flex-grow-1" style="margin-left: 280px; width: calc(100% - 280px);">
            <header class="bg-white shadow-sm p-3 d-flex justify-content-between d-md-none">
                <img src="{{ asset('assets/images/newlogo.png') }}" style="height: 35px;">
                <button class="btn btn-light" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
            </header>

            @include('layouts.header')

            <div class="main-content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        lucide.createIcons();
        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('active'); }
    </script>
    @include('partials._developer_widget')
</body>

</html>