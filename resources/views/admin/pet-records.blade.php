@extends('layout.admin')

@section('page_title', 'Pet Records Dashboard')

@section('content')
    <div class="container-fluid p-4 fade-in">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">{{ $view === 'archived' ? 'Archived' : 'Pet' }} Database</h2>
                <p class="text-muted small mb-0">
                    {{ $view === 'archived' ? 'Manage and restore records for deceased or deleted pets.' : 'Manage registry and vaccination records for all pets.' }}
                </p>
            </div>
            <div class="d-flex gap-2">
                @if($view === 'archived')
                    <a href="{{ route('admin.pet-records') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">
                        <i class="bi bi-arrow-left me-2"></i> Back to Active
                    </a>
                @else
                    <a href="{{ route('admin.pet-records', ['view' => 'archived']) }}"
                        class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-archive me-2"></i> View Archived
                    </a>
                    <button type="button" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#addPetModal">
                        <i class="fi flaticon-plus me-2"></i> Add New Pet
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Unified Search Bar Card --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4 p-3">
            <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light rounded-start-pill ps-4">
                            <i class="bi bi-qr-code-scan text-muted"></i>
                        </span>
                        {{-- This is for specific ID filtering --}}
                        <input type="text" name="pet_id" value="{{ request('pet_id') }}"
                            class="form-control border-0 bg-light py-2" placeholder="Scan/Type Pet ID...">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light ps-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        {{-- Changed name to 'general_search' to match Controller --}}
                        <input type="text" name="general_search" value="{{ request('general_search') }}"
                            class="form-control border-0 bg-light py-2" placeholder="Search by name, breed, or owner...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-orange w-100 rounded-pill py-2 fw-bold shadow-sm">Search</button>
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-mobile-table">
                        <thead class="bg-light d-none d-md-table-header-group">
                            <tr class="text-uppercase small fw-bold text-muted">
                                <th class="ps-4 py-3">Pet ID</th>
                                <th>Pet Info</th>
                                <th>Type</th>
                                <th>Owner</th>
                                <th>Vax Status</th>
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pets as $pet)
                                <tr>
                                    <td class="ps-md-4" data-label="Pet ID">
                                        <span
                                            class="badge {{ $pet->trashed() ? 'bg-danger-subtle text-danger' : 'bg-light text-dark' }} border">
                                            #{{ $pet->id }}{{ $pet->trashed() ? ' [Deleted]' : '' }}
                                        </span>
                                    </td>

                                    <td data-label="Pet Info">
                                        <div class="fw-bold text-dark">{{ $pet->name }}</div>
                                        <small class="text-muted">{{ $pet->breed ?? 'Hybrid' }}</small>
                                    </td>

                                    <td data-label="Type">
                                        <span class="badge bg-blue-light text-primary text-capitalize">
                                            {{ $pet->type ?? $pet->species ?? 'Dog' }}
                                        </span>
                                    </td>

                                    <td data-label="Owner">
                                        <div class="text-dark fw-medium">{{ $pet->user->name ?? 'Unassigned' }}</div>
                                        <div class="text-muted small">{{ $pet->user->phone ?? 'No Phone' }}</div>
                                    </td>

                                    <td data-label="Vax Status">
                                        @if($pet->status === 'DECEASED')
                                            <span class="badge rounded-pill bg-dark text-white px-3">
                                                DECEASED
                                            </span>
                                        @elseif($pet->trashed())
                                            <span class="badge rounded-pill bg-danger text-white px-3">
                                                ARCHIVED
                                            </span>
                                        @elseif(isset($pet->status) && $pet->status == 'needs_booster')
                                            <span
                                                class="badge rounded-pill bg-warning-subtle text-warning border border-warning px-3">
                                                Booster Due
                                            </span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-success-subtle text-success border border-success px-3">
                                                Active
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-md-center pe-md-4" data-label="Actions">
                                        @if($view === 'archived')
                                            @if($pet->trashed())
                                                <div class="d-flex flex-wrap gap-1">
                                                    <form action="{{ route('admin.pets.restore', $pet->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold">
                                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.pets.force-delete', $pet->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm fw-bold">
                                                            <i class="bi bi-trash"></i> Final Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($pet->status === 'DECEASED' || $pet->status === 'INACTIVE')
                                                <div class="d-flex flex-wrap gap-1">
                                                    <form action="{{ route('admin.pets.restore-deceased', $pet->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold">
                                                            <i class="bi bi-heart-fill"></i> Recover
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.pets.force-delete', $pet->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm fw-bold">
                                                            <i class="bi bi-trash"></i> Final Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm fw-medium w-100 w-md-auto"
                                                    type="button" data-bs-toggle="dropdown">
                                                    Manage <i class="bi bi-three-dots-vertical ms-1"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                    <li>
                                                        <a class="dropdown-item py-2 fw-bold text-primary" href="#"
                                                            data-bs-toggle="modal" data-bs-target="#viewPetModal{{ $pet->id }}">
                                                            <i class="bi bi-eye me-2"></i> View Profile
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#editPetModal{{ $pet->id }}">
                                                            <i class="bi bi-pencil me-2 text-warning"></i> Update Pet
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2"
                                                            href="{{ route('admin.vaccination-status', ['search' => $pet->name]) }}">
                                                            <i class="bi bi-shield-check me-2 text-success"></i> Vax Status
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2 text-danger" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#deletePetModal{{ $pet->id }}">
                                                            <i class="bi bi-trash3 me-2"></i> Delete Record
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-database-exclamation mb-2 fs-1 d-block"></i>
                                        No pet records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($pets, 'hasPages') && $pets->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $pets->links() }}
                </div>
            @endif
        </div>
    </div>
    @foreach ($pets as $pet)
        @include('partials._pet_modal')
        @include('partials._view_pet_modal')
    @endforeach

    <!-- Add Pet Modal -->
    <div class="modal fade" id="addPetModal" tabindex="-1" aria-labelledby="addPetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background: #2c3e50;">
                    <h5 class="modal-title fw-bold" id="addPetModalLabel">Register New Pet</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pets.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Select Owner</label>
                                <select name="user_id" class="form-select bg-light" required>
                                    <option value="" selected disabled>-- Select Verified Owner --</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->email }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">You must <a href="{{ route('admin.owners') }}"
                                        class="text-primary text-decoration-none">register an owner</a> first before adding
                                    a pet.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pet Name</label>
                                <input type="text" class="form-control bg-light" name="name" required
                                    placeholder="e.g., Bella" value="{{ old('name') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Species</label>
                                <select name="species" id="speciesSelect" class="form-select bg-light" required>
                                    <option value="" selected disabled>Select Species</option>
                                    <option value="Dog" {{ old('species') == 'Dog' ? 'selected' : '' }}>Dog</option>
                                    <option value="Cat" {{ old('species') == 'Cat' ? 'selected' : '' }}>Cat</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Breed / Color</label>
                                <select name="breed" id="breedSelect" class="form-select bg-light" required disabled>
                                    <option value="" selected disabled>Select Breed</option>
                                </select>
                                <input type="text" class="form-control bg-light mt-2 d-none" name="other_breed"
                                    id="otherBreedInput" placeholder="Please specify breed"
                                    value="{{ old('other_breed') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Birthdate</label>
                                <input type="date" class="form-control bg-light" name="birthdate" required
                                    value="{{ old('birthdate') }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background:#ff6b6b;">Register
                            Pet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dynamic Breed Selection Logic
            const speciesSelect = document.getElementById('speciesSelect');
            const breedSelect = document.getElementById('breedSelect');
            const otherBreedInput = document.getElementById('otherBreedInput');

            const dogBreeds = [
                'Aspin', 'Shih Tzu', 'Pomeranian', 'Pug', 'Chihuahua',
                'Golden Retriever', 'Labrador Retriever', 'Siberian Husky',
                'German Shepherd', 'Poodle', 'Beagle', 'Bulldog', 'Rottweiler',
                'Dachshund', 'Yorkshire Terrier', 'Boxer', 'Doberman Pinscher',
                'Corgi', 'Maltese', 'Bichon Frise', 'Chow Chow', 'Dalmatian', 'Other'
            ];

            const catBreeds = [
                'Puspin', 'Persian', 'Siamese', 'Maine Coon', 'Ragdoll',
                'British Shorthair', 'Sphynx', 'Abyssinian', 'Scottish Fold',
                'Russian Blue', 'Bengal', 'American Shorthair', 'Himalayan',
                'Norwegian Forest Cat', 'Oriental Shorthair', 'Other'
            ];

            if (speciesSelect) {
                speciesSelect.addEventListener('change', function () {
                    const selectedSpecies = this.value;
                    breedSelect.innerHTML = '<option value="" selected disabled>Select Breed</option>';
                    otherBreedInput.classList.add('d-none');

                    // Ensure the select element gets the main 'breed' attribute
                    breedSelect.name = 'breed';
                    otherBreedInput.name = 'other_breed';
                    otherBreedInput.required = false;

                    let breeds = selectedSpecies === 'Dog' ? dogBreeds : (selectedSpecies === 'Cat' ? catBreeds : []);

                    if (breeds.length > 0) {
                        breedSelect.disabled = false;
                        breeds.forEach(breed => {
                            const option = document.createElement('option');
                            option.value = breed;
                            option.textContent = breed;
                            breedSelect.appendChild(option);
                        });
                    } else {
                        breedSelect.disabled = true;
                    }
                });
            }

            if (breedSelect) {
                breedSelect.addEventListener('change', function () {
                    if (this.value === 'Other') {
                        otherBreedInput.classList.remove('d-none');
                        otherBreedInput.required = true;
                        // Swap the 'name' attribute so the controller catches the manual input seamlessly
                        breedSelect.name = 'breed_dropdown';
                        otherBreedInput.name = 'breed';
                    } else {
                        otherBreedInput.classList.add('d-none');
                        otherBreedInput.required = false;
                        breedSelect.name = 'breed';
                        otherBreedInput.name = 'other_breed';
                    }
                });
            }

            // --- NEW: Admin Scanner Auto-Modal Logic ---
            // If the URL has a search/pet_id parameter (from a scan), automatically open the pet modal
            const urlParams = new URLSearchParams(window.location.search);
            const petIdParam = urlParams.get('pet_id') || urlParams.get('general_search');

            if (petIdParam) {
                // Find the first Manage button's dropdown and the "View Profile" link
                // We assume the first row is the match for a scan
                const firstViewBtn = document.querySelector('[data-bs-target^="#viewPetModal"]');
                if (firstViewBtn) {
                    setTimeout(() => {
                        firstViewBtn.click();
                    }, 500); // Small delay for rendering
                }
            }
        });
    </script>
@endpush
