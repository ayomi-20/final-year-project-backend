<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <p>Hello {{ $name }},</p>

    <p>You requested a password reset. Use the code below to reset your password.</p>
    <p>It expires in <strong>10 minutes</strong>.</p>

    <div style="
        display: inline-block;
        padding: 16px 32px;
        font-size: 36px;
        font-weight: bold;
        letter-spacing: 10px;
        color: #1C5E4A;
        background-color: #f0faf6;
        border: 2px solid #1C5E4A;
        border-radius: 8px;
        margin: 16px 0;">
        {{ $otp }}
    </div>

    <p>Enter this code in the app to reset your password.</p>
    <p>If you did not request a password reset, please ignore this email.</p>

</body>
</html>