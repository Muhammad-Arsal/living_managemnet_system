<?php

namespace App\Http\Controllers\Backend\Admin\Settings;

use App\Enums\SiteSettingKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Settings\UpdateSiteSettingsRequest;
use App\Services\Admin\SiteSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteSettingsController extends Controller
{
    public function __construct(
        private readonly SiteSettingService $siteSettingService,
    ) {}

    public function index(): View
    {
        return view('backend::admin.settings.site-settings.index', [
            'settings' => $this->siteSettingService->allKeyed(),
            'textKeys' => SiteSettingKey::textKeys(),
        ]);
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $this->siteSettingService->update($request->validated());

        return redirect()
            ->route('admin.settings.site-settings.index')
            ->with('success', 'Site settings updated successfully.');
    }
}
