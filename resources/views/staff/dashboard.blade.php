@extends('layout.admin')

@section('content')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div class="container-fluid p-4 fade-in">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Staff Dashboard</h2>
            <p class="text-muted">Welcome back! Here's what's happening today.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                <i data-lucide="calendar" class="me-1" style="width: 14px;"></i> {{ now()->format('M d, Y') }}
            </span>
        </div>
    </div>

    {{-- 1. Top Row Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-light p-3 rounded-3 text-primary me-3">
                        <i data-lucide="calendar-check"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $appointmentsToday->count() }}</h4>
                        <small class="text-muted">Today's Appointments</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-light p-3 rounded-3 text-warning me-3">
                        <i data-lucide="alert-triangle"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $dueForVaccination->count() }}</h4>
                        <small class="text-muted">Pets Due for Vax</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-danger-light p-3 rounded-3 text-danger me-3">
                        <i data-lucide="package"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-danger">{{ $lowStock->count() }}</h4>
                        <small class="text-muted">Low Stock Alerts</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-success-light p-3 rounded-3 text-success me-3">
                        <i data-lucide="check-circle"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $recentVaccinations->count() }}</h4>
                        <small class="text-muted">Recently Vaccinated</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left Column: Scanner & Inventory --}}
        <div class="col-lg-5">
            {{-- Scanner Section --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="mb-0 small fw-bold text-white"><i data-lucide="scan" class="me-2"></i> Quick Scan Pet QR</h5>
                    <span class="badge bg-success">READY</span>
                </div>
                <div class="card-body p-4 text-center">
                    <div id="reader" style="width: 100%; display:none; margin-bottom: 20px; border-radius: 15px; overflow:hidden;"></div>
                    <div id="scan-placeholder" class="py-4">
                        <i data-lucide="qr-code" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
                        <h6 class="fw-bold text-dark">Scan Digital Card</h6>
                        <button id="startScanBtn" class="btn btn-warning rounded-pill fw-bold px-4 shadow-sm mt-2">
                            <i data-lucide="camera" class="me-2"></i> START CAMERA
                        </button>
                    </div>
                    <button id="stopScanBtn" class="btn btn-secondary rounded-pill fw-bold px-4 shadow-sm" style="display:none;">STOP CAMERA</button>
                </div>
            </div>

            {{-- Low Stock Alert List --}}
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3"><i data-lucide="alert-circle" class="me-2 text-danger"></i>Inventory Alerts</h6>
                @forelse($lowStock as $item)
                    <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded-3">
                        <span class="small fw-semibold">{{ $item->name }}</span>
                        <span class="badge bg-danger rounded-pill">{{ $item->stock }} left</span>
                    </div>
                @empty
                    <p class="text-muted small">All stock levels are healthy.</p>
                @endforelse
            </div>
        </div>

        {{-- Right Column: Appointments & Recent Activity --}}
        <div class="col-lg-7">
            {{-- Today's Appointments --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Today's Schedule</h6>
                        <a href="{{ route('staff.appointments') }}" class="small text-decoration-none">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pet / Owner</th>
                                    <th>Service</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointmentsToday as $apt)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $apt->pet_name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $apt->user->name ?? 'Guest' }}</div>
                                    </td>
                                    <td><span class="badge bg-blue-light text-primary">{{ $apt->service_type }}</span></td>
                                    <td>{{ date('h:i A', strtotime($apt->appointment_time)) }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('staff.appointments.update', [$apt->id, 'Done']) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success rounded-pill px-3">Check-in</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">No appointments for today.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recently Vaccinated --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Recent Activity</h6>
                    @foreach($recentVaccinations as $vax)
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded-circle p-2 me-3">
                            <i data-lucide="syringe" class="text-success" style="width: 16px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 small fw-bold text-dark">{{ $vax->pet->name }} received {{ $vax->vaccine_name }}</p>
                            <small class="text-muted">{{ $vax->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Scanner Script --}}
<script>
    const html5QrCode = new Html5Qrcode("reader");
    const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };
    const startBtn = document.getElementById('startScanBtn');
    const stopBtn = document.getElementById('stopScanBtn');
    const reader = document.getElementById('reader');
    const placeholder = document.getElementById('scan-placeholder');

    if(startBtn) {
        startBtn.addEventListener('click', () => {
            reader.style.display = 'block';
            placeholder.style.display = 'none';
            startBtn.style.display = 'none';
            stopBtn.style.display = 'block';
            html5QrCode.start({ facingMode: "environment" }, qrConfig, onScanSuccess);
        });
    }

    if(stopBtn) {
        stopBtn.addEventListener('click', () => {
            html5QrCode.stop().then(() => {
                reader.style.display = 'none';
                placeholder.style.display = 'block';
                startBtn.style.display = 'block';
                stopBtn.style.display = 'none';
            });
        });
    }

    function onScanSuccess(decodedText) {
    // Stop the scanner once a code is found to prevent multiple redirects
    html5QrCode.stop().then(() => {
        // Show a quick loading state (optional)
        console.log("Scanned URL: " + decodedText);

        // Logic to extract the ID from the URL: .../verify-pet/PAW-12345
        let petId = '';

        if (decodedText.includes('/verify-pet/')) {
            petId = decodedText.split('/verify-pet/').pop();
        } else {
            // Fallback if the QR just contains the raw ID
            petId = decodedText;
        }

        // Redirect to Staff Pet Records with the search parameter
        // Your controller's petRecords method should handle the 'search' query
        window.location.href = "{{ route('staff.pet-records') }}?search=" + petId;
    }).catch((err) => {
        console.error("Failed to stop scanner", err);
    });
}
</script>
@endsection
