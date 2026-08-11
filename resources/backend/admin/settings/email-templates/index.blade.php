@extends('backend::admin.layouts.app')

@section('title', 'Email Templates')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Email Templates</h5>
                    <a href="{{ route('admin.settings.email-templates.create') }}" class="btn btn-primary">
                        <i class="iconify me-1" data-icon="bx:bx-plus"></i>
                        Add New Template
                    </a>
                </div>
                <div class="card-body">
                    @if ($emailTemplates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Email Type</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($emailTemplates as $template)
                                        <tr>
                                            <td><code>{{ $template->email_type }}</code></td>
                                            <td>{{ $template->subject }}</td>
                                            <td>
                                                @if ($template->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $template->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.settings.email-templates.edit', $template) }}"
                                                        class="btn btn-sm btn-primary">Edit</a>
                                                    <form action="{{ route('admin.settings.email-templates.destroy', $template) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this template?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
                        <div class="text-center py-5">
                            <p class="text-muted">No email templates found.</p>
                            <a href="{{ route('admin.settings.email-templates.create') }}" class="btn btn-primary">Create First Template</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
