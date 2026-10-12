@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $provider = $f['provider_name'] ?? '';
    @endphp

    <h1>{{ $provider }}</h1>
    <p class="center"><strong>Weekly Service Charge Notice</strong></p>

    <div class="meta">
        <p class="meta-row"><span class="label">Date Issued:</span> {{ $f['date_issued'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Tenant Name:</span> {{ $f['tenant_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Property Address:</span> {{ $f['property_address'] ?? '' }}</p>
    </div>

    <p>Dear {{ $f['dear_name'] ?? '' }},</p>

    <h2>Weekly Service Charge</h2>
    <p>As part of your Licence Agreement with {{ $provider }}, you are required to contribute towards the weekly service charge associated with your supported exempt accommodation.</p>
    <p><span class="label">Weekly Service Charge Amount:</span> £{{ $f['service_charge_amount'] ?? '' }} per week</p>

    <h2>Payment Due Date</h2>
    <p>Your weekly service charge is due every <strong>{{ $f['payment_day'] ?? '' }}</strong> (day of the week).</p>
    <p><span class="label">First Payment Due:</span> {{ $f['first_payment_due'] ?? '' }}</p>
    <p>Thereafter, payment must be received every {{ $f['payment_day'] ?? '' }} for the duration of your licence agreement unless otherwise agreed in writing.</p>

    <h2>Payment Details</h2>
    <p class="meta-row"><span class="label">Account Name:</span> {{ $f['account_name'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Sort Code:</span> {{ $f['sort_code'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Account Number:</span> {{ $f['account_number'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Payment Reference:</span> Your Full Name</p>
    <p>Please ensure your full name is used as the payment reference so that your payment is correctly allocated to your account.</p>

    <h2>Missed Payments</h2>
    <p>If you experience any difficulty making your payment, you must contact {{ $provider }} as soon as possible. We are committed to working with residents to resolve payment issues wherever possible.</p>
    <p>Failure to pay the weekly service charge without prior agreement may result in arrears being accrued and action being taken in accordance with the terms of your Licence Agreement.</p>
    <p>If you have any questions regarding this notice or require assistance, please contact us.</p>

    <div class="signature">
        <p>Yours sincerely,</p>
        <p class="mt"><strong>{{ $provider }}</strong></p>
    </div>
@endsection
