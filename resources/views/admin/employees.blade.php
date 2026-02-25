@extends('layouts.admin')

@section('page_title', 'Staff Dashboard')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Manage Vet Staff</h2>
            <p class="text-muted">Overview of all active veterinary and administrative personnel.</p>
        </div>

        <button class="btn btn-orange rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="bi bi-person-plus-fill me-2"></i> Add New Staff
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary text-uppercase small">
                            <th class="ps-4 py-3" style="width: 25%;">Name</th>
                            <th class="py-3" style="width: 25%;">Email Address</th>
                            <th class="py-3" style="width: 15%;">Role</th>
                            <th class="py-3" style="width: 15%;">Date Joined</th>
                            <th class="text-center py-3" style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $employee)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $employee->name }}</div>
                            </td>
                            <td class="text-muted">{{ $employee->email }}</td>
                            <td>
                                <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary px-3">
                                    {{ ucfirst($employee->role) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $employee->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4 action-cell">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-light border rounded-pill px-3 fw-medium shadow-sm"
                                            type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                        Manage <i class="bi bi-three-dots-vertical ms-1"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                        <li>
                                            <a class="dropdown-item py-2" href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editEmployeeModal{{ $employee->id }}">
                                                <i class="bi bi-pencil me-2 text-primary"></i> Edit Details
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 text-danger" href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteEmployeeModal{{ $employee->id }}">
                                                <i class="bi bi-trash me-2"></i> Delete Employee
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No staff members found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {{ $staff->links() }}
    </div>
</div>
    @foreach($staff as $employee)
        @include('partials._edit_staff_modal', ['employee' => $employee])
        @include('partials._delete_staff_modal', ['employee' => $employee])
    @endforeach

@include('partials._add_staff_modal')

@endsection
