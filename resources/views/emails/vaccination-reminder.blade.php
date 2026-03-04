<h2>PawCare Veterinary Office</h2>

<p>Hello {{ $pet->user->name }},</p>

@if($status === 'due_soon')
    <p>Your pet <strong>{{ $pet->name }}</strong> has a vaccination coming up soon.</p>
@elseif($status === 'overdue')
    <p>Your pet <strong>{{ $pet->name }}</strong> has an <strong>overdue</strong> vaccination.</p>
@endif

<p><strong>Vaccine:</strong> {{ $pet->latestVaccination->vaccine_name ?? 'N/A' }}</p>
<p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($pet->latestVaccination->next_due_date)->format('M d, Y') ?? 'N/A' }}</p>

<p>Please schedule an appointment with us as soon as possible if needed.</p>
<p>Thank you! 🐾</p>
