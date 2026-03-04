<h2>PawCare Veterinary Clinic</h2>

<p>Hello {{ $appointment->user->name ?? 'Pet Owner' }},</p>

<p>This email is to confirm that we have received your appointment request for
    <strong>{{ $appointment->pet_name }}</strong>.</p>

<p><strong>Appointment Details:</strong></p>
<ul>
    <li><strong>Service:</strong> {{ ucfirst($appointment->service_type) }}</li>
    <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</li>
    <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</li>
    <li><strong>Status:</strong> Pending Approval</li>
</ul>

<p>Our staff will review your request shortly. You will receive another notification once your appointment is approved
    or if it needs to be rescheduled.</p>

<p>Thank you for choosing PawCare! 🐾</p>
<br>
<small>If you need to make changes to this booking, please log into your account portal.</small>
