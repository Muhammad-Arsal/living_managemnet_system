<?php

namespace App\Http\Controllers\Backend\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('backend::admin.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
            'isSetup' => $request->boolean('setup'),
        ]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $status = Password::broker('admins')->reset(
            $validated,
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Auth::guard('admin')->logout();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login', ['email' => $validated['email']])
                ->with('success', 'Password saved successfully. You can now log in.');
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
