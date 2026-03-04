@extends('layout.admin')

@section('page_title', 'Appointments Dashboard')

@section('content')
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-0">Appointment Availability</h2>
                <p class="text-muted small">Select an available date on the calendar below to set an appointment.</p>
            </div>
        </div>

        {{-- Master Appointment Calendar --}}
        <div class="card shadow-sm border-0 mb-3 p-3 rounded-4">
            <div id="userCalendar"></div>
        </div>
        {{-- Calendar Legend --}}
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4 small text-muted">
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="width: 12px; height: 12px; background-color: #e3f7e7;"></span>
                <span>All Slots Free (No Bookings)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="width: 12px; height: 12px; background-color: #fff7cc;"></span>
                <span>Limited Slots (Some Times Taken)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="width: 12px; height: 12px; background-color: #fde8e7;"></span>
                <span>Fully Booked / You Already Booked</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="width: 12px; height: 12px; background-color: #ececec;"></span>
                <span>Closed (Saturday / Sunday)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="width: 12px; height: 12px; background-color: #f8f9fa;"></span>
                <span>Past Day</span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h3 class="fw-bold mb-0">My Appointments</h3>
                <p class="text-muted small">Manage your scheduled healthcare visits here.</p>
            </div>
        </div>

        {{-- Appointment History Table --}}
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    {{-- Added custom-mobile-table class here --}}
                    <table class="table align-middle mb-0 custom-mobile-table">
                        <thead class="bg-light">
                            <tr class="text-secondary text-uppercase small fw-bold">
                                <th class="ps-4">Date & Time</th>
                                <th>Pet</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr>
                                    {{-- Added data-label to all <td> tags --}}
                                    <td class="ps-4" data-label="Date & Time">
                                        <div class="fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td data-label="Pet">
                                        <div class="fw-bold text-dark">{{ $appointment->pet_name }}</div>
                                        <div class="small text-muted">{{ ucfirst($appointment->species) }}</div>
                                    </td>
                                    <td data-label="Service">
                                        <div class="badge bg-info-subtle text-info border border-info px-3 rounded-pill">
                                            {{ ucfirst($appointment->service_type) }}
                                        </div>
                                    </td>
                                    <td data-label="Status">
                                        @php
                                            $statusStyles = [
                                                'pending' => 'bg-warning-subtle text-warning border-warning',
                                                'approved' => 'bg-primary-subtle text-primary border-primary',
                                                'completed' => 'bg-success-subtle text-success border-success',
                                                'done' => 'bg-success-subtle text-success border-success',
                                                'missed' => 'bg-danger-subtle text-danger border-danger',
                                                'cancelled' => 'bg-secondary-subtle text-secondary border-secondary',
                                                'rejected' => 'bg-danger-subtle text-danger border-danger',
                                                'rescheduled' => 'bg-light text-dark',
                                            ];
                                            $currentStyle = $statusStyles[strtolower($appointment->status)] ?? 'bg-light text-dark';
                                        @endphp
                                        <span class="badge rounded-pill border px-3 fw-bold {{ $currentStyle }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    {{-- Action label matches your CSS trigger --}}
                                    <td class="text-end pe-4" data-label="Actions">
                                        @if(in_array(strtolower($appointment->status), ['pending', 'rescheduled']))
                                            <form action="{{ route('pet-owner.appointments.cancel', $appointment->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3">Cancel</button>
                                            </form>

                                        @elseif(strtolower($appointment->status) === 'completed' || strtolower($appointment->status) === 'done')
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#viewResultModal{{ $appointment->id }}">
                                                View
                                            </button>
                                        @else
                                            <span class="text-muted small">Closed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <p class="text-muted mb-0">No appointments scheduled yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($appointments as $appointment)
        @if(strtolower($appointment->status) === 'completed' || strtolower($appointment->status) === 'done')
            @include('partials._view_vaccination_details_modal', ['appointment' => $appointment])
        @endif
    @endforeach

    {{-- Appointment Booking Modal --}}
    <div class="modal fade" id="setAppointmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="fw-bold text-dark m-0">Set an Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pet-owner.appointments.book') }}" method="POST" id="setAppointmentForm">
                    @csrf
                    <input type="hidden" name="appointment_date" id="appointment_date_input">
                    <div class="modal-body p-4 text-start">

                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-1">Appointment Schedule</label>
                            <h5 class="fw-bold text-primary mb-0" id="appointmentScheduleDisplay">Select a Date</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6 border-end-md">
                                <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">Owner Information</h6>
                                <div class="mb-3">
                                    <label class="small text-muted fw-bold mb-1">Name</label>
                                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}"
                                        readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted fw-bold mb-1">Contact #</label>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ auth()->user()->phone ?? 'N/A' }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted fw-bold mb-1">Email</label>
                                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->email }}"
                                        readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted fw-bold mb-1">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control bg-light" name="address" required
                                        rows="2" placeholder="Enter your full address here...">{{ auth()->user()->address ?? auth()->user()->location ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">Pet Information</h6>
                                <div class="mb-3">
                                    <label class="small fw-bold text-muted mb-1">Pet Type / Name <span
                                            class="text-danger">*</span></label>
                                    <select name="pet_id" class="form-select bg-light" required>
                                        <option value="">Please Select Pet here.</option>
                                        @foreach(\App\Models\Pet::where('user_id', auth()->id())
                                            ->whereIn('status', ['ACTIVE', 'Verified'])
                                            ->get() as $pet)
                                            <option value="{{ $pet->id }}">
                                                {{ $pet->name }} ({{ ucfirst($pet->species) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold text-muted mb-1">Time <span
                                            class="text-danger">*</span></label>
                                    <select name="appointment_time" id="appointment_time_select"
                                        class="form-select bg-light" required>
                                        <option value="">Select Time</option>
                                        <!-- Options injected via JS based on availability -->
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="small fw-bold text-muted mb-1">Service(s) <span
                                            class="text-danger">*</span></label>
                                    <select name="service_type" class="form-select bg-light" required>
                                        <option value="">Please Select Service(s) Here.</option>
                                        <option value="vaccination">Vaccination</option>
                                        <option value="checkup">General Checkup</option>
                                        <option value="consultation">Medical Consultation</option>
                                        <option value="deworming">Deworming</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary px-4 fw-bold" id="saveAppointmentBtn">Save</button>
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        /* --- Modern FullCalendar Aesthetics --- */
        #userCalendar {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #ffffff;
            border-radius: 16px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #f1f5f9;
        }

        .fc-scrollgrid {
            border: none !important;
        }

        .fc-day-today {
            background-color: #f8fafc !important;
        }

        .fc .fc-button-primary {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
            text-transform: capitalize;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }

        .fc .fc-button-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
            box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.3);
            transform: translateY(-1px);
        }

        .fc .fc-button-primary:not(:disabled):active,
        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .border-end-md {
            border-right: 1px dashed #e2e8f0;
        }

        @media (max-width: 768px) {
            .border-end-md {
                border-right: none;
                border-bottom: 1px dashed #e2e8f0;
                padding-bottom: 1.5rem;
                margin-bottom: 1.5rem;
            }
        }

        /* Available / Booked styles for background cells */
        .fc-day-available {
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f0fdf4 !important; /* Premium soft green */
        }

        .fc-day-available:hover {
            background-color: #dcfce7 !important;
            transform: scale(0.98);
            border-radius: 8px;
            box-shadow: inset 0 0 0 2px #4ade80;
        }

        .fc-day-limited {
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fefce8 !important; /* Premium soft yellow */
        }

        .fc-day-limited:hover {
            background-color: #fef08a !important;
            transform: scale(0.98);
            border-radius: 8px;
            box-shadow: inset 0 0 0 2px #facc15;
        }

        .fc-day-full {
            background-color: #fef2f2 !important;
            cursor: not-allowed;
        }

        .fc-day-passed {
            background-color: #f8fafc !important;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .fc-day-closed {
            background-color: #f1f5f9 !important;
            cursor: not-allowed;
            opacity: 0.7;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(0,0,0,0.03) 10px, rgba(0,0,0,0.03) 20px);
        }

        .fc-daygrid-day-number {
            padding: 12px;
            font-weight: 700;
            font-size: 1.15em;
            color: #475569;
        }

        /* The block indicator for slots */
        .availability-indicator {
            background-color: #10b981; /* emerald-500 */
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 6px 12px;
            margin: 8px auto 0;
            font-size: 0.75rem;
            font-weight: 700;
            max-width: 85%;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .availability-full {
            background-color: #ef4444; /* red-500 */
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
        }

        /* Modal Enhancements */
        .modal-content {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
        }

        .form-select.bg-light,
        .form-control.bg-light {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }

        .form-select.bg-light:focus,
        .form-control.bg-light:focus {
            background-color: #fff !important;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            var calendarEl = document.getElementById('userCalendar');
            var appointmentModal = new bootstrap.Modal(document.getElementById('setAppointmentModal'));
            var scheduleDisplay = document.getElementById('appointmentScheduleDisplay');
            var dateInput = document.getElementById('appointment_date_input');
            var timeSelect = document.getElementById('appointment_time_select');

            // Available times template - strictly defined clinic blocks
            const PREDEFINED_TIMES = [
                { label: "8:00 AM - 9:00 AM", value: "08:00" },
                { label: "9:00 AM - 10:00 AM", value: "09:00" },
                { label: "10:00 AM - 11:00 AM", value: "10:00" },
                { label: "11:00 AM - 12:00 PM (Final Hour for Morning Session)", value: "11:00" },
                { label: "1:00 PM - 2:00 PM", value: "13:00" },
                { label: "2:00 PM - 3:00 PM", value: "14:00" },
                { label: "3:00 PM - 4:00 PM", value: "15:00" }
            ];

            // Pre-compute the 24h versions of the predefined slots for easy comparison
            const PREDEFINED_TIMES_24 = PREDEFINED_TIMES.map(t => t.value);

            let availabilityData = {};
            let ownerBookedDates = [];
            let maxCapacity = 10;

            // Ensures +08:00 timezones map accurately to calendar dates and prevents `.toISOString()` shifting dates backwards by a day.
            const formatLocalToISODate = (dateObj) => {
                const year = dateObj.getFullYear();
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const day = String(dateObj.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            // Fetch availability first before rendering calendar
            async function fetchAvailability(startStr, endStr) {
                try {
                    const res = await fetch(`{{ route('pet-owner.api.available-slots') }}?start=${startStr}&end=${endStr}`);
                    const data = await res.json();
                    availabilityData = data.booked_slots || {};
                    ownerBookedDates = data.owner_booked_dates || [];
                    maxCapacity = data.max_capacity_per_day || 10;
                } catch (err) {
                    console.error("Failed to fetch slots", err);
                }
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'today prev,next',
                    center: 'title',
                    right: '' // Just month view per screenshot
                },
                height: 'auto',
                datesSet: async function (info) {
                    // When the calendar changes month, fetch data then force rerender
                    await fetchAvailability(info.startStr.split('T')[0], info.endStr.split('T')[0]);

                    // Simple hack to trigger dayCellDidMount again without reloading the whole object
                    let currentEvents = calendar.getEvents();
                    calendar.removeAllEvents();

                    // Add dummy background event to trigger view update, we handle UI in dayCellDidMount
                    calendar.addEvent({
                        title: 'render',
                        start: info.startStr,
                        display: 'none'
                    });
                },
                dayCellDidMount: function (info) {
                    const dateStr = formatLocalToISODate(info.date);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Grab potentially stored owner status payload
                    const ownerBookedInfo = ownerBookedDates[dateStr];
                    const isOwnerBooked = !!ownerBookedInfo;
                    const ownerStatus = ownerBookedInfo ? ownerBookedInfo.status : null;

                    // 0 = Sunday, 6 = Saturday
                    const dayOfWeek = info.date.getDay();
                    const isClosedDay = (dayOfWeek === 0 || dayOfWeek === 6);

                    if (info.date < today) {
                        info.el.classList.add('fc-day-passed');
                        return;
                    }

                    if (isClosedDay) {
                        info.el.classList.add('fc-day-closed');

                        // Clear previous indicators if re-mounted
                        let existInd = info.el.querySelector('.availability-indicator');
                        if (existInd) existInd.remove();

                        const indicator = document.createElement('div');
                        indicator.className = 'availability-indicator availability-full';
                        indicator.innerText = "Closed";
                        info.el.querySelector('.fc-daygrid-day-frame').appendChild(indicator);
                        return;
                    }

                    const bookedTimesRaw = availabilityData[dateStr] || [];
                    // Only consider bookings that fall within our exact defined clinic slots
                    const bookedTimes = bookedTimesRaw.filter(t => PREDEFINED_TIMES_24.includes(t));
                    const count = bookedTimes.length;

                    // Clear previous indicators if re-mounted
                    let existInd = info.el.querySelector('.availability-indicator');
                    if (existInd) existInd.remove();

                    const indicator = document.createElement('div');
                    indicator.className = 'availability-indicator';

                    const isFullyBooked = (count >= maxCapacity || count >= PREDEFINED_TIMES.length);

                    if (isOwnerBooked) {
                        if (ownerStatus === 'completed' || ownerStatus === 'done') {
                            info.el.classList.add('fc-day-available'); 
                            indicator.style.backgroundColor = '#10b981'; // Green
                            indicator.innerText = "Visit Done";
                        } else {
                            info.el.classList.add('fc-day-full');
                            indicator.classList.add('availability-full');
                            indicator.innerText = "You Already Booked";
                        }
                    } else if (isFullyBooked) {
                        info.el.classList.add('fc-day-full');
                        indicator.classList.add('availability-full');
                        indicator.innerText = "Fully Booked";
                    } else if (count === 0) {
                        // No bookings yet: all slots free (GREEN)
                        info.el.classList.add('fc-day-available');
                        indicator.innerText = "All Slots Free";
                    } else {
                        // Some bookings but not full: still available (YELLOW)
                        info.el.classList.add('fc-day-limited');
                        indicator.style.backgroundColor = '#f4b000'; // yellow pill
                        indicator.innerText = "Limited Slots";
                    }

                    info.el.querySelector('.fc-daygrid-day-frame').appendChild(indicator);
                },
                dateClick: function (info) {
                    const dateStr = info.dateStr;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Grab potentially stored owner status payload
                    const ownerBookedInfo = ownerBookedDates[dateStr];
                    const isOwnerBooked = !!ownerBookedInfo;
                    const ownerStatus = ownerBookedInfo ? ownerBookedInfo.status : null;

                    // If this owner already has an appointment on this day, trigger receipt or do nothing
                    if (isOwnerBooked) {
                        if (ownerStatus === 'completed' || ownerStatus === 'done') {
                            const modalId = '#viewResultModal' + ownerBookedInfo.id;
                            const receiptModalEl = document.querySelector(modalId);
                            if (receiptModalEl) {
                                const receiptModal = new bootstrap.Modal(receiptModalEl);
                                receiptModal.show();
                            }
                        }
                        return; // Always prevent opening the "book" modal if they have a booking
                    }

                    // 0 = Sunday, 6 = Saturday
                    const dayOfWeek = info.date.getDay();
                    const isClosedDay = (dayOfWeek === 0 || dayOfWeek === 6);

                    // If not viewing a receipt, block past dates and closed days from booking
                    if (info.date < today || isClosedDay) return;

                    const bookedTimesRaw = availabilityData[dateStr] || [];
                    const bookedTimes = bookedTimesRaw.filter(t => PREDEFINED_TIMES_24.includes(t));
                    if (bookedTimes.length >= maxCapacity || bookedTimes.length >= PREDEFINED_TIMES.length) {
                        return; // Ignore fully booked days
                    }

                    // Set Modal UI
                    const options = { year: 'numeric', month: 'long', day: 'numeric' };
                    scheduleDisplay.innerText = info.date.toLocaleDateString('default', options);
                    dateInput.value = dateStr;

                    // Populate time dropdown dynamically
                    timeSelect.innerHTML = '<option value=\"\">Select Time</option>';
                    PREDEFINED_TIMES.forEach(timeObj => {
                        const time24 = timeObj.value;
                        const timeLabel = timeObj.label;
                        let opt = document.createElement('option');

                        // If this specific time is already booked, show it as disabled/gray
                        if (bookedTimes.includes(time24)) {
                            opt.disabled = true;
                            opt.value = '';
                            opt.innerText = `${timeLabel} (Taken)`;
                            opt.style.color = '#6c757d';
                            opt.style.backgroundColor = '#f8f9fa';
                        } else {
                            opt.value = time24;
                            opt.innerText = timeLabel;
                        }

                        timeSelect.appendChild(opt);
                    });

                    // Open Modal
                    appointmentModal.show();
                }
            });

            // Force initial render immediately so cell mounting works on load
            const initialStart = new Date();
            initialStart.setDate(1); // load at least this month
            
            const startFormat = formatLocalToISODate(initialStart);
            const endFormat = formatLocalToISODate(new Date(initialStart.getFullYear(), initialStart.getMonth() + 1, 0));
            
            await fetchAvailability(startFormat, endFormat);
            calendar.render();
        });
    </script>
@endpush
