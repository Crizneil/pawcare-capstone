<header class="bg-white shadow-sm px-4 py-3 d-flex justify-content-between align-items-center sticky-top"
        style="z-index: 1045;">
    {{-- LEFT SIDE --}}
    <div class="d-flex align-items-center">

        {{-- Mobile Toggle --}}
        <button class="btn btn-light border d-md-none me-3"
                id="sidebarToggle"
                type="button">
            <i data-lucide="menu"></i>
        </button>

        {{-- Page Title Desktop --}}
        <h5 class="fw-bold text-dark mb-0 d-none d-md-block">
            @yield('page_title', 'Dashboard')
        </h5>

        {{-- Logo Mobile --}}
        <img src="{{ asset('assets/images/newlogo.png') }}"
             style="height: 30px;"
             class="d-md-none">
    </div>

    {{-- RIGHT SIDE --}}
    <div class="d-flex align-items-center">

        <div class="text-end me-3 d-none d-sm-block">
            <p class="fw-bold mb-0 small text-dark" style="line-height:1.2;">
                {{ Auth::user()->name }}
            </p>
            <p class="text-primary mb-0 small text-uppercase fw-bold"
               style="font-size:10px; letter-spacing:0.5px;">
                {{ Auth::user()->role }}
            </p>
        </div>

        <a href="{{
            Auth::user()->role === 'admin' ? route('admin.profile') :
            (Auth::user()->role === 'staff' ? route('staff.profile') : route('pet-owner.profile'))
        }}" class="position-relative">

            <img src="{{ Auth::user()->profile_image
                ? asset('storage/' . Auth::user()->profile_image)
                : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=d35400&color=fff' }}"
                 class="rounded-circle border shadow-sm"
                 style="width:42px; height:42px; object-fit:cover;"
                 alt="Profile Picture">

            <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                  style="width:12px; height:12px;">
            </span>
        </a>

    </div>
</header>
