<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Profile\UpdateProfileRequest;
use App\Services\Admin\AdminProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly AdminProfileService $profileService
    ) {}

    public function edit(): View
    {
        $admin = Auth::guard('admin')->user();
        $admin->loadMissing('profile');

        return view('backend::admin.profile.edit', compact('admin'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profileService->update(
            Auth::guard('admin')->user(),
            $request->validated()
        );

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
