<?php

namespace App\Http\Controllers\Backend\Council\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Council\Auth\LoginRequest;
use App\Services\Council\CouncilAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly CouncilAuthService $authService
    ) {}

    public function show(): View|RedirectResponse
    {
        if (Auth::guard('council')->check()) {
            return redirect()->route('council.dashboard');
        }

        return view('backend::council.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $this->authService->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );

        return redirect()->intended(route('council.dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('council.login');
    }
}
