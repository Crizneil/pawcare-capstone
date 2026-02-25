@extends('layouts.admin')

@section('page_title', 'Vaccination History')

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0">Vaccination History</h2>
        <p class="text-muted small">Comprehensive record of your pets' immunizations.</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary text-uppercase small fw-bold">
                            <th class="ps-4 py-3">Pet Name</th>
                            <th>Vaccine</th>
                            <th>Date Administered</th>
                            <th>Next Due Date</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vaccinations as $record)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $record->pet->name }}</td>
                            <td>{{ $record->vaccine_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->date_administered)->format('M d, Y') }}</td>
                            <td>
                                <span class="text-primary fw-bold">
                                    {{ $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date)->format('M d, Y') : 'N/A' }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $record->remarks ?? '--' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No vaccination records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $vaccinations->links() }}
    </div>
</div>
@endsection
