<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            background-color: #e9ecef;
            padding: 15px 25px;
            border-radius: 5px;
            margin: 20px 0;
            letter-spacing: 3px;
        }
        .warning {
            color: #dc3545;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <p>You have requested to reset your password. Please use the following verification code:</p>
        
        <div class="otp-code">{{ $otp }}</div>
        
        <p>This code will expire in 5 minutes.</p>
        
        <div class="warning">
            <strong>Security Notice:</strong> If you did not request this password reset, please ignore this email.
        </div>
    </div>
</body>
</html>