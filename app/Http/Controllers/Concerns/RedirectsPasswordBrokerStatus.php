<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

trait RedirectsPasswordBrokerStatus
{
    private function redirectForPasswordBrokerStatus(string $status, string $successMessage): RedirectResponse
    {
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', $successMessage);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->with('warning', 'Please wait before sending another email of this type.');
        }

        return back()->with('error', __($status));
    }
}
