<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Concerns\RedirectsPasswordBrokerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Staff\StoreStaffRequest;
use App\Http\Requests\Backend\Admin\Staff\UpdateStaffRequest;
use App\Models\Staff;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Services\Admin\StaffManagementService;
use App\Services\StaffMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    use RedirectsPasswordBrokerStatus;

    public function __construct(
        private readonly StaffManagementService $staffManagementService,
        private readonly StaffMailService $staffMailService,
        private readonly StaffRepositoryInterface $staffRepository,
    ) {}

    public function index(Request $request): View
    {
        $filterColumns = [
            'name' => 'Name',
            'email' => 'Email',
        ];

        $staffMembers = $this->staffRepository->paginateFiltered(
            $request->string('column')->toString() ?: null,
            $request->string('search')->trim()->toString() ?: null,
        );

        return view('backend::admin.staff.index', compact('staffMembers', 'filterColumns'));
    }

    public function create(): View
    {
        return view('backend::admin.staff.create');
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $this->staffManagementService->store($request->validated());

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff created successfully. A verification email has been sent.');
    }

    public function edit(Staff $staff): View
    {
        return view('backend::admin.staff.edit', compact('staff'));
    }

    public function update(UpdateStaffRequest $request, Staff $staff): RedirectResponse
    {
        $this->staffManagementService->update($staff, $request->validated());

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $this->staffManagementService->delete($staff);

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff deleted successfully.');
    }

    public function sendVerificationEmail(Staff $staff): RedirectResponse
    {
        $staff->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email has been sent.');
    }

    public function sendPasswordResetEmail(Staff $staff): RedirectResponse
    {
        return $this->redirectForPasswordBrokerStatus(
            $this->staffMailService->sendPasswordResetLink($staff),
            'Password reset email has been sent.'
        );
    }

    public function sendWelcomeEmail(Staff $staff): RedirectResponse
    {
        return $this->redirectForPasswordBrokerStatus(
            $this->staffMailService->sendWelcomeWithPasswordSetup($staff),
            'Welcome / password setup email has been sent.'
        );
    }
}
