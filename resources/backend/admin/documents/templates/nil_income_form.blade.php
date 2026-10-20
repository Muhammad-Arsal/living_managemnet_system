@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
    @endphp

    <h1>Nil Income Form (RH-6)</h1>

    <p>I Mr / Mrs / Miss <strong>{{ $f['claimant_name'] ?? '' }}</strong> confirm that I had no income</p>
    <p class="meta-row"><span class="label">From</span> {{ $f['period_from'] ?? '' }} <span class="label">To</span> {{ $f['period_to'] ?? '' }}</p>
    <p class="muted">DD/MM/YYYY &nbsp;&nbsp; DD/MM/YYYY</p>

    <p class="label">Address:</p>
    <div class="box">{{ $f['address'] ?? '' }}</div>

    <h2>I manage to support myself by:</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['support_methods']['options'] ?? [],
        'selected' => $f['support_methods'] ?? [],
    ])

    <p class="label">Please give details below:</p>
    <div class="box">{{ $f['support_details'] ?? '' }}</div>

    <p class="label">I confirm the total value of my savings for the period were:</p>
    <div class="box">{{ $f['savings_value'] ?? '' }}</div>

    <p class="label">Other / Additional information:</p>
    <div class="box">{{ $f['other_information'] ?? '' }}</div>

    <div class="signature">
        <p class="signature-line"><span class="label">Signature of person claiming:</span> ______________________________</p>
        <p class="meta-row"><span class="label">Date:</span> {{ $f['claimant_date'] ?? '' }}</p>
    </div>

    <h2>If this form has been filled in by someone other than the person claiming, please state why?</h2>
    <div class="box">{{ $f['filled_by_reason'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Name of the person who filled in this form:</span> {{ $f['filled_by_name'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Relationship to the person claiming:</span> {{ $f['filled_by_relationship'] ?? '' }}</p>
    <p class="signature-line"><span class="label">Signature of person completing the form:</span> ______________________________</p>
    <p class="meta-row"><span class="label">Date:</span> {{ $f['filled_by_date'] ?? '' }}</p>
@endsection
