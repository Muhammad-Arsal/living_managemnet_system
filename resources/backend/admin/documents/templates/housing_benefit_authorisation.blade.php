@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $provider = $f['provider_name'] ?? '';
    @endphp

    <h1>Housing Benefit Authorisation Form (RH-4)</h1>

    <p class="muted">Coventry City Council – Benefits Service<br>
        PO Box 7097<br>
        Coventry<br>
        CV6 9SL</p>
    <p class="muted">Telephone: 024 7683 1800 &nbsp; Email: benefits@coventry.gov.uk</p>

    <p>To whom it may concern,</p>

    <p>I <strong>{{ $f['resident_name'] ?? '' }}</strong> of</p>
    <div class="box">{{ $f['address'] ?? '' }}</div>

    <p class="meta-row"><span class="label">Date of Birth:</span> {{ $f['date_of_birth'] ?? '' }}</p>
    <p class="meta-row"><span class="label">National Insurance Number:</span> {{ $f['ni_number'] ?? '' }}</p>

    <p>hereby give full consent and authorisation to Coventry City Council to make enquiries
        with the Department of Work and Pensions in relation to the benefit I currently receive, and to
        discuss all matters and aspects of my claim in relation to my Housing Benefit with staff from
        <strong>{{ $provider }}</strong></p>

    <p>Kind regards</p>

    <div class="signature">
        <p class="signature-line"><span class="label">Signature:</span> ______________________________</p>
        <p class="meta-row"><span class="label">Date:</span> {{ $f['authorisation_date'] ?? '' }}</p>
    </div>
@endsection
