<?php

namespace App\Http\Controllers\Backend\Council\Auth;

use App\Http\Controllers\Controller;
use App\Models\Council;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class EmailVerificationController extends Controller
{
    public function notice(): RedirectResponse
    {
        return redirect()->route('council.dashboard');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $council = Council::query()->findOrFail($id);

        if (! hash_equals(sha1($council->getEmailForVerification()), $hash)) {
            abort(403);
        }

        if (! $council->hasVerifiedEmail()) {
            $council->markEmailAsVerified();
            event(new Verified($council));
        }

        if (Auth::guard('council')->check() && Auth::guard('council')->id() === $council->id) {
            return redirect()
                ->route('council.dashboard')
                ->with('success', 'Your email has been verified.');
        }

        if ($council->last_login_at === null) {
            $token = Password::broker('councils')->createToken($council);

            return redirect()
                ->route('council.password.reset', [
                    'token' => $token,
                    'email' => $council->email,
                    'setup' => 1,
                ])
                ->with('success', 'Your email has been verified. Please set your password below.');
        }

        return redirect()
            ->route('council.login')
            ->with('success', 'Your email has been verified. You can now log in.');
    }

    public function send(Request $request): RedirectResponse
    {
        /** @var Council|null $council */
        $council = Auth::guard('council')->user();

        if (! $council) {
            return redirect()->route('council.login');
        }

        if ($council->hasVerifiedEmail()) {
            return redirect()
                ->route('council.dashboard')
                ->with('success', 'Your email is already verified.');
        }

        $council->sendEmailVerificationNotification();

        return back()->with('success', 'A fresh verification link has been sent to your email address.');
    }
}
