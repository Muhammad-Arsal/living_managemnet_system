@php
    $selected = $selected ?? [];
    if (is_string($selected) && $selected !== '') {
        $selected = [$selected];
    }
    if (! is_array($selected)) {
        $selected = [];
    }
    $selected = array_map('strval', $selected);
    $options = $options ?? [];
@endphp
<ul class="option-list">
    @foreach ($options as $value => $label)
        @php
            $checked = in_array((string) $value, $selected, true) || in_array((string) $label, $selected, true);
        @endphp
        <li>{{ $checked ? '☑' : '☐' }} {{ $label }}</li>
    @endforeach
</ul>
