@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Vaccinations</h3>
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">+ New Vaccinations</a>
    </div>

    <div class="card shadow-sm">
        </div>
@endsection
