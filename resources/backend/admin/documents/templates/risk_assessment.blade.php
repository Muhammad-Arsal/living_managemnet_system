@extends('backend::admin.documents.templates.layout')

@section('content')
    @php $f = $fields; @endphp

    <h1>Your Risk Assessment (RH-9)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Telephone Number:</span> {{ $f['telephone'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Email Address:</span> {{ $f['email'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Name of Support Worker:</span> {{ $f['support_worker'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date:</span> {{ $f['assessment_date'] ?? '' }}</p>
    </div>

    <h2>Key Review Dates</h2>
    <p class="meta-row"><span class="label">3 Monthly Review:</span> {{ $f['review_3_month'] ?? '' }}</p>
    <p class="meta-row"><span class="label">6 Monthly Review:</span> {{ $f['review_6_month'] ?? '' }}</p>
    <p class="meta-row"><span class="label">9 Monthly Review:</span> {{ $f['review_9_month'] ?? '' }}</p>
    <p class="meta-row"><span class="label">12 Monthly Review:</span> {{ $f['review_12_month'] ?? '' }}</p>

    <p class="muted">Please refer to RH-8 Risk Assessment Guidance before completing the sections below.</p>

    <h2>Welcome to your Support Plan</h2>
    <p>We at {{ $f['provider_name'] ?? '' }} want to help keep you safe. For us to do this, we need to understand what possible risks you may encounter during your stay with us.</p>
    <p>Identifying risk related behaviour and situations.</p>

    @php
        $risks = [
            'risk_violence' => '1. Risk of violence & aggression',
            'risk_harm' => '2. Risk of harm to/from others',
            'risk_self_neglect' => '3. Risk of self-neglect or self-harm',
            'risk_mental' => '4. Risk of deterioration in mental and emotional health',
            'risk_physical' => '5. Risk of deterioration in physical health',
            'risk_substance' => '6. Risk associated with substance misuse or other addiction',
            'risk_financial' => '7. Risk of financial hardship or difficulties managing finances',
            'risk_offending' => '8. Risk of offending and/or anti-social behaviour',
            'risk_learning' => '9. Risk associated with learning, understanding or communication difficulties',
            'risk_other' => '10–11. Other (please specify)',
        ];
    @endphp

    @foreach ($risks as $key => $title)
        <h3>{{ $title }}</h3>
        <table class="risk">
            <thead>
                <tr>
                    <th style="width:20%">Initial Score</th>
                    <th style="width:20%">3 Month Review</th>
                    <th style="width:20%">6 Month Review</th>
                    <th style="width:20%">9 Month Review</th>
                    <th style="width:20%">12 Month Review</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5">{{ $f[$key] ?? '' }}</td>
                </tr>
            </tbody>
        </table>
        <p class="muted">New risk assessment to be completed as required.</p>
    @endforeach

    <div class="signature">
        <p class="signature-line"><span class="label">Resident Signature:</span> ______________________________</p>
        <p class="signature-line"><span class="label">Date:</span> {{ $f['assessment_date'] ?? '' }}</p>
    </div>
@endsection
