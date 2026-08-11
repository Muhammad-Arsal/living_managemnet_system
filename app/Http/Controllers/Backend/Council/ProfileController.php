<?php

namespace App\Http\Controllers\Backend\Council;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Council\Profile\UpdateProfileRequest;
use App\Services\Council\CouncilProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly CouncilProfileService $profileService
    ) {}

    public function edit(): View
    {
        $council = Auth::guard('council')->user();
        $council->loadMissing('profile');

        return view('backend::council.profile.edit', compact('council'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profileService->update(
            Auth::guard('council')->user(),
            $request->validated()
        );

        return redirect()
            ->route('council.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
