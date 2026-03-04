<!DOCTYPE html>
<html>

<head>
    <title>Welcome to PawCare</title>
</head>

<body
    style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">

    <div style="text-align: center; margin-bottom: 20px;">
        <!-- Replace with actual absolute URL to your logo when in production -->
        <h2 style="color: #ff6b6b; margin-bottom: 5px;">PawCare</h2>
        <p style="color: #666; margin-top: 0;">Meycauayan Municipal Veterinary Office</p>
    </div>

    <div style="background: #fdfaf6; padding: 30px; border-radius: 8px; border: 1px solid #eee;">
        <h3 style="margin-top: 0; color: #2c3e50;">Welcome to the Pack, {{ $user->name }}! 🐾</h3>

        <p>Thank you for visiting the clinic and registering with PawCare. We've successfully verified your details and
            created your official Pet Owner account.</p>

        <p>You can now log in to the PawCare system to book appointments, track your pet's vaccination history, and view
            their digital health records.</p>

        <div
            style="background: #fff; padding: 15px; border-radius: 5px; margin: 25px 0; border-left: 4px solid #ff6b6b;">
            <p style="margin: 0 0 10px 0;"><strong>Your Login Credentials:</strong></p>
            <p style="margin: 0 0 5px 0;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin: 0;"><strong>Password:</strong> {{ $password }}</p>
        </div>

        <p><em>For your security, we strongly recommend changing this password immediately after your first login by
                visiting your Profile settings.</em></p>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ url('/login') }}"
                style="display: inline-block; background: #ff6b6b; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Log
                in to PawCare</a>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #999;">
        <p>This is an automated message from the PawCare system. Please do not reply directly to this email.</p>
        <p>&copy; {{ date('Y') }} PawCare Meycauayan. All rights reserved.</p>
    </div>
</body>

</html>
