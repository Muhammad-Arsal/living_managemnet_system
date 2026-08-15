<div class="lms-ticket-meta">
    <div>
        <span class="lms-person__email">Reference</span>
        <strong>{{ $ticket->reference }}</strong>
    </div>
    <div>
        <span class="lms-person__email">Type</span>
        <strong>{{ $ticket->type?->name ?? '—' }}</strong>
        @if ($ticket->type && ! $ticket->type->is_active)
            <span class="lms-badge lms-badge--muted ms-1">Inactive</span>
        @endif
    </div>
    <div>
        <span class="lms-person__email">Priority</span>
        <strong>{{ $ticket->priority?->name ?? '—' }}</strong>
        @if ($ticket->priority && ! $ticket->priority->is_active)
            <span class="lms-badge lms-badge--muted ms-1">Inactive</span>
        @endif
    </div>
    <div>
        <span class="lms-person__email">From</span>
        <strong>{{ $ticket->creator?->name ?? '—' }}</strong>
    </div>
    <div>
        <span class="lms-person__email">Assigned</span>
        <strong>{{ $ticket->assignee?->name ?? '—' }}</strong>
    </div>
</div>

<div class="lms-ticket-thread">
    <article class="lms-ticket-message">
        <header>
            <strong>{{ $ticket->creator?->name ?? 'Unknown' }}</strong>
            <span>{{ $ticket->created_at?->format('M d, Y H:i') }}</span>
        </header>
        <h6 class="mb-2">{{ $ticket->subject }}</h6>
        <p class="mb-0">{!! nl2br(e($ticket->body)) !!}</p>
        @include('backend::partials.tickets.attachments-list', ['attachments' => $ticket->openingAttachments])
    </article>

    @foreach ($ticket->replies as $reply)
        <article class="lms-ticket-message">
            <header>
                <strong>{{ $reply->author?->name ?? 'Unknown' }}</strong>
                <span>{{ $reply->created_at?->format('M d, Y H:i') }}</span>
            </header>
            <p class="mb-0">{!! nl2br(e($reply->body)) !!}</p>
            @include('backend::partials.tickets.attachments-list', ['attachments' => $reply->attachments])
        </article>
    @endforeach
</div>

@if ($canReply)
    <form action="{{ route($portal.'.tickets.replies.store', $ticket) }}" method="POST" class="mt-4" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="body" class="form-label">Reply</label>
            <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="4" required>{{ old('body') }}</textarea>
            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        @include('backend::partials.tickets.attachments-field')
        <button type="submit" class="btn btn-primary">Post Reply</button>
        <a href="{{ route($portal.'.tickets.index') }}" class="btn btn-secondary">Back</a>
    </form>
@else
    <a href="{{ route($portal.'.tickets.index') }}" class="btn btn-secondary mt-3">Back</a>
@endif
