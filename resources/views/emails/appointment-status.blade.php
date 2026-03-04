<h2>PawCare Veterinary Office</h2>

<p>Hello {{ $appointment->user->name }},</p>

@if($type === 'approved')
    <p>Good news! Your appointment has been <strong>APPROVED</strong>.</p>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
@elseif($type === 'rejected')
    <p>We regret to inform you that your appointment has been <strong>REJECTED</strong>.</p>
    @if($appointment->rejection_reason)
        <p><strong>Reason:</strong> {{ $appointment->rejection_reason }}</p>
    @endif
@elseif($type === 'rescheduled')
    <p>Your appointment has been <strong>RESCHEDULED</strong>.</p>
    <p><strong>New Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
    <p><strong>New Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
@endif

<p><strong>Pet Name:</strong> {{ $appointment->pet_name }}</p>
<p>Please contact the veterinary office if you have questions.</p>
<p>Thank you! 🐾</p>
