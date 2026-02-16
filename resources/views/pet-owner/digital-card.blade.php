@extends('layouts.dashboard')

@section('content')
    <div class="container py-5 fade-in">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('pet-owner.dashboard') }}" class="btn btn-light rounded-circle mr-3">
                        <i data-lucide="arrow-left"></i>
                    </a>
                    <h2 class="font-weight-bold mb-0">Pet Digital ID Card</h2>
                </div>

                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 30px;">
                    <div class="card-body p-0">
                        <div class="row no-gutters">
                            <!-- Left Side: Information -->
                            <div class="col-lg-7 p-4 p-md-5 bg-white">
                                <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-5">
                                    <div class="mb-3 mb-sm-0 mr-sm-4">
                                        <img src="{{ $pet->image_url ?? 'https://ui-avatars.com/api/?name=' . $pet->name }}"
                                            class="rounded-circle shadow-sm"
                                            style="width: 120px; height: 120px; object-fit: cover; border: 5px solid #fce7d6;">
                                    </div>
                                    <div>
                                        <h1 class="font-weight-bold mb-1 pet-name-title"
                                            style="font-size: 3rem; color: #1f2937;">{{ $pet->name }}</h1>
                                        <p class="text-primary font-weight-bold h3 mb-0">
                                            {{ $pet->breed ?? 'Unknown Breed' }}</p>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-6">
                                        <p class="mb-1 text-muted small text-uppercase font-weight-bold">Species</p>
                                        <h5 class="font-weight-bold text-dark">{{ $pet->species }}</h5>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 text-muted small text-uppercase font-weight-bold">Birthdate</p>
                                        <h5 class="font-weight-bold text-dark small-responsive">
                                            {{ $pet->birthdate ? \Carbon\Carbon::parse($pet->birthdate)->format('M d, Y') : 'N/A' }}
                                        </h5>
                                    </div>
                                    <div class="col-12 mt-4 text-break">
                                        <p class="mb-1 text-muted small text-uppercase font-weight-bold">Owner Name</p>
                                        <h5 class="font-weight-bold text-dark">{{ $pet->user->name }}</h5>
                                        <p class="small text-muted mb-0">
                                            {{ $pet->user->city }}, {{ $pet->user->province }}
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-light p-4 rounded-xl">
                                    <p class="mb-3 text-muted small text-uppercase font-weight-bold border-bottom pb-2">
                                        Recent Vaccinations</p>
                                    @forelse($pet->vaccinations->take(2) as $vax)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="font-weight-bold text-dark small">{{ $vax->vaccine_name }}</span>
                                            <span
                                                class="text-muted small">{{ \Carbon\Carbon::parse($vax->administered_at)->format('M d, Y') }}</span>
                                        </div>
                                    @empty
                                        <p class="text-muted small mb-0">No records found.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Right Side: QR & ID -->
                            <div class="col-lg-5 p-5 d-flex flex-column align-items-center justify-content-center border-left"
                                style="background-color: #f8fafc !important;">
                                <div class="bg-white p-4 shadow-sm rounded-xl mb-4">
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->generate(route('pet.public-profile', $pet->unique_id)) !!}
                                </div>
                                <h2 class="font-weight-bold text-dark mb-1" style="letter-spacing: 4px;">
                                    {{ $pet->unique_id }}</h2>
                                <p class="text-muted text-uppercase mb-5 text-center"
                                    style="letter-spacing: 2px; font-size: 0.8rem;">Official Registry ID</p>

                                <img src="{{ asset('assets/images/newlogo.png') }}" style="height: 40px; opacity: 0.5;">
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-dark text-white p-3 text-center">
                            <p class="mb-0 small" style="letter-spacing: 2px;">PAWCARE VETERINARY CLINIC • MEYCAUAYAN ANIMAL
                                REGISTRY SYSTEM</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center d-print-none">
                    <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-4 font-weight-bold">
                        <i data-lucide="printer" class="mr-2"></i> Print Digital Card
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 576px) {
            .pet-name-title {
                font-size: 2.2rem !important;
            }

            .small-responsive {
                font-size: 0.9rem !important;
            }
        }

        @media print {
            .main-content-wrapper {
                padding: 0 !important;
            }

            #sidebar,
            header,
            .btn-light {
                display: none !important;
            }

            .container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
            }
        }
    </style>
@endsection