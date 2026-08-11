<?php

namespace App\Http\Controllers\Backend\Council\Auth;

use App\Http\Controllers\Controller;
use App\Models\Council;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class EmailVerificationController extends Controller
{
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
}
