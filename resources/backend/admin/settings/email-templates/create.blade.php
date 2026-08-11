@extends('backend::admin.layouts.app')

@section('title', 'Create Email Template')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Create Email Template</h5>
                <div class="card-body">
                    <form action="{{ route('admin.settings.email-templates.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email_type" class="form-label">Email Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('email_type') is-invalid @enderror"
                                    id="email_type" name="email_type" value="{{ old('email_type') }}"
                                    placeholder="e.g. welcome_email">
                                <small class="text-muted">Lowercase letters, numbers, and underscores only.</small>
                                @error('email_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                    id="subject" name="subject" value="{{ old('subject') }}">
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                    {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="html_content" class="form-label">HTML Content</label>
                            <textarea class="form-control @error('html_content') is-invalid @enderror"
                                id="html_content" name="html_content" rows="10">{{ old('html_content') }}</textarea>
                            <small class="text-muted">Use placeholders like @{{name}}, @{{email}}, @{{reset_link}}.</small>
                            @error('html_content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Create Template</button>
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
