<form action="{{ route($portal.'.tickets.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="ticket_type_id" class="form-label">Ticket Type <span class="text-danger">*</span></label>
            <select name="ticket_type_id" id="ticket_type_id" class="form-select @error('ticket_type_id') is-invalid @enderror">
                <option value="">Select type</option>
                @foreach ($ticketTypes as $type)
                    <option value="{{ $type->id }}" @selected((string) old('ticket_type_id') === (string) $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('ticket_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="ticket_priority_id" class="form-label">Ticket Priority <span class="text-danger">*</span></label>
            <select name="ticket_priority_id" id="ticket_priority_id" class="form-select @error('ticket_priority_id') is-invalid @enderror">
                <option value="">Select priority</option>
                @foreach ($ticketPriorities as $priority)
                    <option value="{{ $priority->id }}" @selected((string) old('ticket_priority_id') === (string) $priority->id)>{{ $priority->name }}</option>
                @endforeach
            </select>
            @error('ticket_priority_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-3">
        <label for="assignee_id" class="form-label">Assign {{ $assigneeLabel }} <span class="text-danger">*</span></label>
        <select name="assignee_id" id="assignee_id" class="form-select @error('assignee_id') is-invalid @enderror">
            <option value="">Select {{ strtolower($assigneeLabel) }}</option>
            @foreach ($assignees as $assignee)
                <option value="{{ $assignee->id }}" @selected((string) old('assignee_id') === (string) $assignee->id)>
                    {{ $assignee->name }} ({{ $assignee->email }})
                </option>
            @endforeach
        </select>
        @error('assignee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}">
        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="body" class="form-label">Message <span class="text-danger">*</span></label>
        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="6">{{ old('body') }}</textarea>
        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @include('backend::partials.tickets.attachments-field')
    <button type="submit" class="btn btn-primary">Create Ticket</button>
    <a href="{{ route($portal.'.tickets.index') }}" class="btn btn-secondary">Cancel</a>
</form>
