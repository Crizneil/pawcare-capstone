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
                            @if($pet->image)
                                <img src="{{ asset('storage/' . $pet->image) }}" class="rounded-4 shadow-sm border"
                                    style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-4 d-flex align-items-center justify-content-center border"
                                    style="height: 200px;">
                                    <i data-lucide="dog" class="text-muted" style="width: 60px; height: 60px;"></i>
                                </div>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-0">{{ $pet->name }}</h4>
                        <span class="badge bg-light text-dark border mb-3">#{{ $pet->id }}</span>

                        <div class="text-start mt-3">
                            <label class="small text-muted d-block text-uppercase fw-bold mb-1">Current Status</label>
                            {{-- Updated to use the Model property with defensive checks --}}
                            @php
                                $vax = $pet->vax_status ?? (object) [
                                    'class' => 'bg-secondary-subtle text-secondary border-secondary',
                                    'label' => 'No Records',
                                    'icon' => '<i data-lucide="shield-off"></i>',
                                    'latest_vax' => null
                                ];
                            @endphp
                            <span
                                class="badge rounded-pill {{ $vax->class ?? 'bg-secondary' }} badge-vax-subtle w-100 py-2"
                                style="font-size: 0.85rem;">
                                {!! $vax->icon ?? '<i data-lucide="shield"></i>' !!} {{ $vax->label ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-8 ps-md-4 mt-3 mt-md-0">
                        <h6 class="fw-bold text-orange mb-3 mt-2"><i data-lucide="info" class="me-2"
                                style="width:16px;"></i>Pet Profile</h6>
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
                                    {{ $pet->dob ? \Carbon\Carbon::parse($pet->dob)->format('M d, Y') : ($pet->birthday ? \Carbon\Carbon::parse($pet->birthday)->format('M d, Y') : 'N/A') }}
                                    @php
                                        $dobField = $pet->dob ?? $pet->birthday;
                                    @endphp
                                    @if($dobField)
                                        <small class="text-muted">({{ \Carbon\Carbon::parse($dobField)->age }} yrs)</small>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <h6 class="fw-bold text-orange mb-3"><i data-lucide="user" class="me-2"
                                style="width:16px;"></i>Owner Information</h6>
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

                        <h6 class="fw-bold text-orange mb-3"><i data-lucide="shield-check" class="me-2"
                                style="width:16px;"></i>Vaccination Summary</h6>
                        <div class="row g-2">
                            {{-- Latest Vaccine Name --}}
                            <div class="col-12 mb-2">
                                <small class="text-muted d-block">Latest Vaccine Type</small>
                                <span
                                    class="fw-bold text-dark">{{ optional($vax->latest_vax)->vaccine_name ?? 'No vaccines recorded' }}</span>
                            </div>

                            {{-- Date Administered --}}
                            <div class="col-6">
                                <small class="text-muted d-block">Date Administered</small>
                                <span class="fw-bold small text-dark">
                                    {{ optional($vax->latest_vax)->date_administered ? \Carbon\Carbon::parse($vax->latest_vax->date_administered)->format('M d, Y') : '--' }}
                                </span>
                            </div>

                            {{-- Next Due Date --}}
                            <div class="col-6">
                                <small class="text-muted d-block">Next Due Date</small>
                                <span class="fw-bold small text-dark">
                                    {{ optional($vax->latest_vax)->next_due_date ? \Carbon\Carbon::parse($vax->latest_vax->next_due_date)->format('M d, Y') : '--' }}
                                </span>
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