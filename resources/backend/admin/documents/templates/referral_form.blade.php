@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
    @endphp

    <h1>REFERRAL &amp; SUPPORT ASSESSMENT FORM</h1>
    <p><span class="label">Organisation:</span> {{ $f['organisation'] ?? '' }}</p>

    <h2>1. Client Details</h2>
    <p class="meta-row"><span class="label">Full Name:</span> {{ $f['full_name'] ?? '' }}</p>
    <p class="meta-row"><span class="label">DOB &amp; Age:</span> {{ $f['dob_age'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Financial Status:</span></p>
    <p class="muted">Eg receiving benefits, working, whether entitled to public funds</p>
    <div class="box">{{ $f['financial_status'] ?? '' }}</div>
    <p class="meta-row"><span class="label">NI Number:</span> {{ $f['ni_number'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Telephone:</span> {{ $f['telephone'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Gender:</span> {{ $f['gender'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Email:</span> {{ $f['email'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Client Number:</span> {{ $f['client_number'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Previous Contact with organisation:</span> {{ $f['previous_contact'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Current Address:</span></p>
    <p class="muted">Including housing status e.g. tenant, living with family. If no current address please give "care of" address where information can be sent to</p>
    <div class="box">{{ $f['current_address'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Landlord Name &amp; Contact:</span> {{ $f['landlord_contact'] ?? '' }}</p>
    <p class="label">Reason Support Required:</p>
    <div class="box">{{ $f['reason_support'] ?? '' }}</div>

    <h2>2. Current Accommodation</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['current_accommodation']['options'] ?? [],
        'selected' => $f['current_accommodation'] ?? [],
    ])
    <p class="label">Further information:</p>
    <div class="box">{{ $f['current_accommodation_further'] ?? '' }}</div>

    <h2>3. Current Circumstances</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['current_circumstances']['options'] ?? [],
        'selected' => $f['current_circumstances'] ?? [],
    ])
    <p class="label">Further information:</p>
    <div class="box">{{ $f['current_circumstances_further'] ?? '' }}</div>

    <h2>4. Support Needs</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['support_needs']['options'] ?? [],
        'selected' => $f['support_needs'] ?? [],
    ])
    <p class="label">Further information:</p>
    <div class="box">{{ $f['support_needs_further'] ?? '' }}</div>

    <h2>5. Health &amp; Risk</h2>
    <p class="meta-row"><span class="label">Translation Language:</span> {{ $f['translation_language'] ?? '' }}</p>
    <p class="label">Health Issues:</p>
    <div class="box">{{ $f['health_issues'] ?? '' }}</div>
    <p class="meta-row"><span class="label">GP/CPN:</span> {{ $f['gp_cpn'] ?? '' }}</p>
    <p class="meta-row"><span class="label">GP Telephone:</span> {{ $f['gp_telephone'] ?? '' }}</p>
    <p class="label">GP Address:</p>
    <div class="box">{{ $f['gp_address'] ?? '' }}</div>
    <p class="label">Medication:</p>
    <div class="box">{{ $f['medication'] ?? '' }}</div>
    <p class="label">Risk Factors:</p>
    <div class="box">{{ $f['risk_factors'] ?? '' }}</div>
    <p class="label">Other Agencies Involved:</p>
    <div class="box">{{ $f['other_agencies'] ?? '' }}</div>

    <h2>6. Referrer Details</h2>
    <p class="meta-row"><span class="label">Referring Agency:</span> {{ $f['referring_agency'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Referrer Name:</span> {{ $f['referrer_name'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Contact Number:</span> {{ $f['referrer_phone'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Referrer Email:</span> {{ $f['referrer_email'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Application Date:</span> {{ $f['application_date'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Referral Date:</span> {{ $f['referral_date'] ?? '' }}</p>

    <h2>7. Safeguarding</h2>
    <p class="label">Any safeguarding concerns or risks:</p>
    <div class="box">{{ $f['safeguarding'] ?? '' }}</div>

    <h2>8. Emergency Contact</h2>
    <p class="meta-row"><span class="label">Name:</span> {{ $f['emergency_name'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Relationship:</span> {{ $f['emergency_relationship'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Telephone:</span> {{ $f['emergency_telephone'] ?? '' }}</p>

    <h2>9. Equality &amp; Diversity (Optional)</h2>
    <p class="meta-row"><span class="label">Ethnicity:</span> {{ $f['ethnicity'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Religion/Belief:</span> {{ $f['religion'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Disability:</span> {{ $f['disability'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Preferred Language:</span> {{ $f['preferred_language'] ?? '' }}</p>

    <h2>10. Consent &amp; Declaration</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['consent']['options'] ?? [],
        'selected' => $f['consent'] ?? [],
    ])
    <p class="signature-line"><span class="label">Client Signature:</span> __________________ &nbsp;&nbsp; <span class="label">Date:</span> __________</p>
    <p class="signature-line"><span class="label">Referrer Signature:</span> ________________ &nbsp;&nbsp; <span class="label">Date:</span> __________</p>

    <h2>Office Use Only</h2>
    <p class="meta-row"><span class="label">Assessment Date:</span> {{ $f['office_assessment_date'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Outcome:</span> {{ $f['office_outcome'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Allocated Worker:</span> {{ $f['office_allocated_worker'] ?? '' }}</p>
@endsection
