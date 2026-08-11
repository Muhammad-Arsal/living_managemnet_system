<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Concerns\RedirectsPasswordBrokerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Council\StoreCouncilRequest;
use App\Http\Requests\Backend\Admin\Council\UpdateCouncilRequest;
use App\Models\Council;
use App\Repositories\Contracts\CouncilRepositoryInterface;
use App\Services\Admin\CouncilManagementService;
use App\Services\CouncilMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouncilController extends Controller
{
    use RedirectsPasswordBrokerStatus;

    public function __construct(
        private readonly CouncilManagementService $councilManagementService,
        private readonly CouncilMailService $councilMailService,
        private readonly CouncilRepositoryInterface $councilRepository,
    ) {}

    public function index(Request $request): View
    {
        $filterColumns = [
            'name' => 'Name',
            'email' => 'Email',
        ];

        $councils = $this->councilRepository->paginateFiltered(
            $request->string('column')->toString() ?: null,
            $request->string('search')->trim()->toString() ?: null,
        );

        return view('backend::admin.council.index', compact('councils', 'filterColumns'));
    }

    public function create(): View
    {
        return view('backend::admin.council.create');
    }

    public function store(StoreCouncilRequest $request): RedirectResponse
    {
        $this->councilManagementService->store($request->validated());

        return redirect()
            ->route('admin.council.index')
            ->with('success', 'Council member created successfully. A welcome email with a set-password link has been sent.');
    }

    public function edit(Council $council): View
    {
        return view('backend::admin.council.edit', compact('council'));
    }

    public function update(UpdateCouncilRequest $request, Council $council): RedirectResponse
    {
        $this->councilManagementService->update($council, $request->validated());

        return redirect()
            ->route('admin.council.index')
            ->with('success', 'Council member updated successfully.');
    }

    public function destroy(Council $council): RedirectResponse
    {
        $this->councilManagementService->delete($council);

        return redirect()
            ->route('admin.council.index')
            ->with('success', 'Council member deleted successfully.');
    }

    public function sendVerificationEmail(Council $council): RedirectResponse
    {
        if ($council->hasVerifiedEmail()) {
            return back()->with('info', 'This council member is already verified.');
        }

        $council->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email has been sent.');
    }

    public function sendPasswordResetEmail(Council $council): RedirectResponse
    {
        return $this->redirectForPasswordBrokerStatus(
            $this->councilMailService->sendPasswordResetLink($council),
            'Password reset email has been sent.'
        );
    }

    public function sendWelcomeEmail(Council $council): RedirectResponse
    {
        return $this->redirectForPasswordBrokerStatus(
            $this->councilMailService->sendWelcomeWithPasswordSetup($council),
            'Welcome / password setup email has been sent.'
        );
    }
}
