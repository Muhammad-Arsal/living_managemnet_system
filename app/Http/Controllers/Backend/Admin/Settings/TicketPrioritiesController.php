<?php

namespace App\Http\Controllers\Backend\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Settings\StoreTicketPriorityRequest;
use App\Http\Requests\Backend\Admin\Settings\UpdateTicketPriorityRequest;
use App\Models\TicketPriority;
use App\Services\Admin\TicketCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TicketPrioritiesController extends Controller
{
    public function __construct(
        private readonly TicketCatalogService $ticketCatalogService,
    ) {}

    public function index(): View
    {
        $ticketPriorities = $this->ticketCatalogService->paginatePriorities();

        return view('backend::admin.settings.ticket-priorities.index', compact('ticketPriorities'));
    }

    public function create(): View
    {
        return view('backend::admin.settings.ticket-priorities.create');
    }

    public function store(StoreTicketPriorityRequest $request): RedirectResponse
    {
        $this->ticketCatalogService->storePriority($request->validated());

        return redirect()
            ->route('admin.settings.ticket-priorities.index')
            ->with('success', 'Ticket priority created successfully.');
    }

    public function edit(TicketPriority $ticketPriority): View
    {
        return view('backend::admin.settings.ticket-priorities.edit', compact('ticketPriority'));
    }

    public function update(UpdateTicketPriorityRequest $request, TicketPriority $ticketPriority): RedirectResponse
    {
        $this->ticketCatalogService->updatePriority($ticketPriority, $request->validated());

        return redirect()
            ->route('admin.settings.ticket-priorities.index')
            ->with('success', 'Ticket priority updated successfully.');
    }

    public function destroy(TicketPriority $ticketPriority): RedirectResponse
    {
        try {
            $this->ticketCatalogService->deletePriority($ticketPriority);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.settings.ticket-priorities.index')
            ->with('success', 'Ticket priority deleted successfully.');
    }
}
