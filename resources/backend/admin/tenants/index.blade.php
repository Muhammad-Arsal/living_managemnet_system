@extends('backend::admin.layouts.app')

@section('title', 'Tenants')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Occupancy</p>
                        <h5 class="lms-page-header__title">Tenants</h5>
                        <p class="lms-page-header__subtitle">Current status is derived from an active tenancy, not a stored flag.</p>
                    </div>
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary lms-btn-add">
                        <i class="iconify" data-icon="bx:bx-plus"></i>
                        Add Tenant
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.tenants.index') }}" class="lms-filter-bar">
                        <div>
                            <label for="column" class="form-label">Select By Column</label>
                            <select name="column" id="column" class="form-select">
                                <option value="">Select By Column</option>
                                @foreach ($filterColumns as $value => $label)
                                    <option value="{{ $value }}" @selected(request('column') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="search" class="form-label">Search</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="iconify" data-icon="bx:bx-search"></i></span>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Search tenants" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div>
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All</option>
                                <option value="current" @selected(request('status') === 'current')>Current</option>
                                <option value="past" @selected(request('status') === 'past')>Past</option>
                            </select>
                        </div>
                        <div class="lms-filter-bar__actions">
                            <button type="submit" class="lms-filter-btn lms-filter-btn--primary">
                                <i class="iconify" data-icon="bx:bx-filter-alt"></i>
                                Submit
                            </button>
                            <a href="{{ route('admin.tenants.index') }}" class="lms-filter-btn lms-filter-btn--ghost">
                                <i class="iconify" data-icon="bx:bx-reset"></i>
                                Reset
                            </a>
                        </div>
                    </form>

                    @if ($tenants->count() > 0)
                        <div class="table-responsive lms-table-shell">
                            <table class="table table-hover lms-data-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Tenant</th>
                                        <th class="d-none d-md-table-cell">Mobile</th>
                                        <th>Status</th>
                                        <th class="d-none d-lg-table-cell">Current property</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tenants as $tenant)
                                        <tr>
                                            <td>
                                                <div class="lms-person">
                                                    <span class="lms-person__initials">{{ $tenant->initials }}</span>
                                                    <div class="lms-person__meta">
                                                        <span class="lms-person__name">{{ $tenant->full_name }}</span>
                                                        <span class="lms-person__email">{{ $tenant->email }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">{{ $tenant->mobile_number }}</td>
                                            <td>
                                                @if ($tenant->isCurrent())
                                                    <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>Current</span>
                                                @else
                                                    <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>Past</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                {{ $tenant->currentTenancy?->property?->name ?? '—' }}
                                            </td>
                                            <td class="text-end">
                                                <div class="lms-actions justify-content-end">
                                                    @if (! $tenant->isCurrent())
                                                        <a href="{{ route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'current']) }}"
                                                            class="lms-action-btn lms-action-btn--edit"
                                                            title="Assign property">
                                                            <i class="iconify" data-icon="bx:bx-home-smile"></i>
                                                            <span>Assign</span>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="lms-action-btn lms-action-btn--edit">
                                                        <i class="iconify" data-icon="bx:bx-edit-alt"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <button type="button" class="lms-action-btn lms-action-btn--delete" data-bs-toggle="modal" data-bs-target="#deleteTenantModal" data-delete-url="{{ route('admin.tenants.destroy', $tenant) }}" data-tenant-name="{{ $tenant->full_name }}">
                                                        <i class="iconify" data-icon="bx:bx-trash"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $tenants->links() }}</div>
                    @else
                        <div class="lms-empty-state">
                            <div class="lms-empty-state__icon">
                                <i class="iconify" data-icon="bx:bx-user"></i>
                            </div>
                            @if (request()->filled('search') || request()->filled('column') || request()->filled('status'))
                                <p class="mb-3">No tenants match your search.</p>
                                <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @else
                                <p class="mb-3">No tenants found.</p>
                                <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">Create First Tenant</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteTenantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Tenant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="deleteTenantName"></strong>? Tenants with tenancy history cannot be deleted.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteTenantForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            var modalEl = document.getElementById('deleteTenantModal');
            if (!modalEl) return;
            modalEl.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                if (!button) return;
                document.getElementById('deleteTenantForm').action = button.getAttribute('data-delete-url');
                document.getElementById('deleteTenantName').textContent = button.getAttribute('data-tenant-name') || 'this tenant';
            });
        })();
    </script>
@endsection
