@extends('backend::admin.layouts.app')

@section('title', 'Email Templates')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Settings</p>
                        <h5 class="lms-page-header__title">Email Templates</h5>
                        <p class="lms-page-header__subtitle">Customize welcome, verification, and password emails for each portal.</p>
                    </div>
                    <a href="{{ route('admin.settings.email-templates.create') }}" class="btn btn-primary lms-btn-add">
                        <i class="iconify" data-icon="bx:bx-plus"></i>
                        Add New Template
                    </a>
                </div>
                <div class="card-body">
                    @if ($emailTemplates->count() > 0)
                        <div class="table-responsive lms-table-shell">
                            <table class="table table-hover lms-data-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Template</th>
                                        <th>Status</th>
                                        <th class="d-none d-md-table-cell">Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($emailTemplates as $template)
                                        <tr>
                                            <td>
                                                <div class="lms-person__meta">
                                                    <span class="lms-person__name"><code>{{ $template->email_type }}</code></span>
                                                    <span class="lms-person__email">{{ $template->subject }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($template->status)
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
                                            <td class="d-none d-md-table-cell">
                                                <span class="lms-muted-date">{{ $template->created_at->format('M d, Y') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="lms-actions justify-content-end">
                                                    <a href="{{ route('admin.settings.email-templates.edit', $template) }}"
                                                        class="lms-action-btn lms-action-btn--edit"
                                                        title="Edit template">
                                                        <i class="iconify" data-icon="bx:bx-edit-alt"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <form action="{{ route('admin.settings.email-templates.destroy', $template) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        data-confirm-title="Delete email template"
                                                        data-confirm-body="Delete the {{ $template->email_type }} template? This cannot be undone."
                                                        data-confirm-submit="Delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="lms-action-btn lms-action-btn--delete" title="Delete template">
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
                        <div class="mt-3">{{ $emailTemplates->links() }}</div>
                    @else
                        <div class="lms-empty-state">
                            <div class="lms-empty-state__icon">
                                <i class="iconify" data-icon="bx:bx-envelope"></i>
                            </div>
                            <p class="mb-3">No email templates found.</p>
                            <a href="{{ route('admin.settings.email-templates.create') }}" class="btn btn-primary">Create First Template</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
