<!DOCTYPE html>
<html>

<head>
    <title>New Message for Developers</title>
</head>

<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #FF7D44; border-radius: 10px;">
        <h2 style="color: #FF7D44;">🐾 New Developer Contact Message</h2>
        <p><strong>From:</strong> {{ $name }} ({{ $email }})</p>
        <hr style="border: 0; border-top: 1px solid #eee;">
        <p><strong>Message:</strong></p>
        <p style="background: #fdfdfd; padding: 15px; border-radius: 5px; border-left: 4px solid #FF7D44;">
            {{ $messageText }}
        </p>
        <hr style="border: 0; border-top: 1px solid #eee;">
        <p style="font-size: 0.8rem; color: #777;">
            Sent from the PawCare Developer Widget.
        </p>
    </div>
</body>

</html>