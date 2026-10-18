@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
        $provider = $f['provider_name'] ?? '';
        $selected = $f['statements'] ?? [];
        if (! is_array($selected)) {
            $selected = [];
        }
        $selected = array_map('strval', $selected);
        $options = $defs['statements']['options'] ?? [];

        $renderStatement = function (string $key, string $label) use ($selected, $provider): array {
            $text = $label;
            if ($key === 'landlord') {
                $text = 'I understand that '.$provider.' is my landlord';
            }

            return [$text, in_array($key, $selected, true)];
        };
    @endphp

    <h1>Knowing Your Rights (RH-19)</h1>

    <p>It is important to {{ $provider }} that you fully understand your rights and commitments.</p>
    <p>Please sign each statement below to confirm that you agree and understand.</p>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
    </div>

    <table class="risk">
        <thead>
            <tr>
                <th style="width:70%">Statement of Understanding</th>
                <th style="width:30%">Resident Signature</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($options as $key => $label)
                @php [$text, $checked] = $renderStatement((string) $key, (string) $label); @endphp
                <tr>
                    <td>{{ $checked ? '☑' : '☐' }} {{ $text }}</td>
                    <td>____________________</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="meta mt">
        <p class="meta-row"><span class="label">Name of support worker completing the form:</span> {{ $f['support_worker'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date of Completion:</span> {{ $f['date_of_completion'] ?? '' }}</p>
    </div>

    <div class="signature">
        <p class="signature-line"><span class="label">Resident Signature:</span> ______________________________</p>
        <p class="signature-line"><span class="label">Support Worker Signature:</span> ______________________________</p>
    </div>
@endsection
