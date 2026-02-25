@extends('layouts.admin')

@section('page_title', 'Profile Dashboard')

@section('content')
<div class="main-content p-4">
    <div class="container-fluid">
        <div class="bg-white rounded-4 shadow-sm overflow-hidden">
            <div class="row g-0"> <div class="col-lg-4 bg-light-subtle border-end p-5 text-center">
                    <div class="mb-4">
                        <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=d35400&color=fff&size=128' }}"
                            class="rounded-circle shadow-sm border border-4 border-white"
                            alt="Profile Picture"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    </div>

                    <h3 class="fw-bold text-dark mb-1">{{ auth()->user()->name }}</h3>
                    <span class="badge bg-orange-subtle text-orange px-3 py-2 rounded-pill mb-4">
                        {{ strtoupper(auth()->user()->role) }}
                    </span>

                    <hr class="my-4 mx-5 opacity-25">

                    <div class="text-start px-3">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Email Address</label>
                        <p class="fw-bold text-primary">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <div class="col-lg-8 p-5">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h4 class="fw-bold text-dark m-0">Account Information</h4>
                        <button class="btn btn-orange rounded-pill px-4 shadow-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editProfileModal">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </button>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1">House Number</label>
                            <p class="border-bottom pb-2 fs-5 text-dark">{{ auth()->user()->house_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Street</label>
                            <p class="border-bottom pb-2 fs-5 text-dark">{{ auth()->user()->street ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Barangay</label>
                            <p class="border-bottom pb-2 fs-5 text-dark">{{ auth()->user()->barangay }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold mb-1">City</label>
                            <p class="border-bottom pb-2 fs-5 text-dark">{{ auth()->user()->city }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Province</label>
                            <p class="border-bottom pb-2 fs-5 text-dark">{{ auth()->user()->province }}</p>
                        </div>
                    </div>

                    <div class="mt-5 p-4 rounded-3 d-flex align-items-center" style="background-color: #fff9f4; border-left: 5px solid #d35400;">
                        <div class="me-3">
                            <i class="fas fa-shield-alt text-orange fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Security Note</h6>
                            <p class="small text-muted mb-0">Keep your information accurate. We recommend updating your details regularly to maintain account security.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials._edit_profile_modal')
@endsection
