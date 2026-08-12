@php
    $oldValues = $audit->old_values ?? [];
    $newValues = $audit->new_values ?? [];
    $allKeys = collect(array_keys($oldValues))
        ->merge(array_keys($newValues))
        ->unique()
        ->sort()
        ->values();
@endphp

<div class="lms-audit-details">
    <dl class="row mb-4">
        <dt class="col-sm-4">Date &amp; Time</dt>
        <dd class="col-sm-8">{{ $audit->created_at?->format('M d, Y H:i:s') ?? '—' }}</dd>

        <dt class="col-sm-4">User</dt>
        <dd class="col-sm-8">
            @if ($audit->user)
                {{ $audit->user->name }} ({{ $audit->user->email }})
            @else
                System
            @endif
        </dd>

        <dt class="col-sm-4">Action</dt>
        <dd class="col-sm-8">{{ audit_event_label($audit->event) }}</dd>

        <dt class="col-sm-4">Model</dt>
        <dd class="col-sm-8">{{ audit_model_label($audit->auditable_type) }} #{{ $audit->auditable_id }}</dd>

        @if ($audit->ip_address)
            <dt class="col-sm-4">IP Address</dt>
            <dd class="col-sm-8">{{ $audit->ip_address }}</dd>
        @endif

        @if ($audit->url)
            <dt class="col-sm-4">URL</dt>
            <dd class="col-sm-8 text-break">{{ $audit->url }}</dd>
        @endif
    </dl>

    @if ($allKeys->isNotEmpty())
        <h6 class="mb-3">Changes</h6>
        <div class="table-responsive lms-table-shell">
            <table class="table table-sm lms-data-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allKeys as $key)
                        <tr>
                            <td><code>{{ $key }}</code></td>
                            <td class="text-break">{{ isset($oldValues[$key]) ? (is_scalar($oldValues[$key]) ? $oldValues[$key] : json_encode($oldValues[$key])) : '—' }}</td>
                            <td class="text-break">{{ isset($newValues[$key]) ? (is_scalar($newValues[$key]) ? $newValues[$key] : json_encode($newValues[$key])) : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="lms-muted-date mb-0">No field-level changes recorded for this event.</p>
    @endif
</div>
