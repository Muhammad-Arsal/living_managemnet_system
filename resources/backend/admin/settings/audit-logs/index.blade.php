@extends('backend::admin.layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <h5 class="lms-page-header__title mb-0">Activity Log</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.settings.audit-logs.index') }}" class="lms-filter-bar lms-filter-bar--audit">
                        <div>
                            <label for="action" class="form-label">Action</label>
                            <select name="action" id="action" class="form-select">
                                <option value="">All</option>
                                @foreach ($actionOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(request('action') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="model" class="form-label">Model</label>
                            <select name="model" id="model" class="form-select">
                                <option value="">All</option>
                                @foreach ($modelOptions as $type)
                                    <option value="{{ $type }}" @selected(request('model') === $type)>
                                        {{ audit_model_label($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="from" class="form-label">From</label>
                            <input type="date" name="from" id="from" class="form-control"
                                value="{{ request('from') }}">
                        </div>
                        <div>
                            <label for="to" class="form-label">To</label>
                            <input type="date" name="to" id="to" class="form-control"
                                value="{{ request('to') }}">
                        </div>
                        <div class="lms-filter-bar__actions">
                            <button type="submit" class="lms-filter-btn lms-filter-btn--primary">
                                Filter
                            </button>
                            <a href="{{ route('admin.settings.audit-logs.index') }}" class="lms-filter-btn lms-filter-btn--ghost">
                                Reset
                            </a>
                        </div>
                    </form>

                    @if ($audits->count() > 0)
                        <div class="table-responsive lms-table-shell">
                            <table class="table table-hover lms-data-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date &amp; Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th class="d-none d-md-table-cell">Model</th>
                                        <th class="text-end">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($audits as $audit)
                                        @php
                                            $user = $audit->user;
                                            $eventClass = match ($audit->event) {
                                                'created' => 'lms-badge--audit-created',
                                                'updated' => 'lms-badge--audit-updated',
                                                'deleted' => 'lms-badge--audit-deleted',
                                                'restored' => 'lms-badge--audit-restored',
                                                default => 'lms-badge--muted',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="lms-muted-date">{{ $audit->created_at?->format('M d, Y H:i') ?? '—' }}</span>
                                            </td>
                                            <td>
                                                @if ($user)
                                                    <div class="lms-person">
                                                        <div class="lms-person__meta">
                                                            <span class="lms-person__name">{{ $user->name ?? 'Unknown' }}</span>
                                                            <span class="lms-person__email">{{ $user->email ?? '—' }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="lms-muted-date">System</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="lms-badge {{ $eventClass }}">
                                                    {{ audit_event_label($audit->event) }}
                                                </span>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="lms-audit-model">{{ audit_model_label($audit->auditable_type) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button"
                                                    class="lms-action-btn lms-action-btn--view"
                                                    title="View audit details"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#auditDetailsModal"
                                                    data-audit-url="{{ route('admin.settings.audit-logs.show', $audit) }}">
                                                    <i class="iconify" data-icon="bx:bx-show"></i>
                                                    <span>View</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $audits->links() }}</div>
                    @else
                        <div class="lms-empty-state">
                            <div class="lms-empty-state__icon">
                                <i class="iconify" data-icon="bx:bx-history"></i>
                            </div>
                            @if (request()->hasAny(['action', 'model', 'from', 'to']))
                                <p class="mb-3">No activity matches your filters.</p>
                                <a href="{{ route('admin.settings.audit-logs.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @else
                                <p class="mb-0">No activity recorded yet.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="auditDetailsModal" tabindex="-1" aria-labelledby="auditDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditDetailsModalLabel">Audit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="auditDetailsBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            var modalEl = document.getElementById('auditDetailsModal');
            var bodyEl = document.getElementById('auditDetailsBody');
            if (!modalEl || !bodyEl) return;

            modalEl.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                if (!button) return;

                var url = button.getAttribute('data-audit-url');
                if (!url) return;

                bodyEl.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                    .then(function(response) {
                        if (!response.ok) throw new Error('Failed to load audit details.');
                        return response.text();
                    })
                    .then(function(html) {
                        bodyEl.innerHTML = html;
                    })
                    .catch(function() {
                        bodyEl.innerHTML = '<p class="text-danger mb-0">Unable to load audit details. Please try again.</p>';
                    });
            });
        })();
    </script>
@endsection
