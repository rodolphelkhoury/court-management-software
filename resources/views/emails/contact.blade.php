<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .header {
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #4f46e5;
            margin: 0;
        }
        .content {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #4f46e5;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            color: #333;
            padding: 10px;
            background-color: #f0f0f0;
            border-left: 4px solid #4f46e5;
            border-radius: 3px;
        }
        .message-box {
            background-color: #f0f0f0;
            border-left: 4px solid #4f46e5;
            padding: 15px;
            border-radius: 3px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎾 New Contact Form Submission</h2>
        </div>

        <div class="content">
            <div class="field">
                <span class="label">From:</span>
                <div class="value">{{ $name }}</div>
            </div>

            <div class="field">
                <span class="label">Email:</span>
                <div class="value">
                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                </div>
            </div>

            <div class="field">
                <span class="label">Subject:</span>
                <div class="value">{{ $subject }}</div>
            </div>

            <div class="field">
                <span class="label">Message:</span>
                <div class="message-box">{{ $user_message }}</div>
            </div>
        </div>

        <div class="footer">
            <p>This email was sent from the CourtConnect landing page contact form.</p>
            <p>&copy; {{ date('Y') }} CourtConnect. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
