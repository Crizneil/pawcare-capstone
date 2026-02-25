@extends('layouts.admin')

@section('page_title', 'Pet Records Dashboard')

@section('content')
    <div class="container-fluid p-4 fade-in">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Pet Database</h2>
                <p class="text-muted small mb-0">Manage registry and vaccination records for all pets.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4 p-3">
            <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light rounded-start-pill ps-4">
                            <i class="bi bi-qr-code-scan text-muted"></i>
                        </span>
                        <input type="text" name="pet_id" value="{{ request('pet_id') }}"
                            class="form-control border-0 bg-light py-2" placeholder="Scan/Type Pet ID..." autofocus>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light ps-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control border-0 bg-light py-2" placeholder="Name, breed, or owner...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-orange w-100 rounded-pill py-2 fw-bold shadow-sm">Search</button>
                </div>
                <div class="col-md-2">
                    @if(request('pet_id') || request('search'))
                        <a href="{{ route('admin.pet-records') }}"
                            class="btn btn-light w-100 rounded-pill py-2 border shadow-sm">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
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
                                    {{-- Styled Pet ID --}}
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border">#{{ $pet->id }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="fw-bold text-dark">{{ $pet->name }}</div>
                                        <small class="text-muted">{{ $pet->breed ?? 'Hybrid' }}</small>
                                    </td>

                                    <td>
                                        <span class="badge bg-blue-light text-primary text-capitalizel">
                                            {{ $pet->type ?? $pet->species ?? 'Dog' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="text-dark fw-medium">{{ $pet->user->name ?? 'Unassigned' }}</div>
                                        <div class="text-muted small">{{ $pet->user->phone ?? 'No Phone' }}</div>
                                    </td>

                                    <td>
                                        @if(isset($pet->status) && $pet->status == 'needs_booster')
                                            <span
                                                class="badge rounded-pill bg-warning-subtle text-warning border border-warning px-3">
                                                Booster Due
                                            </span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-success-subtle text-success border border-success px-3">
                                                Updated
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm fw-medium"
                                                type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                                Manage <i class="bi bi-three-dots-vertical ms-1"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                {{-- Edit Action --}}
                                                <li>
                                                    <a class="dropdown-item py-2" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editPetModal{{ $pet->id }}">
                                                        <i class="bi bi-pencil me-2 text-warning"></i> Update Pet
                                                    </a>
                                                </li>

                                                {{-- Vax Status Action --}}
                                                <li>
                                                    <a class="dropdown-item py-2"
                                                        href="{{ route('admin.vaccination-status', ['search' => $pet->name]) }}">
                                                        <i class="bi bi-shield-check me-2 text-success"></i> Vax Status
                                                    </a>
                                                </li>

                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>

                                                {{-- Delete Action --}}
                                                <li>
                                                    <a class="dropdown-item py-2 text-danger" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#deletePetModal{{ $pet->id }}">
                                                        <i class="bi bi-trash3 me-2"></i> Delete Record
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
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
    @endforeach
@endsection