<div class="card lms-page-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route($portal.'.tickets.index') }}" class="lms-filter-bar lms-filter-bar--tickets">
            <div>
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control"
                    placeholder="Subject or reference" value="{{ $filters['search'] ?? request('search') }}">
            </div>
            <div>
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Any</option>
                    @foreach ($ticketStatuses as $ticketStatus)
                        <option value="{{ $ticketStatus->value }}" @selected(($filters['status'] ?? request('status')) === $ticketStatus->value)>
                            {{ $ticketStatus->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="ticket_priority_id" class="form-label">Priority</label>
                <select name="ticket_priority_id" id="ticket_priority_id" class="form-select">
                    <option value="">Any</option>
                    @foreach ($ticketPriorities as $ticketPriority)
                        <option value="{{ $ticketPriority->id }}" @selected((string) ($filters['ticket_priority_id'] ?? request('ticket_priority_id')) === (string) $ticketPriority->id)>
                            {{ $ticketPriority->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lms-filter-bar__actions">
                <button type="submit" class="lms-filter-btn lms-filter-btn--primary">
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card lms-page-card">
    <div class="card-header lms-page-header">
        <div class="lms-page-header__copy">
            <h5 class="lms-page-header__title">Support tickets</h5>
        </div>
        @if ($canCreate)
            <a href="{{ route($portal.'.tickets.create') }}" class="btn btn-primary lms-btn-add">
                New ticket
            </a>
        @endif
    </div>
    <div class="card-body">
        @if ($tickets->count() > 0)
            <div class="table-responsive lms-table-shell">
                <table class="table table-hover lms-data-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Type</th>
                            <th>Priority</th>
                            <th class="d-none d-lg-table-cell">From</th>
                            <th class="d-none d-md-table-cell">Assigned</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td>
                                    <div class="lms-person__meta">
                                        <span class="lms-person__name">
                                            @if ($ticket->isUnreadFor($actor))
                                                <span class="lms-unread-dot" title="Unread"></span>
                                            @endif
                                            {{ $ticket->reference }}
                                        </span>
                                        <span class="lms-person__email">{{ $ticket->subject }}</span>
                                    </div>
                                </td>
                                <td>{{ $ticket->type?->name ?? '—' }}</td>
                                <td>{{ $ticket->priority?->name ?? '—' }}</td>
                                <td class="d-none d-lg-table-cell">{{ $ticket->creator?->name ?? '—' }}</td>
                                <td class="d-none d-md-table-cell">{{ $ticket->assignee?->name ?? '—' }}</td>
                                <td>
                                    <span class="lms-badge {{ $ticket->status?->value === 'open' ? 'lms-badge--success' : 'lms-badge--muted' }}">
                                        <span class="lms-badge__dot"></span>
                                        {{ $ticket->status?->label() ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route($portal.'.tickets.show', $ticket) }}" class="lms-action-btn lms-action-btn--edit">
                                        <i class="iconify" data-icon="bx:bx-show"></i>
                                        <span>View</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $tickets->links() }}</div>
        @else
            <div class="lms-empty-state">
                @if (request()->filled('search') || request()->filled('status') || request()->filled('ticket_priority_id'))
                    <p class="mb-3">No tickets match your filters.</p>
                    <a href="{{ route($portal.'.tickets.index') }}" class="btn btn-outline-secondary">Reset</a>
                @else
                    <p class="mb-0">No tickets yet.</p>
                @endif
            </div>
        @endif
    </div>
</div>
