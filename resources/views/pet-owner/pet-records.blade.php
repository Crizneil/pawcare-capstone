@extends('layouts.admin')

@section('page_title', 'My Pets')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">My Pets</h2>
            <p class="text-muted small">Manage your pets and view their digital ID cards.</p>
        </div>
        {{-- Link to the Vaccination Scheduling page --}}
        <button class="btn btn-orange rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#registerPetModal">
            <i data-lucide="plus-circle" class="me-1" style="width: 18px;"></i> Register New Pet
        </button>
    </div>

    <div class="row">
        @forelse(Auth::user()->pets as $pet)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $pet->image_url ?? 'https://ui-avatars.com/api/?name=' . $pet->name . '&background=fce7d6&color=primary' }}"
                             class="rounded-circle border border-3 border-white shadow-sm"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="ms-3">
                            <h4 class="fw-bold text-dark mb-0">{{ $pet->name }}</h4>
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary px-3">
                                {{ ucfirst($pet->species) }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="small text-muted d-block text-uppercase fw-bold">Breed</label>
                            <span class="text-dark fw-medium">{{ $pet->breed ?? 'Labrador' }}</span>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted d-block text-uppercase fw-bold">Birthdate</label>
                            <span class="text-dark fw-medium">{{ $pet->birthdate ? \Carbon\Carbon::parse($pet->birthdate)->format('M d, Y') : 'Jan 05, 2022' }}</span>
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-bold text-dark">Vaccination Status</span>
                            {{-- This logic depends on your database flags --}}
                            @if($pet->is_fully_vaccinated)
                                <span class="text-success small fw-bold"><i data-lucide="check-circle" class="me-1" style="width: 14px;"></i> Fully Vaccinated</span>
                            @else
                                <span class="text-warning small fw-bold"><i data-lucide="alert-circle" class="me-1" style="width: 14px;"></i> Incomplete</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        {{-- Button to the Digital ID Card page we just built --}}
                        <a href="{{ route('pet-owner.digital-id', $pet->id) }}" class="btn btn-outline-dark rounded-pill shadow-sm">
                            <i data-lucide="qr-code" class="me-2" style="width: 16px;"></i> View Digital ID
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="mb-3"><i data-lucide="paw-print" class="text-muted" style="width: 60px; height: 60px;"></i></div>
            <h5 class="text-muted">No pets registered yet.</h5>
            <p class="text-muted small">Please visit the clinic to register your pet's information.</p>
        </div>
        @endforelse
    </div>
</div>
@include('partials._register_pet_modal')
@endsection
