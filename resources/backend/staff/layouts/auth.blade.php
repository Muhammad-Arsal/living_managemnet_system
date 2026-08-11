<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff Login') | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ site_favicon() }}">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Crimson+Text">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/auth.css') }}">
    @yield('styles')
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <img src="{{ site_logo() }}" alt="{{ config('app.name') }} Logo">
                <h1>@yield('page-title', 'Staff Login')</h1>
                <p>@yield('page-subtitle', 'Sign in to access the staff panel')</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    @yield('scripts')
</body>

</html>
