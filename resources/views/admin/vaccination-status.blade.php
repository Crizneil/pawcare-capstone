@extends('layouts.dashboard')

@section('content')
    <div class="vaccination-status-page">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h1 class="font-weight-bold text-dark mb-1" style="font-size: 2rem;">Pet Vaccination Status</h1>
                    <p class="text-muted mb-0">Track and update vaccination records for all pet patients.</p>
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

            <div class="row">
                <!-- Pet Vaccination Status -->
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="font-weight-bold mb-0 text-dark"><i class="fa fa-syringe mr-2 text-primary"></i>
                                Patient Records</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="py-3 pl-4 text-muted font-weight-bold text-uppercase small">Pet</th>
                                        <th class="py-3 text-muted font-weight-bold text-uppercase small">Owner</th>
                                        <th class="py-3 text-muted font-weight-bold text-uppercase small">Status</th>
                                        <th class="py-3 text-muted font-weight-bold text-uppercase small">Last Vaccination
                                        </th>
                                        <th class="py-3 pr-4 text-right text-muted font-weight-bold text-uppercase small">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pets as $pet)
                                        <tr>
                                            <td class="py-3 pl-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $pet->image_url ?? 'https://ui-avatars.com/api/?name=' . $pet->name }}"
                                                        class="rounded-circle mr-2" style="width: 30px; height: 30px;">
                                                    <span class="font-weight-bold text-dark">{{ $pet->name }}</span>
                                                </div>
                                                <div class="small text-muted">{{ $pet->unique_id }}</div>
                                            </td>
                                            <td class="py-3 text-dark font-weight-bold">{{ $pet->user->name }}</td>
                                            <td class="py-3">
                                                @if($pet->vaccinations->isNotEmpty())
                                                    <span class="badge badge-success rounded-pill px-3">VACCINATED</span>
                                                @else
                                                    <span class="badge badge-danger rounded-pill px-3">UNVACCINATED</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                @if($pet->vaccinations->isNotEmpty())
                                                    <div class="font-weight-bold text-primary">
                                                        {{ $pet->vaccinations->last()->vaccine_name }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($pet->vaccinations->last()->administered_at)->format('M d, Y') }}</small>
                                                @else
                                                    <span class="text-muted small">No record</span>
                                                @endif
                                            </td>
                                            <td class="py-3 pr-4 text-right">
                                                <button class="btn btn-primary btn-sm rounded-pill px-3" data-toggle="modal"
                                                    data-target="#vaccinateModal{{ $pet->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#vaccinateModal{{ $pet->id }}">
                                                    Update Vax
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Vaccination Modal -->
                                        <div class="modal fade" id="vaccinateModal{{ $pet->id }}" tabindex="-1" role="dialog"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                    <div class="modal-header p-4 border-0" style="background-color: #f8fafc;">
                                                        <h5 class="modal-title font-weight-bold text-dark">Record Vaccination
                                                        </h5>
                                                        <button type="button" class="close text-dark" data-dismiss="modal"
                                                            data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form action="{{ route('admin.pets.vaccinate', $pet->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-4">
                                                                <label
                                                                    class="font-weight-bold text-dark small text-uppercase mb-2">Vaccine
                                                                    Administered</label>
                                                                <input type="text" name="vaccine_name"
                                                                    class="form-control rounded-pill bg-light border-0"
                                                                    placeholder="Brand or Type" required>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group mb-0">
                                                                        <label
                                                                            class="font-weight-bold text-dark small text-uppercase mb-2">Date</label>
                                                                        <input type="date" name="administered_at"
                                                                            class="form-control rounded-pill bg-light border-0"
                                                                            value="{{ date('Y-m-d') }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group mb-0">
                                                                        <label
                                                                            class="font-weight-bold text-dark small text-uppercase mb-2">Next
                                                                            Due</label>
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
                                            <td colspan="5" class="text-center py-4">No pets found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 bg-light border-top">
                            {{ $pets->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection