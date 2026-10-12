@extends('backend::admin.documents.templates.layout')

@section('content')
    @php $f = $fields; @endphp

    <h1>Final Warning for Non-Engagement of Support Sessions (RH-18)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Postcode:</span> {{ $f['postcode'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date:</span> {{ $f['date'] ?? '' }}</p>
    </div>

    <p>Dear {{ $f['dear_name'] ?? '' }},</p>
    <p>I am writing to you as you have now missed several support sessions.</p>
    <p>We were due to meet on <strong>{{ $f['missed_meeting_on'] ?? '' }}</strong>, but unfortunately you did not attend.</p>
    <p>As this is your final warning for non-engagement a meeting has been arranged to discuss if you still require to reside in supported accommodation.</p>
    <p><span class="label">The meeting has been arranged for</span> {{ $f['meeting_datetime'] ?? '' }}</p>
    <p><span class="label">At:</span> {{ $f['meeting_location'] ?? '' }}</p>
    <p>If you are unable to make this appointment, please contact me to rearrange by calling {{ $f['contact_phone'] ?? '' }}.</p>
    <p>Failure to attend the meeting will result in your license being terminated with 7 days notice being issued from the date of the meeting.</p>
    <p>This is your second and final warning for not engaging with the support process.</p>

    <div class="signature">
        <p>Yours sincerely,</p>
        <p class="signature-line">{{ $f['support_worker_name'] ?? '' }}</p>
        <p>{{ $f['support_worker_title'] ?: 'Support Worker' }}</p>
    </div>
@endsection
