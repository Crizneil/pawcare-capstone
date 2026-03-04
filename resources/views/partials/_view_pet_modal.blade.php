<div class="modal fade" id="viewPetModal{{ $pet->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark ps-2 pt-2">Pet Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 px-4 pb-4">
                <div class="row">
                    <div class="col-md-4 text-center border-end">
                        <div class="mb-3 mt-3">
                            @if($pet->image_url)
                                <img src="{{ asset($pet->image_url) }}" class="rounded-4 shadow-sm border" style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-4 d-flex align-items-center justify-content-center border" style="height: 200px;">
                                    <i data-lucide="dog" class="text-muted" style="width: 60px; height: 60px;"></i>
                                </div>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-0">{{ $pet->name }}</h4>
                        <span class="badge bg-light text-dark border mb-3">#{{ $pet->id }}</span>

                        {{-- Update the Badge Section --}}
                        <div class="text-start mt-3">
                            <label class="small text-muted d-block text-uppercase fw-bold mb-1">Current Status</label>
                            <span class="badge rounded-pill {{ $pet->vax_status->class }} w-100 py-2 d-flex align-items-center justify-content-center" style="font-size: 0.85rem;">
                                {!! $pet->vax_status->icon !!} <span class="ms-2">{{ $pet->vax_status->label }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-8 ps-md-4 mt-3 mt-md-0">
                        <h6 class="fw-bold text-orange mb-3 mt-2"><i data-lucide="info" class="me-2" style="width:16px;"></i>Pet Profile</h6>
                        <div class="row mb-4">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Species</small>
                                <span class="fw-bold text-dark">{{ $pet->species ?? 'Dog' }}</span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Breed</small>
                                <span class="fw-bold text-dark">{{ $pet->breed ?? 'Unknown' }}</span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Gender</small>
                                <span class="fw-bold text-dark text-capitalize">{{ $pet->gender ?? 'N/A' }}</span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Date of Birth / Age</small>
                                <span class="fw-bold text-dark">
                                    {{ $pet->birthday ? \Carbon\Carbon::parse($pet->birthday)->format('M d, Y') : 'N/A' }}
                                    @if($pet->birthday)
                                        <small class="text-muted">({{ \Carbon\Carbon::parse($pet->birthday)->age }} yrs)</small>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <h6 class="fw-bold text-orange mb-3"><i data-lucide="user" class="me-2" style="width:16px;"></i>Owner Information</h6>
                        <div class="bg-light p-3 rounded-3 mb-4 border">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">Owner Name</small>
                                    <span class="fw-bold text-dark">{{ $pet->user->name ?? $pet->owner }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Contact Number</small>
                                    <span class="fw-bold text-dark">{{ $pet->user->phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-orange mb-3">
                            <i data-lucide="shield-check" class="me-2" style="width:16px;"></i>Vaccination History
                        </h6>

                        <div class="bg-light rounded-3 p-3 border">
                            <div class="vstack gap-2">
                                @forelse($pet->vaccinations->sortByDesc('date_administered') as $vax)
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 last-child-border-0">
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">
                                                <i data-lucide="check-circle-2" class="text-success me-1" style="width: 14px;"></i>
                                                {{ $vax->vaccine_name }}
                                            </span>
                                            {{-- Showing Administered Date here --}}
                                            <small class="text-muted">Date Administered: <span class="badge bg-white text-dark border rounded-pill px-3">{{ \Carbon\Carbon::parse($vax->date_administered)->format('M d, Y') }}</span></small>
                                        </div>

                                        {{-- Highlighting the Next Due Date in the Badge --}}
                                        <div class="text-end">
                                            <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700;">Next Due</small>
                                            <span class="badge bg-white text-dark border rounded-pill px-3">
                                                {{ \Carbon\Carbon::parse($vax->next_due_date)->format('m/d/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-2">
                                        <p class="text-muted small mb-0">No vaccination records found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
