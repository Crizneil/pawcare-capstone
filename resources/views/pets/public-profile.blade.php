@extends('index')

@section('content')
    <!-- Explicitly include core CSS to ensure theme is applied -->
    <link href="{{ asset('assets/css/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/flaticon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <div class="public-card-page">
        <div class="public-card-container pos-rel">
            <!-- Floating shapes like in index/login -->
            <div class="floating-paws">
                <img src="{{ asset('assets/images/slider/shape-1.svg') }}" class="p-shape s1" alt="">
                <img src="{{ asset('assets/images/slider/shape-2.svg') }}" class="p-shape s2" alt="">
                <img src="{{ asset('assets/images/slider/shape-3.svg') }}" class="p-shape s3" alt="">
                <img src="{{ asset('assets/images/slider/shape-4.svg') }}" class="p-shape s4" alt="">
            </div>

            <!-- Side Decorative Images (Dog and Cat) -->
            <div class="side-ornament left-pet d-none d-xl-block">
                <img src="{{ asset('assets/images/slider/slide-1.png') }}" alt="Pet Image Left">
            </div>
            <div class="side-ornament right-pet d-none d-xl-block">
                <img src="{{ asset('assets/images/slider/slide-2.png') }}" alt="Pet Image Right">
            </div>

            <div class="container py-3">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 col-sm-11">
                        <!-- The Digital Card (Compact Info) -->
                        <div class="card border-0 shadow-2xl overflow-hidden profile-digital-card">
                            <!-- Top Header Ribbon inside the card -->
                            <div class="card-top-branding p-3 text-center">
                                 <img src="{{ asset('assets/images/newlogo.png') }}" style="height: 35px;">
                                 <h6 class="mt-1 text-dark font-weight-bold mb-0" style="letter-spacing: 1.5px;">PAWCARE DIGITAL ID</h6>
                            </div>

                            <div class="card-body p-0">
                                <!-- Information Section -->
                                <div class="p-4 bg-white text-center">
                                    <div class="profile-img-wrap mb-3">
                                        <img src="{{ $pet->image_url ?? 'https://ui-avatars.com/api/?name=' . $pet->name }}"
                                            class="rounded-circle shadow-lg"
                                            style="width: 110px; height: 110px; object-fit: cover; border: 5px solid #FCE7D6;">
                                    </div>
                                    <h2 class="font-weight-bold mb-1" style="font-size: 2.2rem; color: #1F2937;">{{ $pet->name }}</h2>
                                    <div class="mb-2">
                                        <span class="badge badge-success rounded-pill px-3 py-1 font-weight-bold" style="font-size: 0.8rem;">
                                            <i class="ti-check-box mr-1"></i> VERIFIED
                                        </span>
                                    </div>
                                    <h5 class="text-primary font-weight-bold mb-3" style="letter-spacing: 2px;">
                                        {{ $pet->unique_id }}
                                    </h5>
                                    <p class="text-muted font-weight-bold mb-4" style="font-size: 0.9rem;">
                                        {{ $pet->breed ?? 'Unknown Breed' }} <span class="mx-1">•</span> {{ $pet->species }}
                                    </p>

                                    <div class="row mb-4">
                                        <div class="col-6 pr-2">
                                            <div class="info-box-compact h-100">
                                                <p class="mb-1 text-muted smaller-text text-uppercase font-weight-bold">Status</p>
                                                <h6 class="font-weight-bold mb-0 {{ $pet->vaccinations->isNotEmpty() ? 'text-success' : 'text-danger' }}">
                                                    {{ $pet->vaccinations->isNotEmpty() ? 'Vaccinated' : 'Unvaccinated' }}
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="col-6 pl-2">
                                            <div class="info-box-compact h-100">
                                                <p class="mb-1 text-muted smaller-text text-uppercase font-weight-bold">Birthdate</p>
                                                <h6 class="font-weight-bold text-dark mb-0">
                                                    {{ $pet->birthdate ? \Carbon\Carbon::parse($pet->birthdate)->format('M d, Y') : 'N/A' }}
                                                </h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="vaccine-card-section p-3 rounded-xl shadow-sm bg-light-faded">
                                        <h6 class="font-weight-bold mb-3 border-bottom pb-2 text-primary d-flex align-items-center justify-content-center">
                                            <i class="ti-medall-alt mr-2 text-warning"></i> VACCINATION HISTORY
                                        </h6>
                                        @forelse($pet->vaccinations->take(3) as $vax)
                                            <div class="vax-item-compact d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white">
                                                <div class="vax-info text-left">
                                                    <span class="font-weight-bold text-dark d-block text-uppercase" style="font-size: 0.8rem;">{{ $vax->vaccine_name }}</span>
                                                    <span class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($vax->administered_at)->format('M d, Y') }}</span>
                                                </div>
                                                <div class="vax-due text-right">
                                                    <span class="badge bg-soft-primary text-primary rounded-pill px-2 py-0 font-weight-bold" style="font-size: 0.6rem;">NEXT DUE</span>
                                                    <span class="d-block font-weight-bold text-primary" style="font-size: 0.8rem;">{{ $vax->next_due_at ? \Carbon\Carbon::parse($vax->next_due_at)->format('M d, Y') : 'N/A' }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="py-2 text-center">
                                                <p class="text-muted smaller-text mb-0">No vaccination history.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="theme-btn-s2 rounded-pill px-4 py-2 shadow-lg animate-up" style="font-size: 0.9rem;">
                                <i class="ti-home mr-1"></i> BACK TO HOME
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* HIDE TOP NAVIGATION ON THIS PAGE */
        #header, .wpo-site-header, .wpo-site-footer { display: none !important; }

        .pos-rel { position: relative; }
        .public-card-page {
            background-color: #FCF1E8;
            background-image: radial-gradient(#FABE3C 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .public-card-container {
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            position: relative;
        }

        .p-shape {
            position: absolute;
            opacity: 0.1;
            width: 80px;
        }
        .s1 { top: 10%; left: 5%; }
        .s2 { top: 80%; left: 15%; }
        .s3 { top: 15%; right: 10%; }
        .s4 { bottom: 15%; right: 5%; }

        .side-ornament {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 320px;
            opacity: 0.9;
            z-index: 2;
            pointer-events: none;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));
        }
        .left-pet { left: -80px; }
        .right-pet { right: -80px; }

        .side-ornament img { width: 100%; height: auto; }

        .card-top-branding {
            background-color: #f8fafc;
            border-bottom: 2px solid #FCE7D6;
        }

        .profile-digital-card {
            z-index: 10;
            border: 8px solid white !important;
            border-radius: 35px !important;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2) !important;
        }

        .info-box-compact {
            background: #F8FAFC;
            padding: 0.75rem;
            border-radius: 15px;
            border: 1px solid #EDF2F7;
        }

        .smaller-text { font-size: 0.65rem !important; }

        .bg-light-faded { background: #fafafa; }
        .vax-item-compact:last-child { border-bottom: none !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }

        .bg-soft-primary { background-color: #FDF2F0; }
        .rounded-xl { border-radius: 20px !important; }

        .animate-up { transition: all 0.3s ease; }
        .animate-up:hover { transform: translateY(-3px); }

        @media (max-width: 1400px) { .side-ornament { width: 250px; } }
        @media (max-width: 1100px) { .side-ornament { display: none; } }
        @media (max-width: 575px) {
            .profile-digital-card { border-radius: 25px !important; border-width: 5px !important; }
            .public-card-container { padding: 20px 0; }
        }
    </style>
@endsection