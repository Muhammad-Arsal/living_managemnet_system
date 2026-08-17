@php
    $documentMimes = implode(', ', config('documents.mimes', []));
    $documentMaxMb = number_format(((int) config('documents.max_kilobytes', 10240)) / 1024, 0);
    $documentMaxFiles = (int) config('documents.max_files', 10);
    $inputId = $inputId ?? 'documents';
    $required = $required ?? false;
@endphp
<p class="text-muted small">{{ $required ? '' : 'Optional. ' }}Up to {{ $documentMaxFiles }} files, {{ $documentMaxMb }} MB each. Allowed: {{ $documentMimes }}.</p>
<input type="file"
    class="form-control mb-3 @error('documents') is-invalid @enderror @error('documents.*') is-invalid @enderror"
    id="{{ $inputId }}"
    name="documents[]"
    multiple>
@error('documents')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
@error('documents.*')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
