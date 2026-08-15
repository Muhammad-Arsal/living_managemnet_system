@extends('backend::admin.layouts.app')

@section('title', 'Edit Ticket Priority')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Edit Ticket Priority</h5>
                <div class="card-body">
                    <p class="text-muted">Deactivating hides this priority from new tickets. Existing tickets keep the historical reference.</p>
                    <form action="{{ route('admin.settings.ticket-priorities.update', $ticketPriority) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $ticketPriority->name) }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $ticketPriority->sort_order) }}">
                                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $ticketPriority->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Priority</button>
                        <a href="{{ route('admin.settings.ticket-priorities.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
