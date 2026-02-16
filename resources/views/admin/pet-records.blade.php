@extends('layouts.dashboard')

@section('content')
    <div class="admin-records-page">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h1 class="font-weight-bold text-dark mb-1" style="font-size: 2rem;">Pet Database</h1>
                    <p class="text-muted mb-0">Manage registry and vaccination records for all pets.</p>
                </div>
                <div class="col-md-6 text-md-right mt-3 mt-md-0">
                    <form action="{{ url()->current() }}" method="GET" class="d-inline-block w-100 w-md-auto">
                        <div class="input-group mb-0 shadow-sm rounded-pill overflow-hidden">
                            <input type="text" name="search" class="form-control border-0 px-4"
                                placeholder="Search Pet ID or Name..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                @if(session('success'))
                    <div class="alert alert-success border-0 rounded-0 mb-0 p-3 d-flex align-items-center">
                        <i class="fa fa-check-circle mr-3" style="font-size: 1.2rem;"></i>
                        <span class="font-weight-bold">{{ session('success') }}</span>
                        <button type="button" class="close ml-auto" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead style="background-color: #f8fafc; border-bottom: 1px solid #edf2f7;">
                            <tr>
                                <th class="py-3 pl-4 text-muted font-weight-bold text-uppercase small">Pet ID</th>
                                <th class="py-3 text-muted font-weight-bold text-uppercase small">Pet Info</th>
                                <th class="py-3 text-muted font-weight-bold text-uppercase small">Type</th>
                                <th class="py-3 text-muted font-weight-bold text-uppercase small">Owner</th>
                                <th class="py-3 text-muted font-weight-bold text-uppercase small">Vax Status</th>
                                <th class="py-3 pr-4 text-right text-muted font-weight-bold text-uppercase small">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($pets as $pet)
                                            <tr class="record-row">
                                                <td class="py-4 pl-4 font-weight-bold text-primary">{{ $pet->unique_id }}</td>
                                                <td class="py-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            <img src="{{ $pet->image_url ?? 'https://ui-avatars.com/api/?name=' . $pet->name }}"
                                                                class="rounded-circle"
                                                                style="width: 40px; height: 40px; object-fit: cover;">
                                                        </div>
                                                        <div>
                                                            <span class="d-block font-weight-bold text-dark mb-0">{{ $pet->name }}</span>
                                                            <small class="text-muted">{{ $pet->gender ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4">
                                                    <span class="badge badge-light border rounded-pill px-2 py-1 text-dark">
                                                        {{ $pet->species }}
                                                    </span>
                                                    <div class="small text-muted mt-1">{{ $pet->breed ?? 'N/A' }}</div>
                                                </td>
                                                <td class="py-4">
                                                    <div class="font-weight-bold text-dark mb-0">{{ $pet->user->name }}</div>
                                                    <small class="text-muted"><i class="fa fa-map-marker"></i>
                                                        {{ $pet->user->city ?? 'No City' }}</small>
                                                </td>
                                                <td class="py-4">
                                                    @if($pet->vaccinations->isNotEmpty())
                                                        <div class="d-flex align-items-center text-success mb-1">
                                                            <i class="fa fa-shield mr-2"></i>
                                                            <span class="font-weight-bold small text-uppercase">Vaccinated</span>
                                                        </div>
                                                        <div class="small text-muted">
                                                            Next:
                                                            {{ $pet->vaccinations->last()->next_due_at ? \Carbon\Carbon::parse($pet->vaccinations->last()->next_due_at)->format('M d, Y') : 'N/A' }}
                                                        </div>
                                                    @else
                                                        <div class="d-flex align-items-center text-danger">
                                                            <i class="fa fa-warning mr-2"></i>
                                                            <span class="font-weight-bold small text-uppercase">No Record</span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="py-4 pr-4 text-right">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mr-2 px-3"
                                                            data-toggle="modal" data-target="#editPetModal{{ $pet->id }}"
                                                            data-bs-toggle="modal" data-bs-target="#editPetModal{{ $pet->id }}">
                                                            <i class="fa fa-edit mr-1"></i> EDIT
                                                        </button>
                                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3"
                                                            data-toggle="modal" data-target="#vaccinateModal{{ $pet->id }}"
                                                            data-bs-toggle="modal" data-bs-target="#vaccinateModal{{ $pet->id }}">
                                                            <i class="fa fa-plus mr-1"></i> VAX
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Pet Modal -->
                                            <div class="modal fade" id="editPetModal{{ $pet->id }}" tabindex="-1" role="dialog"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                        <div class="modal-header p-4 border-0 bg-light">
                                                            <h5 class="modal-title font-weight-bold text-dark">Edit Pet Information</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form action="{{ route('admin.pets.update', $pet->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body p-4">
                                                                <div class="form-group mb-3">
                                                                    <label class="text-muted small font-weight-bold text-uppercase">Pet
                                                                        Name</label>
                                                                    <input type="text" name="name"
                                                                        class="form-control rounded-pill bg-light border-0"
                                                                        value="{{ $pet->name }}" required>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div class="form-group mb-3">
                                                                            <label
                                                                                class="text-muted small font-weight-bold text-uppercase">Species</label>
                                                                            <input type="text" name="species"
                                                                                class="form-control rounded-pill bg-light border-0"
                                                                                value="{{ $pet->species }}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="form-group mb-3">
                                                                            <label
                                                                                class="text-muted small font-weight-bold text-uppercase">Breed</label>
                                                                            <input type="text" name="breed"
                                                                                class="form-control rounded-pill bg-light border-0"
                                                                                value="{{ $pet->breed }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group mb-0">
                                                                    <label
                                                                        class="text-muted small font-weight-bold text-uppercase">Birthdate</label>
                                                                    <input type="date" name="birthdate"
                                                                        class="form-control rounded-pill bg-light border-0"
                                                                        value="{{ $pet->birthdate }}">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer p-4 border-0">
                                                                <button type="button"
                                                                    class="btn btn-link text-muted font-weight-bold text-decoration-none"
                                                                    data-dismiss="modal" data-bs-dismiss="modal">CANCEL</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">SAVE
                                                                    CHANGES</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Vaccination Modal -->
                                            <div class="modal fade" id="vaccinateModal{{ $pet->id }}" tabindex="-1" role="dialog"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                        <div class="modal-header p-4 border-0" style="background-color: #f8fafc;">
                                                            <h5 class="modal-title font-weight-bold text-dark">Add Vaccine Record</h5>
                                                            <button type="button" class="close text-dark" data-dismiss="modal"
                                                                data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form action="{{ route('admin.pets.vaccinate', $pet->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body p-4">
                                                                <p class="text-muted mb-4">Recording vaccination for <span
                                                                        class="text-primary font-weight-bold">{{ $pet->name }}</span></p>

                                                                <div class="form-group mb-4">
                                                                    <label
                                                                        class="font-weight-bold text-dark small text-uppercase mb-2">Vaccine
                                                                        Name</label>
                                                                    <input type="text" name="vaccine_name"
                                                                        class="form-control rounded-pill bg-light border-0"
                                                                        placeholder="Brand or Type (e.g. anti-rabies)" required>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div class="form-group mb-4">
                                                                            <label
                                                                                class="font-weight-bold text-dark small text-uppercase mb-2">Price
                                                                                (₱)</label>
                                                                            <input type="number" step="0.01" name="price"
                                                                                class="form-control rounded-pill bg-light border-0"
                                                                                placeholder="0.00">
                                                                        </div>
                                                                        <label
                                                                            class="font-weight-bold text-dark small text-uppercase mb-2">Date
                                                                            Administered</label>
                                                                        <input type="date" name="administered_at"
                                                                            class="form-control rounded-pill bg-light border-0"
                                                                            value="{{ date('Y-m-d') }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group mb-4">
                                                                        <label
                                                                            class="font-weight-bold text-dark small text-uppercase mb-2">Next
                                                                            Due Date (Optional)</label>
                                                                        <input type="date" name="next_due_at"
                                                                            class="form-control rounded-pill bg-light border-0">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    <div class="modal-footer p-4 border-0 bg-light">
                                                        <button type="button"
                                                            class="btn btn-link text-muted font-weight-bold text-decoration-none"
                                                            data-dismiss="modal" data-bs-dismiss="modal">CANCEL</button>
                                                        <button type="submit"
                                                            class="btn btn-primary rounded-pill px-5 font-weight-bold shadow-sm">SAVE
                                                            RECORD</button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                </div>
                            @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <i class="fa fa-search text-muted mb-3" style="font-size: 3rem; opacity: 0.2;"></i>
                                <p class="text-muted h5">No pets found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
                </table>
            </div>

            @if($pets->hasPages())
                <div class="card-footer bg-white border-0 p-4">
                    <div class="d-flex justify-content-center">
                        {{ $pets->appends(['search' => request('search')])->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>

    <style>
        .record-row:hover {
            background-color: #fafbfc;
        }

        .modal-content {
            overflow: hidden;
        }

        .form-control:focus {
            box-shadow: none;
            background-color: #fff !important;
            border: 1px solid #FABE3C !important;
        }
    </style>
@endsection