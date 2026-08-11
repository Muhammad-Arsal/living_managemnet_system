<?php

namespace App\Http\Controllers\Backend\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Settings\StoreEmailTemplateRequest;
use App\Http\Requests\Backend\Admin\Settings\UpdateEmailTemplateRequest;
use App\Models\EmailTemplate;
use App\Services\Admin\EmailTemplateManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailTemplatesController extends Controller
{
    public function __construct(
        private readonly EmailTemplateManagementService $emailTemplateManagementService,
    ) {}

    public function index(): View
    {
        $emailTemplates = $this->emailTemplateManagementService->paginate();

        return view('backend::admin.settings.email-templates.index', compact('emailTemplates'));
    }

    public function create(): View
    {
        return view('backend::admin.settings.email-templates.create');
    }

    public function store(StoreEmailTemplateRequest $request): RedirectResponse
    {
        $this->emailTemplateManagementService->store($request->validated());

        return redirect()
            ->route('admin.settings.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    public function edit(EmailTemplate $emailTemplate): View
    {
        return view('backend::admin.settings.email-templates.edit', compact('emailTemplate'));
    }

    public function update(UpdateEmailTemplateRequest $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $this->emailTemplateManagementService->update($emailTemplate, $request->validated());

        return redirect()
            ->route('admin.settings.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        $this->emailTemplateManagementService->delete($emailTemplate);

        return redirect()
            ->route('admin.settings.email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }
}
