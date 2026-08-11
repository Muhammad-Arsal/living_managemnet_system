<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Council Dashboard') | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ site_favicon() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/sneat/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/sneat/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/sneat/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/properties-theme.css') }}?v={{ filemtime(public_path('css/admin/properties-theme.css')) }}" />

    <script src="{{ asset('vendor/sneat/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('vendor/sneat/js/config.js') }}"></script>
    @include('backend::layouts.partials.sneat-layout-fix')

    @yield('styles')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('backend::council.layouts.partials.sidebar')

            <div class="layout-page">
                @include('backend::council.layouts.partials.navbar')

                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        @include('backend::council.layouts.partials.verify-email-banner')
                        @include('backend::council.layouts.partials.alerts')
                        @yield('content')
                    </div>

                    @include('backend::council.layouts.partials.footer')
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vendor/popper/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/sneat/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('vendor/sneat/js/main.js') }}"></script>
    @include('backend::layouts.partials.sneat-layout-fix')
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script src="{{ asset('js/backend/alerts.js') }}"></script>

    @yield('scripts')
</body>

</html>
