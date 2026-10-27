@extends('backend::admin.documents.templates.layout')

@section('content')
    @php $f = $fields; @endphp

    <h1>Risk Assessment for Staff Eyes Only (RH-10)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
    </div>

    <p>When the Resident has completed the risk assessment there may be scores that you disagree with.</p>
    <p>There may also be times when you feel you cannot discuss a certain risk with a Resident. An example of this may be if the resident has a history of sexual offences.</p>
    <p>Please complete a Risk Management Plan below for each of the risks that fall into either of the above categories.</p>

    @for ($i = 1; $i <= 2; $i++)
        <div class="section mt">
            <p class="meta-row"><span class="label">Risk:</span> {{ $f['risk_'.$i.'_name'] ?? '' }}</p>
            <p class="meta-row"><span class="label">Resident score:</span> {{ $f['risk_'.$i.'_resident_score'] ?? '' }}</p>
            <p class="meta-row"><span class="label">Staff score:</span> {{ $f['risk_'.$i.'_staff_score'] ?? '' }}</p>
            <p class="label">Why do you disagree with the Residents score?</p>
            <div class="box">{{ $f['risk_'.$i.'_disagree_reason'] ?? '' }}</div>
            <p class="label">Please explain why you have chosen the score:</p>
            <div class="box">{{ $f['risk_'.$i.'_why_score'] ?? '' }}</div>
            <p class="label">Are there any triggers associated with the risk?</p>
            <div class="box">{{ $f['risk_'.$i.'_triggers'] ?? '' }}</div>
            <p class="label">Are there any ways we can minimize the risk? (Risk Management Plan)</p>
            <div class="box">{{ $f['risk_'.$i.'_plan'] ?? '' }}</div>
        </div>
    @endfor

    <h2>Any further comments:</h2>
    <div class="box">{{ $f['further_comments'] ?? '' }}</div>
@endsection
