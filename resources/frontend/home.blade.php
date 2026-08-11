<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ site_favicon() }}">
    <link rel="stylesheet" href="{{ asset('css/admin/auth.css') }}">
    <style>
        .portal-links { display: grid; gap: 12px; margin-top: 28px; }
        .portal-links a {
            display: block; text-align: center; text-decoration: none;
            padding: 14px; border-radius: 10px; font-weight: 500;
            background: #10b981; color: #fff; border: 1px solid #10b981;
        }
        .portal-links a:hover { background: #059669; border-color: #059669; color: #fff; }
        .portal-links a.secondary { background: #1e293b; border-color: #1e293b; }
        .portal-links a.secondary:hover { background: #334155; border-color: #334155; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <img src="{{ site_logo() }}" alt="{{ config('app.name') }} Logo">
                <h1>{{ config('app.name') }}</h1>
                <p>Choose a portal to continue</p>
            </div>
            <div class="portal-links">
                <a href="{{ route('admin.login') }}">Admin Login</a>
                <a class="secondary" href="{{ route('staff.login') }}">Staff Login</a>
                <a class="secondary" href="{{ route('council.login') }}">Council Login</a>
            </div>
        </div>
    </div>
</body>
</html>
