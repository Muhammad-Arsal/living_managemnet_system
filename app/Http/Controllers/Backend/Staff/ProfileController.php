<?php

namespace App\Http\Controllers\Backend\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Staff\Profile\UpdateProfileRequest;
use App\Services\Staff\StaffProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly StaffProfileService $profileService
    ) {}

    public function edit(): View
    {
        $staff = Auth::guard('staff')->user();
        $staff->loadMissing('profile');

        return view('backend::staff.profile.edit', compact('staff'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profileService->update(
            Auth::guard('staff')->user(),
            $request->validated()
        );

        return redirect()
            ->route('staff.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
