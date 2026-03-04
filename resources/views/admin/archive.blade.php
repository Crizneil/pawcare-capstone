@extends('layout.admin')

@section('page_title', 'Archive Center')

@section('content')
    <div class="container-fluid p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Archive Center</h2>
                <p class="text-muted small mb-0">Restore or permanently remove deleted records.</p>
            </div>
        </div>

        {{-- Archive Tabs --}}
        <ul class="nav nav-pills mb-4 gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $tab === 'pets' ? 'active bg-orange' : 'bg-light text-dark' }}"
                    href="{{ route('admin.archive', ['tab' => 'pets']) }}">
                    <i data-lucide="dog" class="me-2 size-18"></i> Pets
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $tab === 'staff' ? 'active bg-orange' : 'bg-light text-dark' }}"
                    href="{{ route('admin.archive', ['tab' => 'staff']) }}">
                    <i data-lucide="users" class="me-2 size-18"></i> Staff
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $tab === 'vaccines' ? 'active bg-orange' : 'bg-light text-dark' }}"
                    href="{{ route('admin.archive', ['tab' => 'vaccines']) }}">
                    <i data-lucide="package" class="me-2 size-18"></i> Vaccines
                </a>
            </li>
        </ul>

        <div class="card shadow-sm border-0 rounded-4 mb-4 p-3">
            <form action="{{ route('admin.archive') }}" method="GET" class="row g-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="col-12 col-md-10">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light rounded-start-pill ps-3 ps-md-4">
                            <i data-lucide="search" class="text-muted size-18"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}"
                            class="form-control border-0 bg-light rounded-end-pill py-2"
                            placeholder="Search archived {{ $tab }}...">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <button class="btn btn-orange w-100 rounded-pill py-2 fw-bold shadow-sm">Search</button>
                </div>
            </form>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            @if($tab === 'pets')
                                <tr>
                                    <th class="ps-4">Pet Name</th>
                                    <th>Owner</th>
                                    <th>Breed</th>
                                    <th>Deleted At</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            @elseif($tab === 'staff')
                                <tr>
                                    <th class="ps-4">Name</th>
                                    <th>Email</th>
                                    <th>Deleted At</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            @elseif($tab === 'vaccines')
                                <tr>
                                    <th class="ps-4">Vaccine Name</th>
                                    <th>Batch No</th>
                                    <th>Stock</th>
                                    <th>Deleted At</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr>
                                    @if($tab === 'pets')
                                        <td class="ps-4 fw-bold text-dark">{{ $item->name }}</td>
                                        <td>{{ $item->user->name ?? 'Unknown' }}</td>
                                        <td>{{ $item->breed }}</td>
                                    @elseif($tab === 'staff')
                                        <td class="ps-4 fw-bold text-dark">{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                    @elseif($tab === 'vaccines')
                                        <td class="ps-4 fw-bold text-dark">{{ $item->name }}</td>
                                        <td>{{ $item->batch_no }}</td>
                                        <td>{{ $item->stock }}</td>
                                    @endif
                                    <td class="small text-muted">
                                        {{ $item->deleted_at->format('M d, Y • h:i A') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                            @php
                                                $restoreRoute = route("admin.{$tab}.restore", $item->id);
                                                $deleteRoute = route("admin.{$tab}.force-delete", $item->id);
                                            @endphp
                                            <form action="{{ $restoreRoute }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success px-3 border-0 rounded-0">
                                                    <i data-lucide="rotate-ccw" class="size-14 me-1"></i> Restore
                                                </button>
                                            </form>
                                            <form action="{{ $deleteRoute }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('PERMANENTLY DELETE this record? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger px-3 border-0 rounded-0">
                                                    <i data-lucide="trash-2" class="size-14 me-1"></i> Final Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i data-lucide="inbox" class="size-48 mb-2 opacity-25"></i>
                                        <p class="mb-0">No archived {{ $tab }} found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $data->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
