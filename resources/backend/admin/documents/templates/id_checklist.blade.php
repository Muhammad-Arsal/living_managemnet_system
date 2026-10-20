@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
        $items = [
            'ID-1' => 'Proof of ID – Passport',
            'ID-2' => 'Proof of ID – Driving Licence',
            'ID-3' => 'Proof of Income',
            'ID-4' => 'UC Account Statement',
            'ID-5' => 'Proof of NI Number',
            'ID-6' => 'Photo of Tenant',
            'ID-7' => 'Utility Bill',
            'ID-8' => 'Birth Certificate',
            'ID-9' => 'Bank Card',
        ];
        $completed = $f['documents_completed'] ?? [];
        if (! is_array($completed)) {
            $completed = [];
        }
        $completed = array_map('strval', $completed);
    @endphp

    <h1>ID Check List (RH-22)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date Moved In:</span> {{ $f['date_moved_in'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Name of Support Worker:</span> {{ $f['support_worker_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Previous RP/Landlord:</span> {{ $f['previous_rp_landlord'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Previous Housing Provider:</span> {{ $f['previous_housing_provider'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Previous Address:</span> {{ $f['previous_address'] ?? '' }}</p>
    </div>

    <table class="risk">
        <thead>
            <tr>
                <th style="width:18%">Reference</th>
                <th style="width:62%">Document</th>
                <th style="width:20%">Completed</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $ref => $label)
                <tr>
                    <td>{{ $ref }}</td>
                    <td>{{ $label }}</td>
                    <td>{{ in_array($ref, $completed, true) ? '☑' : '☐' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
