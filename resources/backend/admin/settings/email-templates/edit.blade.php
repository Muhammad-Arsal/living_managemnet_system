@extends('backend::admin.layouts.app')

@section('title', 'Edit Email Template')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Edit Email Template</h5>
                <div class="card-body">
                    <form action="{{ route('admin.settings.email-templates.update', $emailTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email_type" class="form-label">Email Type</label>
                                <input type="text" class="form-control bg-light" id="email_type"
                                    value="{{ $emailTemplate->email_type }}" readonly tabindex="-1">
                                <small class="text-muted">Email type cannot be changed after creation.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                    id="subject" name="subject" value="{{ old('subject', $emailTemplate->subject) }}">
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                    {{ old('status', $emailTemplate->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="html_content" class="form-label">HTML Content</label>
                            <textarea class="form-control @error('html_content') is-invalid @enderror"
                                id="html_content" name="html_content" rows="10">{{ old('html_content', $emailTemplate->html_content) }}</textarea>
                            @error('html_content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update Template</button>
                        <a href="{{ route('admin.settings.email-templates.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#html_content'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
                ]
            })
            .catch(error => console.error(error));
    </script>
@endsection
