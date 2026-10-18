@extends('backend::admin.documents.templates.layout')

@section('content')
    @php $f = $fields; @endphp

    <h1>Missed Meeting Letter (RH-16)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Postcode:</span> {{ $f['postcode'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date:</span> {{ $f['date'] ?? '' }}</p>
    </div>

    <p>Dear {{ $f['dear_name'] ?? '' }},</p>
    <p>I am writing to you as you missed your support session.</p>
    <p>We were due to meet on <strong>{{ $f['missed_meeting_on'] ?? '' }}</strong>, but unfortunately you did not attend.</p>
    <p><span class="label">I have made another appointment for you on:</span> {{ $f['new_appointment_on'] ?? '' }}</p>
    <p><span class="label">At:</span> {{ $f['meeting_location'] ?? '' }}</p>
    <p>If you are unable to make this appointment, please contact me to rearrange by calling {{ $f['contact_phone'] ?? '' }}</p>
    <p>If you fail to attend this meeting, you will be issued a written warning.</p>
    <p>You have agreed to participate in a program of support and are breaching your license agreement by not engaging and this could ultimately lead to termination of your license agreement and loss of accommodation.</p>

    <div class="signature">
        <p>Yours sincerely,</p>
        <p class="signature-line">{{ $f['support_worker_name'] ?? '' }}</p>
        <p>{{ $f['support_worker_title'] ?: 'Support Worker' }}</p>
    </div>
@endsection
