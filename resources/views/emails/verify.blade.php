<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.6;">
    <p>Hello {{ $name }},</p>

    <p>Click the button below to verify your email and complete your registration:</p>

    <p>
        <a href="{{ $verificationUrl }}" style="
            display:inline-block;
            padding:12px 20px;
            font-size:16px;
            font-weight:bold;
            color:white;
            background-color:#1C5E4A;
            text-decoration:none;
            border-radius:6px;">
            Yes, it's me
        </a>
    </p>

    <p><strong>This link expires in 10 minutes.</strong></p>

    <p>If you did not register, please ignore this email.</p>
</body>
</html>