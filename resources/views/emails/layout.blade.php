<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Message' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f5;
        }
        .email-wrapper { padding: 24px 16px; }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .email-header { padding: 24px; text-align: center; }
        .email-logo img { max-height: 48px; width: auto; vertical-align: middle; }
        .email-body { padding: 24px; }
        .email-body a { color: #059669; text-decoration: none; }
        .email-footer {
            padding: 16px 24px;
            font-size: 12px;
            color: #71717a;
            text-align: center;
            border-top: 1px solid #e4e4e7;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <div class="email-logo">
                    <img src="{{ site_logo() }}" alt="{{ config('app.name') }} Logo">
                </div>
            </div>
            <div class="email-body">
                {!! $content ?? '' !!}
            </div>
            <div class="email-footer">
                This email was sent by {{ config('app.name') }}. Please do not reply directly to this message.
            </div>
        </div>
    </div>
</body>
</html>
