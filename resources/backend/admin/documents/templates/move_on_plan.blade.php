@extends('backend::admin.documents.templates.layout')

@section('content')
    @php $f = $fields; @endphp

    <p class="center muted">SUPPORTED EXEMPT ACCOMMODATION (SEA)</p>
    <h1>RESIDENT MOVE-ON PLAN</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Provider Name:</span> {{ $f['provider_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Property Address:</span> {{ $f['property_address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Resident Name:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date of Birth:</span> {{ $f['date_of_birth'] ?? '' }}</p>
        <p class="meta-row"><span class="label">National Insurance Number:</span> {{ $f['ni_number'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Support Worker:</span> {{ $f['support_worker'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date Move-On Plan Started:</span> {{ $f['plan_started_on'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Review Date:</span> {{ $f['review_date'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Target Move-On Date:</span> {{ $f['target_move_on_date'] ?? '' }}</p>
    </div>

    <h2>PURPOSE OF THIS PLAN</h2>
    <p>This Move-On Plan has been developed to support the resident in progressing towards greater independence and securing suitable long-term accommodation. The plan will be reviewed regularly and updated to reflect progress and changing needs.</p>

    <h2>CURRENT HOUSING SITUATION</h2>
    <p class="meta-row"><span class="label">Current Accommodation Type:</span> {{ $f['accommodation_type'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Date Resident Moved In:</span> {{ $f['moved_in_on'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Length of Stay to Date:</span> {{ $f['length_of_stay'] ?? '' }}</p>
    <p class="label">Reason for Placement:</p>
    <div class="box">{{ $f['reason_for_placement'] ?? '' }}</div>

    <h2>LONG-TERM HOUSING GOAL</h2>
    <p class="meta-row"><span class="label">Target accommodation type:</span> {{ $f['housing_goal_type'] ?? '' }}</p>
    <p class="label">Resident's Long-Term Goal:</p>
    <div class="box">{{ $f['long_term_goal'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Target Timescale:</span> {{ $f['target_timescale'] ?? '' }}</p>

    <h2>MOVE-ON OBJECTIVES</h2>
    <p class="muted">Cover tenancy sustainment, independent living skills, health and wellbeing, employment/education/training, financial stability, and community engagement.</p>
    <div class="box">{{ $f['objectives_notes'] ?? '' }}</div>

    <h2>BARRIERS TO MOVE-ON</h2>
    <div class="box">{{ $f['barriers'] ?? '' }}</div>

    <h2>SUPPORT PROVIDED</h2>
    <div class="box">{{ $f['support_provided'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Frequency of Support:</span> {{ $f['support_frequency'] ?? '' }}</p>

    <h2>DECLARATION</h2>
    <p>I confirm that this Move-On Plan has been discussed with me and I understand the goals and actions agreed.</p>
    <p class="signature-line"><span class="label">Resident Name:</span> {{ $f['resident_name'] ?? '' }}</p>
    <p class="signature-line"><span class="label">Resident Signature:</span> __________________ &nbsp;&nbsp; <span class="label">Date:</span> __________</p>
    <p class="signature-line"><span class="label">Support Worker Name:</span> {{ $f['support_worker'] ?? '' }}</p>
    <p class="signature-line"><span class="label">Support Worker Signature:</span> _____________ &nbsp;&nbsp; <span class="label">Date:</span> ________</p>
    <p class="signature-line"><span class="label">Manager Signature:</span> ___________________ &nbsp;&nbsp; <span class="label">Date:</span> _________</p>
@endsection
