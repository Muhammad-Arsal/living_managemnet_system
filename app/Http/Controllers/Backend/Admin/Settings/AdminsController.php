<?php

namespace App\Http\Controllers\Backend\Admin\Settings;

use App\Http\Controllers\Concerns\RedirectsPasswordBrokerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Settings\StoreAdminRequest;
use App\Http\Requests\Backend\Admin\Settings\UpdateAdminRequest;
use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Services\Admin\AdminManagementService;
use App\Services\AdminMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminsController extends Controller
{
    use RedirectsPasswordBrokerStatus;

    public function __construct(
        private readonly AdminManagementService $adminManagementService,
        private readonly AdminMailService $adminMailService,
        private readonly AdminRepositoryInterface $adminRepository,
    ) {}

    public function index(Request $request): View
    {
        $filterColumns = [
            'name' => 'Name',
            'email' => 'Email',
        ];

        $admins = $this->adminRepository->paginateFiltered(
            $request->string('column')->toString() ?: null,
            $request->string('search')->trim()->toString() ?: null,
        );

        return view('backend::admin.settings.admins.index', compact('admins', 'filterColumns'));
    }

    public function create(): View
    {
        return view('backend::admin.settings.admins.create');
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $this->adminManagementService->store($request->validated());

        return redirect()
            ->route('admin.settings.admins.index')
            ->with('success', 'Admin created successfully. A verification email has been sent.');
    }

    public function edit(Admin $admin): View
    {
        return view('backend::admin.settings.admins.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
    {
        $this->adminManagementService->update($admin, $request->validated());

        return redirect()
            ->route('admin.settings.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        if (Auth::guard('admin')->id() === $admin->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $this->adminManagementService->delete($admin);

        return redirect()
            ->route('admin.settings.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }

    public function sendVerificationEmail(Admin $admin): RedirectResponse
    {
        $admin->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email has been sent.');
    }

    public function sendPasswordResetEmail(Admin $admin): RedirectResponse
    {
        return $this->redirectForPasswordBrokerStatus(
            $this->adminMailService->sendPasswordResetLink($admin),
            'Password reset email has been sent.'
        );
    }

    public function sendWelcomeEmail(Admin $admin): RedirectResponse
    {
        return $this->redirectForPasswordBrokerStatus(
            $this->adminMailService->sendWelcomeWithPasswordSetup($admin),
            'Welcome / password setup email has been sent.'
        );
    }
}
