@extends('index')

@section('content')
    <div class="container" style="margin-top: 150px; margin-bottom: 100px;">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="section-title">
                    <h2>Quick Monitor Scan</h2>
                    <p>Scan the pet's QR code to instantly view health and vaccination records without logging in.</p>
                </div>

                <div class="card border-0 shadow-lg rounded-xl overflow-hidden mb-4">
                    <div class="card-body p-0">
                        <div id="reader" style="width: 100%; display:none; border: none !important;"></div>

                        <div id="scan-placeholder" class="py-5 bg-white">
                            <div class="mb-4">
                                <img src="{{ asset('assets/images/funfact/scanner.png') }}"
                                    style="width: 100px; opacity: 0.5;">
                            </div>
                            <h4 class="font-weight-bold">Ready to Scan</h4>
                            <p class="text-muted px-4">Allow camera access to start scanning the pet's Digital ID card.</p>
                        </div>
                    </div>
                </div>

                <button id="startScanBtn" class="theme-btn" style="width: 100%; border: none;">
                    START SCANNER
                </button>

                <div id="scanning-status" class="mt-3 text-primary font-weight-bold" style="display:none;">
                    <span class="spinner-grow spinner-grow-sm mr-2" role="status" aria-hidden="true"></span>
                    SCANNING IN PROGRESS...
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const qrConfig = { fps: 15, qrbox: { width: 250, height: 250 } };

        document.getElementById('startScanBtn').addEventListener('click', () => {
            document.getElementById('reader').style.display = 'block';
            document.getElementById('scan-placeholder').style.display = 'none';
            document.getElementById('startScanBtn').style.display = 'none';
            document.getElementById('scanning-status').style.display = 'block';

            html5QrCode.start(
                { facingMode: "environment" },
                qrConfig,
                (decodedText) => {
                    // Success: Redirect to the URL in the QR code or handle partial ID
                    html5QrCode.stop().then(() => {
                        // QR usually contains a URL like http://.../p/PC-XXXX
                        // If it's a URL, go there. If it's just an ID, redirect to public profile.
                        if (decodedText.includes('/p/')) {
                            window.location.href = decodedText;
                        } else {
                            window.location.href = "{{ url('/p') }}/" + decodedText;
                        }
                    });
                },
                (errorMessage) => {
                    // Ignore failures
                }
            ).catch((err) => {
                alert("Camera access denied or error: " + err);
                location.reload();
            });
        });
    </script>
@endsection