<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 12px; overflow: hidden; }
        .header { background: #0F3B2E; padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .body { padding: 32px; }
        .body p { color: #333; line-height: 1.7; font-size: 15px; }
        .cta { text-align: center; margin: 32px 0; }
        .cta a { background: #0F3B2E; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 15px; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
        .credentials { background: #E3EFE5; border-radius: 8px; padding: 16px 24px; margin: 20px 0; }
        .credentials p { margin: 6px 0; color: #0F3B2E; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Congratulations!</h1>
        </div>
        <div class="body">
            <p>Dear <strong>{{ $provider->user->first_name }} {{ $provider->user->last_name }}</strong>,</p>

            <p>We are pleased to inform you that your service provider application for <strong>{{ $provider->business_name }}</strong> has been <strong>approved</strong> by the Twende Uganda team.</p>

            <p>You can now log into the provider dashboard to manage your services, view bookings, and connect with tourists.</p>

            <div class="credentials">
                <p><strong>Dashboard URL:</strong> <a href="{{ config('app.url') }}/admin">{{ config('app.url') }}/admin</a></p>
                <p><strong>Email:</strong> {{ $provider->user->email }}</p>
                <p><strong>Password:</strong> Use the password you registered with. If you forgot it, use the reset option on the login page.</p>
            </div>

            <div class="cta">
                <a href="{{ config('app.url') }}/admin">Access Dashboard Here</a>
            </div>

            <p>Welcome to the Twende Uganda provider family. We look forward to working with you!</p>

            <p>Best regards,<br><strong>The Twende Uganda Team</strong></p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Twende Uganda. All rights reserved.</p>
        </div>
    </div>
</body>
</html>