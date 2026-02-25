@extends('layouts.admin')

@section('page_title', 'Pet Records | Staff')

@section('content')
<div class="container-fluid p-4 fade-in">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Pet Database</h2>
            <p class="text-muted">Manage registry and vaccination records for all pets.</p>
        </div>

        <div class="d-flex gap-2">
            {{-- Search Form --}}
            <form action="{{ route('staff.pet-records') }}" method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control border-0 shadow-sm rounded-pill px-3"
                           placeholder="Search Pet or Owner..." value="{{ request('search') }}" style="min-width: 250px;">
                    <button class="btn btn-orange rounded-pill ms-2 px-4 shadow-sm" type="submit">
                        <i data-lucide="search" style="width: 18px;"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary text-uppercase small fw-bold">
                            <th class="ps-4 py-3">Pet ID</th>
                            <th>Pet Info</th>
                            <th>Type</th>
                            <th>Owner</th>
                            <th>Vax Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pets as $pet)
                        {{-- This calls the logic you added to the Pet Model --}}
                        @php $status = $pet->vax_status; @endphp

                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border">#{{ $pet->id }}</span>
                            </td>

                            <td>
                                <div class="fw-bold text-dark">{{ $pet->name }}</div>
                                <small class="text-muted">{{ $pet->breed ?? 'Unknown Breed' }}</small>
                            </td>

                            <td>
                                <span class="badge bg-blue-light text-primary text-capitalize">{{ $pet->species ?? 'Dog' }}</span>
                            </td>

                            <td>
                                @if($pet->user_id)
                                    <div class="text-dark fw-bold">{{ $pet->user->name }}</div>
                                    <small class="text-muted"><i class="bi bi-person-check"></i> Member</small>
                                @else
                                    <div class="text-secondary fw-bold">{{ $pet->owner }}</div>
                                    <span class="badge bg-light text-secondary border small">Walk-in Client</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge rounded-pill border {{ $status->class }} px-3 py-2 fw-bold"
                                    style="font-size: 0.75rem; min-width: 110px; display: inline-block; text-align: center;">
                                    {{ $status->label }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm" type="button" data-bs-toggle="dropdown">
                                        Manage <i data-lucide="more-vertical" class="ms-1" style="width: 14px;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                                        <li>
                                            <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#viewPetModal{{ $pet->id }}">
                                                <i data-lucide="eye" class="me-2 text-primary" style="width: 16px;"></i> View Pet Profile
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item py-2 text-success" href="{{ route('staff.vaccination-history', ['pet_id' => $pet->id]) }}">
                                                <i data-lucide="history" class="me-2" style="width: 16px;"></i> Vax History
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i data-lucide="database" class="text-muted mb-2" style="width: 40px; height: 40px;"></i>
                                <p class="text-muted">No pet records found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pets->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $pets->links() }}
            </div>
        @endif
    </div>
</div>
@foreach ($pets as $pet)
    @include('partials._view_pet_modal')
@endforeach
@endsection
