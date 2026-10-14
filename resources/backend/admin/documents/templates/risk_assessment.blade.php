@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
        $scoreCell = function (string $key, string $column) use ($f): string {
            $row = $f[$key] ?? [];

            return is_array($row) ? (string) ($row[$column] ?? '') : '';
        };
        $risks = [
            ['key' => 'risk_violence', 'title' => '1. Risk of violence & aggression'],
            ['key' => 'risk_harm', 'title' => '2. Risk of harm to/from others'],
            ['key' => 'risk_self_neglect', 'title' => '3. Risk of self-neglect or self-harm'],
            ['key' => 'risk_mental', 'title' => '4. Risk of deterioration in mental and emotional health'],
            ['key' => 'risk_physical', 'title' => '5. Risk of deterioration in physical health'],
            ['key' => 'risk_substance', 'title' => '6. Risk associated with substance misuse or other addiction'],
            ['key' => 'risk_financial', 'title' => '7. Risk of financial hardship or difficulties managing finances'],
            ['key' => 'risk_offending', 'title' => '8. Risk of offending and/or anti-social behaviour'],
            ['key' => 'risk_learning', 'title' => '9. Risk associated with learning, understanding or communication difficulties'],
            ['key' => 'risk_other_1', 'title' => '10. Other (please specify):', 'specify' => 'risk_other_1_specify'],
            ['key' => 'risk_other_2', 'title' => '11. Other (please specify):', 'specify' => 'risk_other_2_specify'],
        ];
    @endphp

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

    @foreach ($risks as $risk)
        <h3>{{ $risk['title'] }}@if (! empty($risk['specify'])) {{ $f[$risk['specify']] ?? '' }}@endif</h3>
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
                    <td>{{ $scoreCell($risk['key'], 'initial') }}</td>
                    <td>{{ $scoreCell($risk['key'], 'm3') }}</td>
                    <td>{{ $scoreCell($risk['key'], 'm6') }}</td>
                    <td>{{ $scoreCell($risk['key'], 'm9') }}</td>
                    <td>{{ $scoreCell($risk['key'], 'm12') }}</td>
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
