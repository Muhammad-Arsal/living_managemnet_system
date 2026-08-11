@extends('backend::admin.layouts.app')

@section('title', 'Council')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Council</h5>
                    <a href="{{ route('admin.council.create') }}" class="btn btn-primary">
                        <i class="iconify me-1" data-icon="bx:bx-plus"></i>
                        Add New Council Member
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.council.index') }}" class="row g-3 mb-4 align-items-end">
                        <div class="col-md-3">
                            <label for="column" class="form-label">Select By Column</label>
                            <select name="column" id="column" class="form-select">
                                <option value="">Select By Column</option>
                                @foreach ($filterColumns as $value => $label)
                                    <option value="{{ $value }}" @selected(request('column') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="iconify" data-icon="bx:bx-search"></i></span>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-5 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.council.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>

                    @if ($councils->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Verified</th>
                                        <th>Active</th>
                                        <th>Last Login</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($councils as $council)
                                        <tr>
                                            <td>{{ $council->name }}</td>
                                            <td>{{ $council->email }}</td>
                                            <td>
                                                @if ($council->hasVerifiedEmail())
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-warning">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($council->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $council->last_login_at?->format('M d, Y H:i') ?? '—' }}</td>
                                            <td>{{ $council->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.council.edit', $council) }}" class="btn btn-sm btn-primary">Edit</a>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteCouncilModal"
                                                        data-delete-url="{{ route('admin.council.destroy', $council) }}"
                                                        data-council-name="{{ $council->name }}">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $councils->links() }}</div>
                    @else
                        <div class="text-center py-5">
                            @if (request()->filled('search') || request()->filled('column'))
                                <p class="text-muted">No council members match your search.</p>
                                <a href="{{ route('admin.council.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @else
                                <p class="text-muted">No council members found.</p>
                                <a href="{{ route('admin.council.create') }}" class="btn btn-primary">Create First Council Member</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteCouncilModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Council Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="deleteCouncilName"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteCouncilForm" method="POST" class="d-inline">
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
    var modalEl = document.getElementById('deleteCouncilModal');
    if (!modalEl) return;
    modalEl.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        if (!button) return;
        document.getElementById('deleteCouncilForm').action = button.getAttribute('data-delete-url');
        document.getElementById('deleteCouncilName').textContent = button.getAttribute('data-council-name') || 'this council member';
    });
})();
</script>
@endsection
