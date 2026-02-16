@extends('layouts.dashboard')

@section('content')
    <!-- Include HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="container-fluid fade-in">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="font-weight-bold mb-0">Vet Dashboard</h2>
                <p class="text-muted">Manage patients and scan records.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="row">
            <!-- Scanner Section -->
            <div class="col-md-7 mb-4">
                <div class="card border-0 shadow-lg rounded-lg overflow-hidden h-100">
                    <div
                        class="card-header bg-dark text-white p-4 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold"><i data-lucide="scan" class="mr-2"></i> Quick Scan</h5>
                        <span class="badge badge-light">READY</span>
                    </div>
                    <div class="card-body p-5 text-center">
                        <div id="reader"
                            style="width: 100%; display:none; margin-bottom: 20px; border-radius: 15px; overflow:hidden;">
                        </div>

                        <div id="scan-placeholder" class="py-5">
                            <i data-lucide="qr-code" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
                            <h5 class="font-weight-bold text-dark">Scan Patient QR Code</h5>
                            <p class="text-muted">Point camera at the Digital Card to verify records instantly.</p>
                        </div>

                        <button id="startScanBtn"
                            class="btn btn-warning btn-lg rounded-pill font-weight-bold px-5 shadow-sm">
                            <i data-lucide="camera" class="mr-2"></i> START CAMERA
                        </button>
                        <button id="stopScanBtn"
                            class="btn btn-secondary btn-lg rounded-pill font-weight-bold px-5 shadow-sm"
                            style="display:none;">
                            STOP CAMERA
                        </button>

                        <div class="mt-4 pt-4 border-top">
                            <p class="small text-muted font-weight-bold uppercase mb-2">OR ENTER ID MANUALLY / USE HARDWARE
                                SCANNER</p>
                            <form action="{{ route('vet.search-pet') }}" method="POST" id="manualSearchForm">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="search" id="petSearchInput"
                                        class="form-control form-control-lg rounded-left-pill bg-light border-0"
                                        placeholder="PC-2026-XXXX" autocomplete="off" autofocus>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary rounded-right-pill px-4" type="submit">
                                            <i data-lucide="search"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">Tip: Focus the box above and use your hardware
                                    scanner.</small>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats / Actions -->
            <div class="col-md-5 mb-4">
                <div class="card border-0 shadow-sm rounded-lg p-4 mb-4">
                    <h5 class="font-weight-bold mb-4">Action Center</h5>
                    <div class="d-grid gap-3">
                        <a href="{{ route('vet.vaccinations') }}"
                            class="btn btn-outline-primary btn-lg btn-block text-left p-3 d-flex align-items-center rounded-lg">
                            <div class="bg-primary-light p-2 rounded mr-3 text-primary"><i data-lucide="boxes"></i></div>
                            <div>
                                <span class="d-block font-weight-bold">Check Vaccine Stocks and Prices</span>
                                <small class="text-muted">Manage inventory levels</small>
                            </div>
                        </a>
                        <a href="{{ route('vet.vaccination-status') }}"
                            class="btn btn-outline-success btn-lg btn-block text-left p-3 d-flex align-items-center rounded-lg">
                            <div class="bg-success-light p-2 rounded mr-3 text-success"><i data-lucide="syringe"></i></div>
                            <div>
                                <span class="d-block font-weight-bold">Vaccination Status</span>
                                <small class="text-muted">Track pet vax records</small>
                            </div>
                        </a>
                        <a href="{{ route('vet.pet-records') }}"
                            class="btn btn-outline-info btn-lg btn-block text-left p-3 d-flex align-items-center rounded-lg">
                            <div class="bg-info-light p-2 rounded mr-3 text-info"><i data-lucide="file-text"></i></div>
                            <div>
                                <span class="d-block font-weight-bold">Search Database</span>
                                <small class="text-muted">Look up history by name</small>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="bg-primary text-white p-4 rounded-lg shadow-sm">
                    <h5 class="font-weight-bold mb-2">Tips</h5>
                    <p class="small opacity-75">Ensure the QR code is well-lit for faster scanning. You can also search by
                        Owner Name in the Database view.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };

        document.getElementById('startScanBtn').addEventListener('click', () => {
            document.getElementById('reader').style.display = 'block';
            document.getElementById('scan-placeholder').style.display = 'none';
            document.getElementById('startScanBtn').style.display = 'none';
            document.getElementById('stopScanBtn').style.display = 'block';

            html5QrCode.start({ facingMode: "environment" }, qrConfig, onScanSuccess, onScanFailure);
        });

        document.getElementById('stopScanBtn').addEventListener('click', () => {
            html5QrCode.stop().then((ignore) => {
                document.getElementById('reader').style.display = 'none';
                document.getElementById('scan-placeholder').style.display = 'block';
                document.getElementById('startScanBtn').style.display = 'block';
                document.getElementById('stopScanBtn').style.display = 'none';
            }).catch((err) => {
                console.log("Stop failed: ", err);
            });
        });

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Code matched = ${decodedText}`, decodedResult);

            // Extract ID if it's a URL
            let petId = decodedText;
            if (decodedText.includes('/p/')) {
                petId = decodedText.split('/p/').pop();
            }

            document.getElementById('petSearchInput').value = petId;

            html5QrCode.stop().then((ignore) => {
                document.getElementById('reader').style.display = 'none';
                document.getElementById('manualSearchForm').submit();
            });
        }

        // Handle hardware scanner input which might be a full URL
        document.getElementById('petSearchInput').addEventListener('input', function (e) {
            if (this.value.includes('/p/')) {
                let petId = this.value.split('/p/').pop();
                this.value = petId;
                document.getElementById('manualSearchForm').submit();
            }
        });

        function onScanFailure(error) {
        }
    </script>
@endsection