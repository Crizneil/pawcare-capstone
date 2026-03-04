@extends('layout.admin')

@section('content')
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Pet Owners</h2>
            <button type="button" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addOwnerModal">
                <i class="fi flaticon-plus me-2"></i> Add New Owner
            </button>
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

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($owners as $owner)
                            <tr>
                                <td class="ps-4"><b>{{ $owner->name }}</b></td>
                                <td>{{ $owner->email }}</td>
                                <td><span class="badge bg-success rounded-pill">Active</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">No owners found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Owner Modal -->
    <div class="modal fade" id="addOwnerModal" tabindex="-1" aria-labelledby="addOwnerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background: #2c3e50;">
                    <h5 class="modal-title fw-bold" id="addOwnerModalLabel">Add New Pet Owner</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.owners.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0" style="background: #eef2f5; color: #333;">
                            <i class="fi flaticon-info me-2 text-primary"></i>
                            <strong>Note:</strong> Creating an account will automatically generate a secure password and
                            email it to the owner.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" class="form-control bg-light" name="name" required
                                    placeholder="e.g., Juan Dela Cruz" value="{{ old('name') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Valid ID Number</label>
                                <input type="text" class="form-control bg-light" name="valid_id_number" required
                                    placeholder="e.g., National ID / Voter's ID" value="{{ old('valid_id_number') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control bg-light" name="email" required
                                    placeholder="e.g., juan@example.com" value="{{ old('email') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mobile Number</label>
                                <input type="text" class="form-control bg-light" name="phone" required
                                    placeholder="e.g., 09123456789" value="{{ old('phone') }}">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-bold">Address (Unit, St, Brgy)</label>
                                <input type="text" class="form-control bg-light" name="address" required
                                    placeholder="Complete home address" value="{{ old('address') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Gender</label>
                                <select name="gender" class="form-select bg-light" required>
                                    <option value="" selected disabled>Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background:#ff6b6b;">Register
                            Owner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
