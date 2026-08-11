<?php

namespace App\Http\Controllers\Backend\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class EmailVerificationController extends Controller
{
    public function notice(): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $admin = Admin::query()->findOrFail($id);

        if (! hash_equals(sha1($admin->getEmailForVerification()), $hash)) {
            abort(403);
        }

        if (! $admin->hasVerifiedEmail()) {
            $admin->markEmailAsVerified();
            event(new Verified($admin));
        }

        if (Auth::guard('admin')->check() && Auth::guard('admin')->id() === $admin->id) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Your email has been verified.');
        }

        if ($admin->last_login_at === null) {
            $token = Password::broker('admins')->createToken($admin);

            return redirect()
                ->route('admin.password.reset', [
                    'token' => $token,
                    'email' => $admin->email,
                    'setup' => 1,
                ])
                ->with('success', 'Your email has been verified. Please set your password below.');
        }

        return redirect()
            ->route('admin.login')
            ->with('success', 'Your email has been verified. You can now log in.');
    }

    public function send(Request $request): RedirectResponse
    {
        /** @var Admin|null $admin */
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        if ($admin->hasVerifiedEmail()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Your email is already verified.');
        }

        $admin->sendEmailVerificationNotification();

        return back()->with('success', 'A fresh verification link has been sent to your email address.');
    }
}
