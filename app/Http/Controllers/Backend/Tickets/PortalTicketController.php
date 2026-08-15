<?php

namespace App\Http\Controllers\Backend\Tickets;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Tickets\StoreTicketReplyRequest;
use App\Http\Requests\Backend\Tickets\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Repositories\Contracts\TicketPriorityRepositoryInterface;
use App\Repositories\Contracts\TicketRepositoryInterface;
use App\Repositories\Contracts\TicketTypeRepositoryInterface;
use App\Services\Tickets\TicketAssignmentService;
use App\Services\Tickets\TicketAttachmentService;
use App\Services\Tickets\TicketService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

abstract class PortalTicketController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected readonly TicketService $ticketService,
        protected readonly TicketRepositoryInterface $ticketRepository,
        protected readonly TicketTypeRepositoryInterface $ticketTypeRepository,
        protected readonly TicketPriorityRepositoryInterface $ticketPriorityRepository,
        protected readonly TicketAssignmentService $ticketAssignmentService,
        protected readonly TicketAttachmentService $ticketAttachmentService,
    ) {}

    abstract protected function portal(): string;

    protected function allowsCreate(): bool
    {
        return true;
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $actor = $request->user();
        $status = TicketStatus::tryFrom($request->string('status')->toString());
        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'status' => $status?->value,
            'ticket_priority_id' => $request->filled('ticket_priority_id') ? $request->integer('ticket_priority_id') : null,
        ];

        $tickets = $this->ticketService->paginateForActor($actor, $filters);

        return view('backend::'.$this->portal().'.tickets.index', [
            'tickets' => $tickets,
            'actor' => $actor,
            'canCreate' => $this->allowsCreate() && $actor->can('create', Ticket::class),
            'portal' => $this->portal(),
            'ticketStatuses' => TicketStatus::cases(),
            'ticketPriorities' => $this->ticketPriorityRepository->listOrdered(),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->allowsCreate(), 403);
        $this->authorize('create', Ticket::class);

        $actor = $request->user();

        return view('backend::'.$this->portal().'.tickets.create', [
            'ticketTypes' => $this->ticketTypeRepository->listActive(),
            'ticketPriorities' => $this->ticketPriorityRepository->listActive(),
            'assignees' => $this->ticketAssignmentService->assigneesFor($actor),
            'assigneeLabel' => $this->assigneeLabel($actor),
            'portal' => $this->portal(),
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        abort_unless($this->allowsCreate(), 403);

        $ticket = $this->ticketService->create($request->user(), $request->validated());

        return redirect()
            ->route($this->portal().'.tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $actor = $request->user();
        $ticket = $this->ticketRepository->findById($ticket->id) ?? $ticket;
        $this->ticketService->markRead($ticket, $actor);

        return view('backend::'.$this->portal().'.tickets.show', [
            'ticket' => $ticket,
            'actor' => $actor,
            'canReply' => $actor->can('reply', $ticket),
            'portal' => $this->portal(),
        ]);
    }

    public function reply(StoreTicketReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->ticketService->reply($ticket, $request->user(), $request->validated());

        return redirect()
            ->route($this->portal().'.tickets.show', $ticket)
            ->with('success', 'Reply posted successfully.');
    }

    public function downloadAttachment(Ticket $ticket, TicketAttachment $attachment): mixed
    {
        $this->authorize('view', $ticket);

        return $this->ticketAttachmentService->download($ticket, $attachment);
    }

    protected function assigneeLabel(mixed $actor): string
    {
        $config = $this->ticketAssignmentService->assignableConfig($actor);

        if ($config === null) {
            return 'Assignee';
        }

        return (string) (config('portals.'.$config['portal'].'.label') ?? 'Assignee');
    }
}
