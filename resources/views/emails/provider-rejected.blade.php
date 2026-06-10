<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 12px; overflow: hidden; }
        .header { background: #7B2020; padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .body { padding: 32px; }
        .body p { color: #333; line-height: 1.7; font-size: 15px; }
        .reason { background: #FFF3F3; border-left: 4px solid #C0392B; border-radius: 4px; padding: 16px 24px; margin: 20px 0; }
        .reason p { margin: 0; color: #7B2020; font-size: 14px; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Application Update</h1>
        </div>
        <div class="body">
            <p>Dear <strong>{{ $provider->user->first_name }} {{ $provider->user->last_name }}</strong>,</p>

            <p>Thank you for applying to become a service provider on Twende Uganda. After reviewing your application for <strong>{{ $provider->business_name }}</strong>, we regret to inform you that it has not been approved at this time.</p>

            <p><strong>Reason for rejection:</strong></p>
            <div class="reason">
                <p>{{ $reason }}</p>
            </div>

            <p>You are welcome to address the issues mentioned above and resubmit your application. If you have any questions, please contact our support team.</p>

            <p>Best regards,<br><strong>The Twende Uganda Team</strong></p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Twende Uganda. All rights reserved.</p>
        </div>
    </div>
</body>
</html>