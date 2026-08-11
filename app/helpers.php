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

if (! function_exists('password_rule_hint')) {
    function password_rule_hint(): string
    {
        return 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a symbol.';
    }
}

if (! function_exists('avatar_placeholder')) {
    function avatar_placeholder(string $initials = 'U'): string
    {
        $safe = htmlspecialchars(strtoupper(substr($initials, 0, 2)), ENT_QUOTES, 'UTF-8');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">'
            .'<rect fill="#d1fae5" width="100" height="100"/>'
            .'<text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" '
            .'fill="#065f46" font-size="36" font-family="Public Sans, Arial, sans-serif" font-weight="600">'
            .$safe
            .'</text></svg>';

        return 'data:image/svg+xml,'.rawurlencode($svg);
    }
}
