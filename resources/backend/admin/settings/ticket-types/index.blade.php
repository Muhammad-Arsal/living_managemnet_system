@extends('backend::admin.layouts.app')

@section('title', 'Ticket Types')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Settings</p>
                        <h5 class="lms-page-header__title">Ticket Types</h5>
                        <p class="lms-page-header__subtitle">Manage the type options shown when creating tickets.</p>
                    </div>
                    <a href="{{ route('admin.settings.ticket-types.create') }}" class="btn btn-primary lms-btn-add">
                        <i class="iconify" data-icon="bx:bx-plus"></i>
                        Add Ticket Type
                    </a>
                </div>
                <div class="card-body">
                    @if ($ticketTypes->count() > 0)
                        <div class="table-responsive lms-table-shell">
                            <table class="table table-hover lms-data-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th class="d-none d-md-table-cell">Order</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ticketTypes as $ticketType)
                                        <tr>
                                            <td>
                                                <span class="lms-person__name">{{ $ticketType->name }}</span>
                                            </td>
                                            <td>
                                                @if ($ticketType->is_active)
                                                    <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>Active</span>
                                                @else
                                                    <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>Inactive</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-md-table-cell">{{ $ticketType->sort_order }}</td>
                                            <td class="text-end">
                                                <div class="lms-actions justify-content-end">
                                                    <a href="{{ route('admin.settings.ticket-types.edit', $ticketType) }}" class="lms-action-btn lms-action-btn--edit">
                                                        <i class="iconify" data-icon="bx:bx-edit-alt"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <form action="{{ route('admin.settings.ticket-types.destroy', $ticketType) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        data-confirm-title="Delete ticket type"
                                                        data-confirm-body="Delete {{ $ticketType->name }}? Types already used by tickets cannot be deleted."
                                                        data-confirm-submit="Delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="lms-action-btn lms-action-btn--delete">
                                                            <i class="iconify" data-icon="bx:bx-trash"></i>
                                                            <span>Delete</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $ticketTypes->links() }}</div>
                    @else
                        <div class="lms-empty-state">
                            <div class="lms-empty-state__icon">
                                <i class="iconify" data-icon="bx:bx-category"></i>
                            </div>
                            <p class="mb-3">No ticket types found.</p>
                            <a href="{{ route('admin.settings.ticket-types.create') }}" class="btn btn-primary">Create First Type</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
