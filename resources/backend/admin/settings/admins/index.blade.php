@extends('backend::admin.layouts.app')

@section('title', 'Admins')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Settings</p>
                        <h5 class="lms-page-header__title">Admins</h5>
                        <p class="lms-page-header__subtitle">Create and manage administrator accounts for the panel.</p>
                    </div>
                    <a href="{{ route('admin.settings.admins.create') }}" class="btn btn-primary lms-btn-add">
                        <i class="iconify" data-icon="bx:bx-plus"></i>
                        Add New Admin
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.settings.admins.index') }}" class="lms-filter-bar">
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
                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="lms-filter-bar__actions">
                            <button type="submit" class="lms-filter-btn lms-filter-btn--primary">
                                <i class="iconify" data-icon="bx:bx-filter-alt"></i>
                                Submit
                            </button>
                            <a href="{{ route('admin.settings.admins.index') }}" class="lms-filter-btn lms-filter-btn--ghost">
                                <i class="iconify" data-icon="bx:bx-reset"></i>
                                Reset
                            </a>
                        </div>
                    </form>

                    @if ($admins->count() > 0)
                        <div class="table-responsive lms-table-shell">
                            <table class="table table-hover lms-data-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Admin</th>
                                        <th>Verified</th>
                                        <th>Active</th>
                                        <th class="d-none d-lg-table-cell">Last Login</th>
                                        <th class="d-none d-md-table-cell">Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($admins as $admin)
                                        <tr>
                                            <td>
                                                <div class="lms-person">
                                                    @if ($admin->avatar_url)
                                                        <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="lms-person__avatar">
                                                    @else
                                                        <span class="lms-person__initials">{{ $admin->initials }}</span>
                                                    @endif
                                                    <div class="lms-person__meta">
                                                        <span class="lms-person__name">{{ $admin->name }}</span>
                                                        <span class="lms-person__email">{{ $admin->email }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($admin->hasVerifiedEmail())
                                                    <span class="lms-badge lms-badge--success">
                                                        <span class="lms-badge__dot"></span>
                                                        Verified
                                                    </span>
                                                @else
                                                    <span class="lms-badge lms-badge--warning">
                                                        <span class="lms-badge__dot"></span>
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($admin->is_active)
                                                    <span class="lms-badge lms-badge--success">
                                                        <span class="lms-badge__dot"></span>
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="lms-badge lms-badge--muted">
                                                        <span class="lms-badge__dot"></span>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <span class="lms-muted-date">{{ $admin->last_login_at?->format('M d, Y H:i') ?? '—' }}</span>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="lms-muted-date">{{ $admin->created_at->format('M d, Y') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="lms-actions justify-content-end">
                                                    <a href="{{ route('admin.settings.admins.edit', $admin) }}"
                                                        class="lms-action-btn lms-action-btn--edit"
                                                        title="Edit admin">
                                                        <i class="iconify" data-icon="bx:bx-edit-alt"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <button type="button"
                                                        class="lms-action-btn lms-action-btn--delete"
                                                        title="Delete admin"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteAdminModal"
                                                        data-delete-url="{{ route('admin.settings.admins.destroy', $admin) }}"
                                                        data-admin-name="{{ $admin->name }}">
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
                        <div class="mt-3">{{ $admins->links() }}</div>
                    @else
                        <div class="lms-empty-state">
                            <div class="lms-empty-state__icon">
                                <i class="iconify" data-icon="bx:bx-shield-quarter"></i>
                            </div>
                            <p class="mb-3">No admins found.</p>
                            <a href="{{ route('admin.settings.admins.create') }}" class="btn btn-primary">Create First Admin</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="deleteAdminName"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteAdminForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="iconify me-1" data-icon="bx:bx-trash"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
(function() {
    var modalEl = document.getElementById('deleteAdminModal');
    if (!modalEl) return;
    modalEl.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        if (!button) return;
        document.getElementById('deleteAdminForm').action = button.getAttribute('data-delete-url');
        document.getElementById('deleteAdminName').textContent = button.getAttribute('data-admin-name') || 'this admin';
    });
})();
</script>
@endsection
