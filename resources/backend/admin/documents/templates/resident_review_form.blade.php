@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
    @endphp

    <h1>Resident Review Form (RH-12)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date of Completion:</span> {{ $f['date_of_completion'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
    </div>

    <h2>Support Questions</h2>

    <p class="meta-row"><span class="label">Are you happy with the support provided?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['happy_with_support'] ?? null])</p>
    <p class="label">Please explain why:</p>
    <div class="box">{{ $f['happy_with_support_why'] ?? '' }}</div>

    <p class="label">How often do you see your support worker?</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['see_support_worker']['options'] ?? [],
        'selected' => $f['see_support_worker'] ?? null,
    ])

    <p class="label">What are your current 5 support goals and how do you feel you are progressing?</p>
    <p class="meta-row"><span class="label">1.</span> {{ $f['goal_1'] ?? '' }}</p>
    <p class="meta-row"><span class="label">2.</span> {{ $f['goal_2'] ?? '' }}</p>
    <p class="meta-row"><span class="label">3.</span> {{ $f['goal_3'] ?? '' }}</p>
    <p class="meta-row"><span class="label">4.</span> {{ $f['goal_4'] ?? '' }}</p>
    <p class="meta-row"><span class="label">5.</span> {{ $f['goal_5'] ?? '' }}</p>

    <p class="label">What do you hope to achieve in the next 3 months?</p>
    <div class="box">{{ $f['next_3_months'] ?? '' }}</div>

    <p class="meta-row"><span class="label">Is there anything else you would like to discuss?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['anything_else'] ?? null])</p>
    <p class="label">If yes, please give details below:</p>
    <div class="box">{{ $f['anything_else_details'] ?? '' }}</div>

    <div class="signature">
        <p class="signature-line"><span class="label">Resident Signature:</span> ______________________________</p>
        <p class="meta-row"><span class="label">Support Worker Name:</span> {{ $f['support_worker_name'] ?? '' }}</p>
        <p class="signature-line"><span class="label">Support Worker Signature:</span> ______________________________</p>
    </div>
@endsection
