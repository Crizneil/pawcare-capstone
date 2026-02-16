@extends('layouts.dashboard')

@push('styles')
    <style>
        @media (max-width: 768px) {
            .pet-name-title {
                font-size: 1.8rem !important;
            }

            .card-body-padding {
                padding: 1.5rem !important;
            }

            .qr-section {
                border-left: none !important;
                border-top: 1px solid #dee2e6;
                padding: 2rem !important;
            }
        }

        .rounded-xl {
            border-radius: 1.5rem !important;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            transition: all 0.3s;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid fade-in">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
            <div class="mb-3 mb-md-0">
                <h2 class="font-weight-bold mb-0">My Pet Registry</h2>
                <p class="text-muted">Manage your pets and view their digital cards.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary rounded-pill px-4 font-weight-bold mr-2 mb-2"
                    data-toggle="modal" data-target="#registerPetModal" data-bs-toggle="modal"
                    data-bs-target="#registerPetModal">
                    <i data-lucide="plus" class="mr-2"></i> Register Pet
                </button>
                <a href="#" class="btn btn-outline-primary rounded-pill px-4 font-weight-bold mb-2">
                    <i data-lucide="calendar" class="mr-2"></i> Book Appointment
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-xl mb-4" role="alert">
                <i data-lucide="check-circle" class="mr-2" style="width: 20px;"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-xl mb-4" role="alert">
                <i data-lucide="alert-circle" class="mr-2" style="width: 20px;"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            @forelse($pets as $pet)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-xl overflow-hidden card-hover">
                        <div class="card-body p-0">
                            <div class="row no-gutters">
                                <!-- Left side: Information -->
                                <div class="col-lg-8 card-body-padding p-4 bg-white">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-4">
                                        <div class="mb-3 mb-sm-0 mr-sm-4">
                                            <img src="{{ $pet->image_url ?? 'https://ui-avatars.com/api/?name=' . $pet->name }}"
                                                class="rounded-circle shadow-sm"
                                                style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fce7d6;">
                                        </div>
                                        <div>
                                            <h1 class="font-weight-bold mb-1 pet-name-title"
                                                style="font-size: 2.5rem; color: #1f2937;">{{ $pet->name }}</h1>
                                            <p class="text-primary font-weight-bold h4 mb-2">
                                                {{ $pet->breed ?? 'Unknown Breed' }}</p>
                                            <div class="d-flex flex-wrap gap-3 text-muted">
                                                <span class="mr-3"><i data-lucide="info" class="mr-1" style="width: 16px;"></i>
                                                    {{ $pet->species }}</span>
                                                <span><i data-lucide="calendar" class="mr-1" style="width: 16px;"></i> Born:
                                                    {{ $pet->birthdate ? \Carbon\Carbon::parse($pet->birthdate)->format('M d, Y') : 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <p class="mb-1 text-muted small text-uppercase font-weight-bold">Owner Information
                                            </p>
                                            <h5 class="font-weight-bold text-dark">{{ Auth::user()->name }}</h5>
                                            <p class="small text-muted mb-0">
                                                {{ Auth::user()->city }}, {{ Auth::user()->province }}
                                            </p>
                                        </div>
                                        <div class="col-sm-6 border-left-sm">
                                            <p class="mb-1 text-muted small text-uppercase font-weight-bold">Registration Status
                                            </p>
                                            <span class="badge badge-success rounded-pill px-3 py-2">Verified Registered</span>
                                            <p class="small text-muted mt-2 mb-0">Member since
                                                {{ \Carbon\Carbon::parse($pet->registry_date)->format('Y') }}</p>
                                        </div>
                                    </div>

                                    <!-- Vaccination Section -->
                                    <div class="mt-4 pt-4 border-top">
                                        <p class="mb-3 text-muted small text-uppercase font-weight-bold">Recent Vaccinations</p>
                                        @if($pet->vaccinations->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <thead>
                                                        <tr class="text-muted small">
                                                            <th>Vaccine</th>
                                                            <th>Date</th>
                                                            <th>Next Due</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pet->vaccinations->take(3) as $vax)
                                                            <tr>
                                                                <td class="font-weight-bold">{{ $vax->vaccine_name }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($vax->administered_at)->format('M d, Y') }}
                                                                </td>
                                                                <td class="text-primary font-weight-bold">
                                                                    {{ $vax->next_due_at ? \Carbon\Carbon::parse($vax->next_due_at)->format('M d, Y') : 'N/A' }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="bg-light p-3 rounded text-center">
                                                <p class="text-muted small mb-0">No records found. Visit the clinic to log vaccines.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Right side: QR Code -->
                                <div
                                    class="col-lg-4 qr-section p-4 d-flex flex-column align-items-center justify-content-center bg-light border-left">
                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(route('pet.public-profile', $pet->unique_id)) !!}
                                    <h3 class="font-weight-bold text-center mb-1" style="color: #1f2937; letter-spacing: 2px;">
                                        {{ $pet->unique_id }}</h3>
                                    <p class="small text-muted text-center text-uppercase mb-4">Official Digital ID</p>

                                    <div class="w-100 px-sm-4">
                                        <a href="{{ route('pet-owner.digital-card', $pet->id) }}"
                                            class="btn btn-dark btn-block rounded-pill font-weight-bold shadow-sm py-2">
                                            <i data-lucide="scan" class="mr-2" style="width: 18px;"></i> View Digital Card
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Key Text Below -->
                            <div class="bg-primary text-white p-2 text-center">
                                <p class="mb-0 small" style="letter-spacing: 1px; font-weight: 600;">PAWCARE VETERINARY CLINIC •
                                    MEYCAUAYAN ANIMAL REGISTRY SYSTEM</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center">
                    <div class="bg-white p-5 rounded-xl shadow-sm d-inline-block">
                        <i data-lucide="dog" class="text-muted mb-4 mx-auto" style="width: 80px; height: 80px;"></i>
                        <h3 class="font-weight-bold mt-3">No Registered Pets</h3>
                        <p class="text-muted mb-4 px-md-5">Register your beloved dog or cat to get their official digital card
                            and track their vaccinations.</p>
                        <button type="button" class="btn btn-primary rounded-pill px-5 py-2 font-weight-bold"
                            data-toggle="modal" data-target="#registerPetModal" data-bs-toggle="modal"
                            data-bs-target="#registerPetModal">
                            <i data-lucide="plus" class="mr-2"></i> Register Your First Pet
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Register Pet Modal -->
    <div class="modal fade" id="registerPetModal" tabindex="-1" role="dialog" aria-labelledby="registerPetModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content rounded-xl border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 p-4">
                    <h4 class="modal-title font-weight-bold" id="registerPetModalLabel">Register New Pet</h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('pet-owner.register-pet') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold small text-uppercase text-muted">Pet Name</label>
                            <input type="text" name="name" class="form-control rounded-pill border-light bg-light px-4 py-4"
                                placeholder="Enter pet name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase text-muted">Species</label>
                                    <select name="species" class="form-control rounded-pill border-light bg-light px-4"
                                        style="height: 50px;" required>
                                        <option value="Dog">Dog</option>
                                        <option value="Cat">Cat</option>
                                        <option value="Bird">Bird</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase text-muted">Breed</label>
                                    <input type="text" name="breed"
                                        class="form-control rounded-pill border-light bg-light px-4" style="height: 50px;"
                                        placeholder="e.g. Bulldog">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold small text-uppercase text-muted">Birthdate</label>
                            <input type="date" name="birthdate" class="form-control rounded-pill border-light bg-light px-4"
                                style="height: 50px;">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">Complete
                            Registration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection