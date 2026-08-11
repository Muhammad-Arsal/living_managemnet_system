<?php

namespace App\Http\Controllers\Backend\Staff\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class EmailVerificationController extends Controller
{
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
}
