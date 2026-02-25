@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <h2 class="fw-bold">Pet Owners</h2>
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
                    <tr><td colspan="3" class="text-center py-4">No owners found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
