<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OFFICIAL APPOINTMENT REPORT - {{ ucfirst($range) }}</title>
    <style>
        /* Standard Page Setup */
        @page {
            margin: 10mm 15mm 45mm 15mm; /* Increased bottom margin to 'reserve' space for footer */
        }

        body {
            font-family: 'Helvetica', sans-serif;
            color: #000;
            margin: 0;
            line-height: 1.4;
        }

        /* HEADER & TITLES */
        .official-header { text-align: center; margin-bottom: 20px; }
        .official-header p { margin: 0; font-size: 13px; }
        .official-header .republic { text-transform: uppercase; font-weight: bold; font-size: 15px; }
        .official-header .location { text-transform: uppercase; font-weight: bold; font-size: 13px; }
        .official-header .office { font-weight: bold; font-size: 17px; margin-top: 8px; display: block; }

        .report-title { text-align: center; font-weight: bold; font-size: 18px; text-transform: uppercase; text-decoration: underline; margin: 15px 0 5px 0; }
        .generation-meta { text-align: center; font-size: 11px; margin-bottom: 20px; }

        /* TABLE STYLING */
        table.appointment-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .appointment-table th { background-color: #f2f2f2; border: 1px solid #000; padding: 8px; font-size: 11px; text-align: left; text-transform: uppercase; }
        .appointment-table td { border: 1px solid #000; padding: 8px; font-size: 11px; }

        /* FIXED FOOTER - This handles both DomPDF and Browser Printing */
        .page-footer-container {
            position: fixed;
            bottom: 0; /* Align to the bottom of the printable area */
            left: 0;
            right: 0;
            width: 100%;
            height: 150px; /* Specific height for the signature section */
        }

        /* SIGNATURE BOXES */
        .sig-table { width: 100%; border: none; }
        .sig-box { width: 45%; text-align: center; border: none !important; vertical-align: bottom; }
        .sig-line { border-bottom: 1px solid #000; margin-top: 30px; margin-bottom: 5px; width: 80%; margin-left: auto; margin-right: auto; }
        .sig-name { font-weight: bold; text-transform: uppercase; font-size: 12px; margin: 0; }
        .footer-note { text-align: center; font-size: 9px; color: #555; border-top: 1px solid #ccc; padding-top: 8px; margin-top: 10px; width: 100%; }

        /* UTILITIES */
        .no-print { display: block; }

        @media print {
            .no-print { display: none !important; }
            .page-footer-container {
                position: fixed;
                bottom: 5mm; /* Slight lift for physical printers */
            }
        }
    </style>
</head>
<body>

@if(!$isPdf)
    <div class="no-print" style="text-align: right; padding: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #555; color: #fff; border: none; cursor: pointer; border-radius: 4px; font-weight: bold;">
            Print Report
        </button>
        <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}" style="padding: 10px 20px; background: #000; color: #fff; text-decoration: none; margin-left: 10px; border-radius: 4px; display: inline-block; font-weight: bold;">
            Download PDF
        </a>
    </div>
@endif

    <div class="official-header">
        <p class="republic">Republic of the Philippines</p>
        <p class="location">Province of Bulacan</p>
        <p class="location">City of Meycauayan</p>
        <span class="office">Office of the City Veterinarian (PawCare)</span>
    </div>

    <div class="report-title">{{ strtoupper($range) }} APPOINTMENT SCHEDULE REPORT</div>
    <div class="generation-meta">
        Range: {{ ucfirst($range) }} | Date: {{ now()->format('F d, Y h:i A') }}
    </div>

    <table class="appointment-table">
        <thead>
            <tr>
                <th>Date/Time</th>
                <th>Pet Owner</th>
                <th>Pet Name</th>
                <th>Service Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $appointment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A') }}</td>
                <td>{{ $appointment->user->name ?? 'N/A' }}</td>
                <td>{{ $appointment->pet->name ?? 'N/A' }}</td>
                <td>{{ $appointment->service_type }}</td>
                <td>{{ strtoupper($appointment->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No appointments found for this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-footer-container">
        <table class="sig-table">
            <tr>
                <td class="sig-box">
                    <p style="margin:0; font-size: 11px;">Prepared By:</p>
                    <div class="sig-line"></div>
                    <p class="sig-name">{{ auth()->user()->name ?? 'Main Admin' }}</p>
                    <p style="margin:0; font-size: 10px;">Clinic Staff / Data Officer</p>
                </td>
                <td style="width: 10%;"></td>
                <td class="sig-box">
                    <p style="margin:0; font-size: 11px;">Noted By:</p>
                    <div class="sig-line"></div>
                    <p class="sig-name">[NAME OF CITY VETERINARIAN]</p>
                    <p style="margin:0; font-size: 10px;">City Veterinarian</p>
                </td>
            </tr>
        </table>
        <div class="footer-note">
            This is an official document generated by the PawCare System for the City of Meycauayan.
        </div>
    </div>

</body>
</html>
