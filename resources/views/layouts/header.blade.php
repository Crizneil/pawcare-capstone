<header class="bg-white shadow-sm px-4 py-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <h5 class="font-weight-bold text-dark mb-0 d-none d-md-block">
            @yield('page_title', 'Dashboard')
        </h5>
        <img src="{{ asset('assets/images/newlogo.png') }}" style="height: 35px;" class="d-md-none">
    </div>

    <div class="d-flex align-items-center">
        <div class="text-end me-3 d-none d-sm-block text-right">
            <p class="fw-bold mb-0 small text-dark" style="line-height: 1.2;">{{ Auth::user()->name }}</p>
            <p class="text-primary mb-0 small text-uppercase fw-800" style="font-size: 10px; letter-spacing: 0.5px;">
                {{ Auth::user()->role }}
            </p>
        </div>
        <a href="{{ route('admin.profile') }}">
            <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=d35400&color=fff' }}"
                class="rounded-circle border shadow-sm"
                style="width: 42px; height: 42px; object-fit: cover;"
                alt="Profile Picture">
        </a>
    </div>
</header>
