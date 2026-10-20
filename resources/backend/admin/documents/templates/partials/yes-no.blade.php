@php
    $value = $value ?? null;
@endphp
@if ($value === 'Yes')
    ☑ Yes  ☐ No
@elseif ($value === 'No')
    ☐ Yes  ☑ No
@else
    ☐ Yes  ☐ No
@endif
