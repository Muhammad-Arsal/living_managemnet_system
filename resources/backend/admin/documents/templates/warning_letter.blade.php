@extends('backend::admin.documents.templates.layout')

@section('content')
    @php $f = $fields; @endphp

    <h1>Warning Letter (RH-17)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Postcode:</span> {{ $f['postcode'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date:</span> {{ $f['date'] ?? '' }}</p>
    </div>

    <p>Dear {{ $f['dear_name'] ?? '' }},</p>
    <p>I am writing to you to inform you of your written warning due to the following reason(s).</p>
    <div class="box">{{ $f['warning_reasons'] ?? '' }}</div>

    <p>As this is a warning related to the breach of the license agreement, a meeting has been arranged to discuss if you still require to reside in supported accommodation and/or an action plan related to the reasons above.</p>
    <p><span class="label">The meeting has been arranged for:</span> {{ $f['meeting_datetime'] ?? '' }}</p>
    <p><span class="label">at:</span> {{ $f['meeting_location'] ?? '' }}</p>
    <p>If you are unable to make this appointment, please contact me to rearrange by calling {{ $f['contact_phone'] ?? '' }}</p>
    <p>If you fail to attend the next support session you will be issued with your final warning.</p>
    <p>You have agreed to participate in a program of support and are breaching your license agreement for the reasons mentioned above. This could ultimately lead to termination of your license agreement and loss of your accommodation.</p>

    <div class="signature">
        <p>Yours sincerely,</p>
        <p class="signature-line">{{ $f['support_worker_name'] ?? '' }}</p>
        <p>{{ $f['support_worker_title'] ?: 'Support Worker' }}</p>
    </div>
@endsection
