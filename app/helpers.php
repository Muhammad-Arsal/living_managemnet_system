<?php

if (! function_exists('site_logo')) {
    function site_logo(): string
    {
        if (file_exists(public_path('img/logo.svg'))) {
            return asset('img/logo.svg');
        }

        if (file_exists(public_path('img/logo-mark.svg'))) {
            return asset('img/logo-mark.svg');
        }

        return asset('vendor/sneat/img/favicon/favicon.ico');
    }
}

if (! function_exists('site_favicon')) {
    function site_favicon(): string
    {
        if (file_exists(public_path('favicon.ico'))) {
            return asset('favicon.ico');
        }

        return asset('vendor/sneat/img/favicon/favicon.ico');
    }
}
