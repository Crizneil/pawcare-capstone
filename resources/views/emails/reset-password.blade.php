<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
</head>

<body
    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; color: #2c3e50;">
    <div
        style="max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(236, 147, 75, 0.9);">

        <div style="background-color: #f1aa6f; text-align: center; padding: 30px;">
            <h1 style="color: #2c3e50; margin: 0; font-size: 28px; letter-spacing: 1px;">PAWCARE</h1>
            <p style="color: #2c3e50; margin: 5px 0 0 0; font-size: 14px;">Meycauayan Municipal Veterinary Office</p>
        </div>

        <div style="padding: 40px 30px;">
            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">Hello,</p>
            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">You are receiving this email because we
                received a password reset request for your PawCare account.</p>

            <div style="text-align: center; margin: 35px 0;">
                <a href="{{ url(route('password.reset', ['token' => $token, 'email' => $email], false)) }}"
                    style="background-color: #f1aa6f; color: #2c3e50; text-decoration: none; padding: 14px 30px; border-radius: 50px; font-weight: bold; font-size: 16px; display: inline-block;">Reset
                    Password</a>
            </div>

            <p style="font-size: 14px; color: #6c757d; line-height: 1.6; margin-bottom: 25px;">This password reset link
                will expire in 60 minutes.</p>

            <p style="font-size: 16px; line-height: 1.6;">If you did not request a password reset, no further action is
                required; your account is safe.</p>

            <hr style="border: none; border-top: 1px solid #e9ecef; margin: 35px 0;">

            <p style="font-size: 14px; color: #6c757d; line-height: 1.6;">Best regards,<br><strong>The PawCare
                    System</strong></p>

            <p style="font-size: 12px; color: #adb5bd; margin-top: 30px; word-break: break-all;">
                If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your
                web browser: <br>
                <a href="{{ url(route('password.reset', ['token' => $token, 'email' => $email], false)) }}"
                    style="color: #3498db;">{{ url(route('password.reset', ['token' => $token, 'email' => $email], false)) }}</a>
            </p>
        </div>

        <div style="background-color: #f1f3f5; text-align: center; padding: 20px; font-size: 12px; color: #868e96;">
            &copy; {{ date('Y') }} PawCare Meycauayan. All rights reserved.
        </div>
    </div>
</body>

</html>
