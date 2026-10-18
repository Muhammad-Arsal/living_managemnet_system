@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
        $yn = function (?string $value): string {
            if ($value === 'Yes') {
                return '☑ Yes  ☐ No';
            }
            if ($value === 'No') {
                return '☐ Yes  ☑ No';
            }

            return '☐ Yes  ☐ No';
        };
        $yna = function (?string $value): string {
            return match ($value) {
                'Yes' => '☑ Yes  ☐ No  ☐ N/A',
                'No' => '☐ Yes  ☑ No  ☐ N/A',
                'N/A' => '☐ Yes  ☐ No  ☑ N/A',
                default => '☐ Yes  ☐ No  ☐ N/A',
            };
        };
    @endphp

    <h1>Resident Exit Form (RH-20)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Resident name:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Name of person completing form:</span> {{ $f['completed_by'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date of completion:</span> {{ $f['date_of_completion'] ?? '' }}</p>
    </div>

    <h2>Why has the resident left the accommodation?</h2>
    <p class="muted">Please tick the relevant reason</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['leave_reasons']['options'] ?? [],
        'selected' => $f['leave_reasons'] ?? [],
    ])
    @if (! empty($f['leave_reason_other']))
        <p class="meta-row"><span class="label">Other (please specify):</span> {{ $f['leave_reason_other'] }}</p>
    @endif

    <h2>Residents new address (if disclosed)</h2>
    <div class="box">{{ $f['new_address'] ?? '' }}</div>

    <p class="meta-row"><span class="label">Has the housing benefit claim been cancelled?</span> {{ $yn($f['housing_benefit_cancelled'] ?? null) }}</p>
    <p class="meta-row"><span class="label">Did the resident leave any belongings?</span> {{ $yn($f['left_belongings'] ?? null) }}</p>

    <p class="label">If yes please list below.</p>
    <div class="box">{{ $f['belongings_list'] ?? '' }}</div>

    <p class="meta-row"><span class="label">If contact has been lost with the resident, was the next of kin contacted?</span> {{ $yna($f['next_of_kin_contacted'] ?? null) }}</p>
    <p class="meta-row"><span class="label">Is any follow up action required?</span> {{ $yn($f['follow_up_required'] ?? null) }}</p>

    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['follow_up_details'] ?? '' }}</div>
@endsection
