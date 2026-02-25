@extends('layouts.admin') {{-- Ensure this matches your main layout file --}}

@section('content')
<div class="container-fluid p-4 fade-in">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('pet-owner.pet-records') }}" class="btn btn-white rounded-circle shadow-sm me-3">
                <i data-lucide="arrow-left" class="text-primary"></i>
            </a>
            <div>
                <h2 class="fw-bold mb-0">Pet Digital ID</h2>
                <p class="text-muted small mb-0">Official Vaccination & Identification Record</p>
            </div>
        </div>
        <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 shadow-sm d-print-none">
            <i data-lucide="printer" class="me-2" style="width: 18px;"></i> Print ID Card
        </button>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Main ID Card --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden border-top border-primary border-5">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-7 p-4 p-md-5 bg-white">
                            <div class="d-flex align-items-center mb-4">
                                <div class="position-relative">
                                    <img src="{{ $pet->image_url ?? 'https://ui-avatars.com/api/?name=' . $pet->name . '&background=fce7d6&color=primary' }}"
                                         class="rounded-4 shadow-sm"
                                         style="width: 140px; height: 140px; object-fit: cover; border: 4px solid #fff;">
                                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-white p-2">
                                        <span class="visually-hidden">Active</span>
                                    </span>
                                </div>
                                <div class="ms-4">
                                    <h1 class="display-5 fw-bold text-dark mb-0">{{ $pet->name }}</h1>
                                    <p class="text-primary fw-bold h4 mb-0">{{ $pet->breed ?? 'Labrador' }}</p>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-6 col-md-4">
                                    <label class="text-uppercase small fw-bold text-muted d-block">Species</label>
                                    <span class="fw-bold text-dark">{{ ucfirst($pet->species) }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="text-uppercase small fw-bold text-muted d-block">Birthdate</label>
                                    <span class="fw-bold text-dark">{{ $pet->birthday ? \Carbon\Carbon::parse($pet->birthday)->format('M d, Y') : 'Jan 5, 2022' }}</span>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="text-uppercase small fw-bold text-muted d-block">Owner</label>
                                    <span class="fw-bold text-dark">{{ $pet->user->name }}</span>
                                </div>
                            </div>

                            {{-- Digital Vaccination Card Section --}}
                            <div class="bg-light rounded-4 p-4">
                                <h6 class="fw-bold text-dark text-uppercase mb-3 border-bottom pb-2">
                                    <i data-lucide="shield-check" class="me-2 text-primary" style="width: 18px;"></i> Digital Vaccination Card
                                </h6>
                                <div class="vstack gap-2">
                                    @forelse($pet->vaccinations->take(3) as $vax)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark"><i data-lucide="check-circle-2" class="text-success me-2" style="width: 14px;"></i> {{ $vax->vaccine_name }}</span>
                                        <span class="fw-bold text-muted small">{{ \Carbon\Carbon::parse($vax->date_administered)->format('M d, Y') }}</span>
                                    </div>
                                    @empty
                                    <p class="text-muted small">No recent vaccinations recorded.</p>
                                    @endforelse

                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <span class="fw-bold text-dark">Next Due:</span>
                                        <span class="text-danger fw-bold">{{ $pet->next_due_date ? \Carbon\Carbon::parse($pet->next_due_date)->format('M d, Y') : 'Apr 10, 2025' }}</span>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success px-3 w-100 py-2">
                                            Status: Fully Vaccinated
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5 p-5 d-flex flex-column align-items-center justify-content-center border-start bg-light-subtle">
                            {{-- REPLACE THE QR CODE BLOCK START --}}
                            <div class="bg-white p-3 shadow-sm rounded-4 mb-3 text-center">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)
                                    ->eye('circle')
                                    ->color(255, 102, 0)
                                    ->margin(1)
                                    ->generate(route('pet.public-profile', $pet->pet_id)) !!}
                            </div>
                            <h3 class="fw-bold text-dark mb-1" style="letter-spacing: 5px;">{{ $pet->pet_id }}</h3>
                            <p class="text-muted text-uppercase small ls-wide mb-4">Official Registry ID</p>
                            <p class="small text-muted d-print-none">Scan to verify records</p>
                            {{-- REPLACE THE QR CODE BLOCK END --}}

                            <div class="mt-auto opacity-50">
                                <img src="{{ asset('assets/images/newlogo.png') }}" style="height: 35px;">
                            </div>
                        </div>
                    </div>

                    <div class="bg-dark text-white py-3 text-center">
                        <p class="mb-0 small fw-bold" style="letter-spacing: 2px;">
                            PAWCARE VETERINARY CLINIC • MEYCAUAYAN ANIMAL REGISTRY
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-wide { letter-spacing: 2px; }
    @media print {
        #sidebar, header, .d-print-none, .btn-white { display: none !important; }
        .container-fluid { padding: 0 !important; }
        .card { border: 1px solid #ddd !important; shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection
