<?php

namespace App\Http\Controllers\Backend\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Settings\StoreTicketTypeRequest;
use App\Http\Requests\Backend\Admin\Settings\UpdateTicketTypeRequest;
use App\Models\TicketType;
use App\Services\Admin\TicketCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TicketTypesController extends Controller
{
    public function __construct(
        private readonly TicketCatalogService $ticketCatalogService,
    ) {}

    public function index(): View
    {
        $ticketTypes = $this->ticketCatalogService->paginateTypes();

        return view('backend::admin.settings.ticket-types.index', compact('ticketTypes'));
    }

    public function create(): View
    {
        return view('backend::admin.settings.ticket-types.create');
    }

    public function store(StoreTicketTypeRequest $request): RedirectResponse
    {
        $this->ticketCatalogService->storeType($request->validated());

        return redirect()
            ->route('admin.settings.ticket-types.index')
            ->with('success', 'Ticket type created successfully.');
    }

    public function edit(TicketType $ticketType): View
    {
        return view('backend::admin.settings.ticket-types.edit', compact('ticketType'));
    }

    public function update(UpdateTicketTypeRequest $request, TicketType $ticketType): RedirectResponse
    {
        $this->ticketCatalogService->updateType($ticketType, $request->validated());

        return redirect()
            ->route('admin.settings.ticket-types.index')
            ->with('success', 'Ticket type updated successfully.');
    }

    public function destroy(TicketType $ticketType): RedirectResponse
    {
        try {
            $this->ticketCatalogService->deleteType($ticketType);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.settings.ticket-types.index')
            ->with('success', 'Ticket type deleted successfully.');
    }
}
