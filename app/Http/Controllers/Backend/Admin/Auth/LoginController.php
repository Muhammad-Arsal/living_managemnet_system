<?php

namespace App\Http\Controllers\Backend\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Auth\LoginRequest;
use App\Services\Admin\AdminAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AdminAuthService $authService
    ) {}

    public function show(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('backend::admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $this->authService->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('admin.login');
    }
}
