@extends('layouts.dashboard')

@section('content')
    <div class="vaccines-page">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h1 class="font-weight-bold text-dark mb-1" style="font-size: 2rem;">Vaccination Management</h1>
                    <p class="text-muted mb-0">Manage vaccine inventory and track pet vaccination status.</p>
                </div>
                <div class="col-md-6 text-md-right mt-3 mt-md-0">
                    <form action="{{ url()->current() }}" method="GET" class="d-inline-block w-100 w-md-auto">
                        <div class="input-group mb-0 shadow-sm rounded-pill overflow-hidden">
                            <input type="text" name="search" class="form-control border-0 px-4"
                                placeholder="Search Vaccine..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- Vaccine Inventory -->
                <div class="col-lg-12 mb-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="font-weight-bold mb-0 text-dark"><i class="fa fa-boxes mr-2 text-primary"></i>
                                Vaccine Inventory</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="py-3 pl-4 text-muted font-weight-bold text-uppercase small">Vaccine Name
                                        </th>
                                        <th class="py-3 text-muted font-weight-bold text-uppercase small">Stock</th>
                                        <th class="py-3 text-muted font-weight-bold text-uppercase small">Last Updated</th>
                                        <th class="py-3 pr-4 text-right text-muted font-weight-bold text-uppercase small">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vaccines as $vaccine)
                                        <tr>
                                            <td class="py-3 pl-4">
                                                <span class="font-weight-bold text-dark">{{ $vaccine->name }}</span>
                                                <div class="small text-muted">{{ Str::limit($vaccine->description, 40) }}</div>
                                            </td>
                                            <td class="py-3">
                                                @if($vaccine->stock < 20)
                                                    <span class="badge badge-soft-danger text-danger px-3 py-1 font-weight-bold">{{ $vaccine->stock }} Low</span>
                                                @else
                                                    <span class="badge badge-soft-success text-success px-3 py-1 font-weight-bold">{{ $vaccine->stock }} In Stock</span>
                                                @endif
                                            </td>
                                            <td class="py-3 font-weight-bold text-dark">₱ {{ number_format($vaccine->price, 2) }}</td>
                                            <td class="py-3 text-muted small">{{ $vaccine->updated_at->format('M d, Y') }}</td>
                                            <td class="py-3 pr-4 text-right">
                                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-toggle="modal" data-target="#editVaccineModal{{ $vaccine->id }}" data-bs-toggle="modal" data-bs-target="#editVaccineModal{{ $vaccine->id }}">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Edit Vaccine Modal -->
                                        <div class="modal fade" id="editVaccineModal{{ $vaccine->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                    <div class="modal-header p-4 border-0 bg-light">
                                                        <h5 class="modal-title font-weight-bold text-dark">Edit Vaccine Inventory</h5>
                                                        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form action="{{ route('admin.vaccines.update', $vaccine->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label class="text-muted small font-weight-bold text-uppercase">Vaccine Name</label>
                                                                <input type="text" name="name" class="form-control rounded-pill bg-light border-0" value="{{ $vaccine->name }}" required>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group mb-0">
                                                                        <label class="text-muted small font-weight-bold text-uppercase">Stock (Doses)</label>
                                                                        <input type="number" name="stock" class="form-control rounded-pill bg-light border-0" value="{{ $vaccine->stock }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group mb-0">
                                                                        <label class="text-muted small font-weight-bold text-uppercase">Price (₱)</label>
                                                                        <input type="number" step="0.01" name="price" class="form-control rounded-pill bg-light border-0" value="{{ $vaccine->price }}" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer p-4 border-0">
                                                            <button type="button" class="btn btn-link text-muted font-weight-bold text-decoration-none" data-dismiss="modal" data-bs-dismiss="modal">CANCEL</button>
                                                            <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">SAVE CHANGES</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No vaccines found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .badge-soft-danger {
            background-color: #fee2e2;
        }

        .badge-soft-success {
            background-color: #dcfce7;
        }

        .btn-outline-primary:hover {
            background-color: #FABE3C;
            border-color: #FABE3C;
            color: #fff;
        }
    </style>
@endsection