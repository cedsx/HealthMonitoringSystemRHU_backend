<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Registration - Health Monitoring System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #0174CF;
        }   
        p {
            font-size: 13px;
            color: #555;
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #0174CF;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .highlight {
            color: #0174CF;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to Health Monitoring System!</h2>
        <p>Hello <span class="highlight">{{ $username }}</span>,</p>
        <p>Thank you for registering with our Health Monitoring System. To complete your registration, please use the following verification code:</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>This code is valid for <strong>5 minutes</strong>. If you did not request this registration, please ignore this email.</p>
        <p>Once verified, you'll be able to access your account and start using the system.</p>
        <p>Thank you,<br>Health Monitoring System Team</p>
    </div>
</body>
</html>