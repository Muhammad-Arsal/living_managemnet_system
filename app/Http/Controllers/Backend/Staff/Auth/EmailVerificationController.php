<?php

namespace App\Http\Controllers\Backend\Staff\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class EmailVerificationController extends Controller
{
    public function notice(): RedirectResponse
    {
        return redirect()->route('staff.dashboard');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $staff = Staff::query()->findOrFail($id);

        if (! hash_equals(sha1($staff->getEmailForVerification()), $hash)) {
            abort(403);
        }

        if (! $staff->hasVerifiedEmail()) {
            $staff->markEmailAsVerified();
            event(new Verified($staff));
        }

        if (Auth::guard('staff')->check() && Auth::guard('staff')->id() === $staff->id) {
            return redirect()
                ->route('staff.dashboard')
                ->with('success', 'Your email has been verified.');
        }

        if ($staff->last_login_at === null) {
            $token = Password::broker('staff')->createToken($staff);

            return redirect()
                ->route('staff.password.reset', [
                    'token' => $token,
                    'email' => $staff->email,
                    'setup' => 1,
                ])
                ->with('success', 'Your email has been verified. Please set your password below.');
        }

        return redirect()
            ->route('staff.login')
            ->with('success', 'Your email has been verified. You can now log in.');
    }

    public function send(Request $request): RedirectResponse
    {
        /** @var Staff|null $staff */
        $staff = Auth::guard('staff')->user();

        if (! $staff) {
            return redirect()->route('staff.login');
        }

        if ($staff->hasVerifiedEmail()) {
            return redirect()
                ->route('staff.dashboard')
                ->with('success', 'Your email is already verified.');
        }

        $staff->sendEmailVerificationNotification();

        return back()->with('success', 'A fresh verification link has been sent to your email address.');
    }
}
