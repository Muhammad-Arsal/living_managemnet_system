@php
    $status = $meta['status'] ?? 'editable';
    $type = $meta['type'] ?? 'text';
    $options = $meta['options'] ?? [];
    $columns = $meta['columns'] ?? [];
    $required = (bool) ($meta['required'] ?? false);
    $rawValue = $fieldValues[$key] ?? (in_array($type, ['checkboxes', 'score_row'], true) ? [] : '');

    if (old('fields.'.$key) !== null) {
        $rawValue = old('fields.'.$key);
        if (in_array($type, ['checkboxes', 'score_row'], true)) {
            $status = filled($rawValue) ? 'editable' : ($required ? 'missing' : 'editable');
        } else {
            $status = filled($rawValue)
                ? (($meta['source_label'] ?? null) ? 'prefilled' : 'editable')
                : ($required ? 'missing' : 'editable');
        }
    }

    $selected = is_array($rawValue) ? $rawValue : [];
    $scoreValues = is_array($rawValue) ? $rawValue : [];
    $scalar = is_array($rawValue) ? '' : (string) $rawValue;
@endphp

<div class="col-12 @if (! in_array($type, ['textarea', 'checkboxes', 'radio', 'score_row'], true)) col-md-6 @endif">
    <label class="form-label d-flex flex-wrap align-items-center gap-2" @if (! in_array($type, ['checkboxes', 'radio', 'score_row'], true)) for="field_{{ $key }}" @endif>
        <span>{{ $meta['label'] }} @if ($required) <span class="text-danger">*</span> @endif</span>
        <span class="lms-field-badge lms-field-badge--{{ $status }}">
            @if ($status === 'prefilled')
                Prefilled{{ ($meta['source_label'] ?? null) ? ' · '.$meta['source_label'] : '' }}
            @elseif ($status === 'missing')
                Missing
            @else
                Manual
            @endif
        </span>
    </label>

    @if ($type === 'score_row')
        <div class="lms-score-row @error('fields.'.$key) is-invalid @enderror">
            @foreach ($columns as $columnKey => $columnLabel)
                <div class="lms-score-row__cell">
                    <label class="form-label small mb-1" for="field_{{ $key }}_{{ $columnKey }}">{{ $columnLabel }}</label>
                    <input
                        type="text"
                        name="fields[{{ $key }}][{{ $columnKey }}]"
                        id="field_{{ $key }}_{{ $columnKey }}"
                        value="{{ $scoreValues[$columnKey] ?? '' }}"
                        class="form-control"
                    >
                </div>
            @endforeach
        </div>
    @elseif ($type === 'checkboxes')
        <div class="lms-option-grid @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif" role="group" aria-label="{{ $meta['label'] }}">
            @foreach ($options as $optionValue => $optionLabel)
                <label class="lms-option-chip">
                    <input
                        type="checkbox"
                        name="fields[{{ $key }}][]"
                        value="{{ $optionValue }}"
                        @checked(in_array((string) $optionValue, array_map('strval', $selected), true))
                    >
                    <span>{{ $optionLabel }}</span>
                </label>
            @endforeach
        </div>
    @elseif ($type === 'radio')
        <div class="lms-option-grid lms-option-grid--radio @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif" role="radiogroup" aria-label="{{ $meta['label'] }}">
            @foreach ($options as $optionValue => $optionLabel)
                <label class="lms-option-chip">
                    <input
                        type="radio"
                        name="fields[{{ $key }}]"
                        value="{{ $optionValue }}"
                        @checked($scalar === (string) $optionValue)
                        @if ($required) required @endif
                    >
                    <span>{{ $optionLabel }}</span>
                </label>
            @endforeach
        </div>
    @elseif ($type === 'select')
        <select
            name="fields[{{ $key }}]"
            id="field_{{ $key }}"
            class="form-select @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif"
            @if ($required) required @endif
        >
            <option value="">Select…</option>
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected($scalar === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea
            name="fields[{{ $key }}]"
            id="field_{{ $key }}"
            rows="3"
            class="form-control @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif"
            @if ($required) required @endif
        >{{ $scalar }}</textarea>
    @elseif ($type === 'date')
        <input
            type="date"
            name="fields[{{ $key }}]"
            id="field_{{ $key }}"
            value="{{ $scalar }}"
            class="form-control @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif"
            @if ($required) required @endif
        >
    @else
        <input
            type="text"
            name="fields[{{ $key }}]"
            id="field_{{ $key }}"
            value="{{ $scalar }}"
            class="form-control @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif"
            @if ($required) required @endif
        >
    @endif

    @error('fields.'.$key)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
