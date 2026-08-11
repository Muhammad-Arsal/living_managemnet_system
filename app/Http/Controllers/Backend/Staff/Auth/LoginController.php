<?php

namespace App\Http\Controllers\Backend\Staff\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Staff\Auth\LoginRequest;
use App\Services\Staff\StaffAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly StaffAuthService $authService
    ) {}

    public function show(): View|RedirectResponse
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }

        return view('backend::staff.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $this->authService->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );

        return redirect()->intended(route('staff.dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('staff.login');
    }
}
