<div class="modal fade" id="viewResultModal{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-check me-2"></i>Vaccination Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold">Pet Name</label>
                        <p class="fw-bold mb-0 text-dark">{{ $appointment->pet_name }}</p>
                    </div>

                    <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold">Owner</label>
                        <p class="fw-bold mb-0 text-dark">{{ $appointment->user->name ?? 'N/A' }}</p>
                    </div>

                    <div class="col-12"><hr class="my-1 opacity-25"></div>

                    <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold">Vaccine Given</label>
                        <p class="fw-bold mb-0 text-dark">
                            {{ $appointment->vaccination->vaccine_name
                                ?? $appointment->vaccine_name
                                ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold">Batch Number</label>
                        <p class="fw-bold mb-0 text-dark">{{ $appointment->batch_no ?? 'N/A' }}</p>
                    </div>

                    <div class="col-12 bg-light p-3 rounded-3">
                        <div class="row">
                            <div class="col-6">
                                <label class="small text-muted text-uppercase fw-bold">Date Administered</label>
                                <p class="fw-bold mb-0 text-dark small">
                                    {{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y') }}
                                </p>
                            </div>

                            <div class="col-6">
                                <label class="small text-muted text-uppercase fw-bold text-danger">Next Due Date</label>
                                <p class="mb-0 small fw-bold text-danger">
                                    {{ $appointment->next_due_date ? \Carbon\Carbon::parse($appointment->next_due_date)->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="small text-muted text-uppercase fw-bold">Administered By</label>
                        <p class="fw-bold mb-0 text-dark">
                            <i data-lucide="user-check" class="text-muted me-1" style="width:14px;"></i>
                            {{ $appointment->administered_by ?? 'Staff' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary w-100 rounded-pill" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>
