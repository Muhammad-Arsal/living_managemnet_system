@php
    $attachmentMimes = implode(', ', config('tickets.attachments.mimes', []));
    $attachmentMaxMb = number_format(((int) config('tickets.attachments.max_kilobytes', 10240)) / 1024, 0);
    $attachmentMaxFiles = (int) config('tickets.attachments.max_files', 10);
@endphp
<div class="mb-3">
    <label for="attachments" class="form-label">Attachments</label>
    <input type="file"
        class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
        id="attachments"
        name="attachments[]"
        multiple>
    <small class="text-muted d-block mt-1">
        Optional. Up to {{ $attachmentMaxFiles }} files, {{ $attachmentMaxMb }} MB each.
        Allowed: {{ $attachmentMimes }}.
    </small>
    @error('attachments')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('attachments.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
