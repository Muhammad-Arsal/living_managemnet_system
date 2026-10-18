@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
    @endphp

    <h1>Incident Form (RH-15)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Provider name:</span> {{ $f['provider_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Name of person completing form:</span> {{ $f['completed_by'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date of completion:</span> {{ $f['date_of_completion'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Email address:</span> {{ $f['email'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Contact Number:</span> {{ $f['contact_number'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address of incident / location:</span> {{ $f['incident_location'] ?? '' }}</p>
    </div>

    <h2>Incident details</h2>
    <p class="muted">How the incident happened, factors leading to the event and what took place. Be as specific as possible, including dates and times where applicable.</p>
    <div class="box">{{ $f['incident_details'] ?? '' }}</div>

    <h2>Name of person/people involved or witness to the incident</h2>
    <p class="muted">Please provide names and contact numbers if possible.</p>
    <div class="box">{{ $f['persons_involved'] ?? '' }}</div>

    <h2>Was there emergency service involvement? (Check if relevant)</h2>
    <p class="muted">If yes, please give details, names and reference numbers if possible.</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['emergency_services']['options'] ?? [],
        'selected' => $f['emergency_services'] ?? [],
    ])
    <div class="box">{{ $f['emergency_details'] ?? '' }}</div>

    <h2>Is there any follow up action required?</h2>
    <p class="muted">If yes, please give details below.</p>
    <div class="box">{{ $f['follow_up_details'] ?? '' }}</div>

    <h2>Any other comments:</h2>
    <div class="box">{{ $f['other_comments'] ?? '' }}</div>

    <div class="signature">
        <p class="signature-line"><span class="label">Signature of person completing the form:</span> ______________________________</p>
    </div>
@endsection
