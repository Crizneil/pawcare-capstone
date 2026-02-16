@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid fade-in">
        <div class="row mb-4">
            <div class="col-md-8">
                <!-- Stats Row -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm p-4 text-center">
                            <h6 class="text-muted text-uppercase font-weight-bold">Patients</h6>
                            <h2 class="display-4 font-weight-bold text-dark">{{ $totalPets }}</h2>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm p-4 text-center" style="border-left: 5px solid #d35400;">
                            <h6 class="text-primary text-uppercase font-weight-bold" style="color: #d35400 !important;">
                                Staff</h6>
                            <h2 class="display-4 font-weight-bold" style="color: #d35400 !important;">{{ $totalStaff }}</h2>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm rounded-lg mb-4">
                    <div class="d-flex align-items-center">
                        <i data-lucide="info" class="mr-3"></i>
                        <div>
                            <h5 class="font-weight-bold mb-1">System Status</h5>
                            <p class="mb-0">All systems operational. {{ $totalOwners }} registered owners.</p>
                        </div>
                    </div>
                </div>

                <!-- Scanner Section -->
                <div class="card border-0 shadow-sm rounded-lg p-4 mb-4 bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0 text-white"><i data-lucide="scan" class="mr-2"></i> Quick Patient
                            Scan</h5>
                        <span class="badge badge-warning">HARDWARE COMPATIBLE</span>
                    </div>
                    <form action="{{ route('vet.search-pet') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-lg border-0 bg-light"
                                placeholder="Scan QR or Enter ID..." autofocus autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-warning px-4" type="submit">SEARCH</button>
                            </div>
                        </div>
                        <small class="text-white-50 mt-2 d-block">Instant redirection to pet medical records upon
                            scan.</small>
                    </form>
                </div>

                <!-- Recent Activity / Quick Actions (Mark5 style) -->
                <div class="card border-0 shadow-sm rounded-lg p-4">
                    <h5 class="font-weight-bold mb-4">Quick Actions</h5>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.employees') }}"
                                class="btn btn-light btn-block p-3 text-left font-weight-bold">
                                <i data-lucide="user-plus" class="mr-2"></i> Add Staff
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.vaccination-status') }}"
                                class="btn btn-light btn-block p-3 text-left font-weight-bold">
                                <i data-lucide="syringe" class="mr-2"></i> Vax Status
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.vaccinations') }}"
                                class="btn btn-light btn-block p-3 text-left font-weight-bold">
                                <i data-lucide="boxes" class="mr-2"></i> Inventory
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.pet-records') }}"
                                class="btn btn-light btn-block p-3 text-left font-weight-bold">
                                <i data-lucide="search" class="mr-2"></i> Search Database
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enroll Form Sidebar -->
            <div class="col-md-4">
                <div class="card border-0 shadow-lg rounded-lg p-4">
                    <h4 class="font-weight-bold mb-4">Enroll Patient</h4>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Use a real route for enrollment -->
                    <form action="{{ route('admin.enroll') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <input type="text" name="owner_name" class="form-control rounded-pill bg-light border-0"
                                placeholder="Full Owner Name (New or Existing)" required>
                        </div>
                        <div class="form-group mb-3">
                            <input type="email" name="owner_email" class="form-control rounded-pill bg-light border-0"
                                placeholder="Owner Email (for account)" required>
                        </div>
                        <hr>
                        <div class="form-group mb-3">
                            <input type="text" name="pet_name" class="form-control rounded-pill bg-light border-0"
                                placeholder="Pet Name" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <select name="gender" class="form-control rounded-pill bg-light border-0">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" name="breed" class="form-control rounded-pill bg-light border-0"
                                    placeholder="Breed" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <select name="vaccine_type" class="form-control rounded-pill bg-light border-0">
                                <option value="Anti-Rabies">Anti-Rabies</option>
                                <option value="5-in-1">5-in-1</option>
                                <option value="6-in-1">6-in-1</option>
                                <option value="Deworming">Deworming</option>
                            </select>
                        </div>

                        <div class="form-group mb-3 text-center border rounded p-3 bg-light cursor-pointer"
                            onclick="document.getElementById('fileInput').click()">
                            <div class="mb-2"><i data-lucide="image" class="text-muted"></i></div>
                            <span class="text-primary font-weight-bold small">CHOOSE PROFILE PHOTO</span>
                            <input type="file" name="image" id="fileInput" accept="image/png, image/jpeg" class="d-none">
                        </div>

                        <div class="form-group mb-3">
                            <label class="small text-muted font-weight-bold ml-2">Next Due Date</label>
                            <input type="date" name="next_date" class="form-control rounded-pill bg-light border-0"
                                required>
                        </div>
                        <button type="submit"
                            class="btn btn-primary btn-block rounded-pill font-weight-bold w-100 py-3">REGISTER PET</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection