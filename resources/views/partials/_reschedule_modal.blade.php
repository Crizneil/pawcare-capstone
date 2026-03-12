<div class="modal fade" id="rescheduleModal{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-calendar-event-fill text-primary me-2"></i>Reschedule Visit
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('staff.appointments.reschedule', $appointment->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <input type="hidden" id="service_{{ $appointment->id }}" value="{{ $appointment->service_type }}">

                        <div class="col-12">
                            <label class="small fw-bold text-uppercase text-muted mb-2 d-block">New Appointment Date</label>
                            <input type="date" name="appointment_date" id="date_{{ $appointment->id }}"
                                   class="form-control rounded-pill border-light-subtle shadow-sm date-picker"
                                   value="{{ $appointment->appointment_date }}"
                                   min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Available Time Slots</label>
                            <select name="appointment_time" id="time_slot_{{ $appointment->id }}"
                                    class="form-select rounded-pill border-light-subtle shadow-sm" required>
                                <option value="">Select a date first</option>
                            </select>
                            <div id="loading_{{ $appointment->id }}" class="small text-primary mt-1" style="display:none;">
                                <span class="spinner-border spinner-border-sm"></span> Checking availability...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-orange rounded-pill px-4 fw-bold shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const appointmentId = "{{ $appointment->id }}";
    const dateInput = document.getElementById('date_' + appointmentId);
    const serviceType = document.getElementById('service_' + appointmentId).value;
    const timeSelect = document.getElementById('time_slot_' + appointmentId);
    const loader = document.getElementById('loading_' + appointmentId);

    const morningSlots = ["08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30"];
    const afternoonSlots = ["13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30"];
    const allSlots = [...morningSlots, ...afternoonSlots];

    function formatTimeDisplay(time) {
        const [h, m] = time.split(':');
        let hour = parseInt(h);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);

        // Calculate end time (30 mins later)
        let endM = parseInt(m) + 30;
        let endH = hour;
        if (endM === 60) { endM = "00"; endH++; }
        const displayEndH = endH > 12 ? endH - 12 : (endH === 0 ? 12 : endH);

        return `${displayHour}:${m} - ${displayEndH}:${endM.toString().padStart(2, '0')} ${ampm}`;
    }

    async function updateRescheduleSlots() {
        const date = dateInput.value;
        if (!date) return;

        loader.style.display = 'block';
        timeSelect.innerHTML = '<option value="" selected disabled>Loading slots...</option>';

        try {
            const response = await fetch("{{ route('staff.appointments.slots') }}?date=" + date);
            const bookedSlots = await response.json();

            timeSelect.innerHTML = '<option value="" selected disabled>Select time...</option>';

            allSlots.forEach((slot, index) => {
                const isBooked = bookedSlots.includes(slot);
                let isDisabled = isBooked;

                // Kapon Logic (Same as Walk-in)
                if (serviceType === 'kapon') {
                    const nextSlot = allSlots[index + 1];
                    const isNextBooked = nextSlot ? bookedSlots.includes(nextSlot) : true;
                    const isEndOfSession = (slot === "11:30" || slot === "16:30");

                    if (isNextBooked || isEndOfSession) {
                        isDisabled = true;
                    }
                }

                const option = document.createElement('option');
                option.value = slot;
                option.textContent = formatTimeDisplay(slot) + (isBooked ? ' (Full)' : '');
                option.disabled = isDisabled;
                timeSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error fetching slots:", error);
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
        } finally {
            loader.style.display = 'none';
        }
    }

    dateInput.addEventListener('change', updateRescheduleSlots);

    // Trigger once on load to show current day slots if date is already filled
    if(dateInput.value) updateRescheduleSlots();
})();
</script>
