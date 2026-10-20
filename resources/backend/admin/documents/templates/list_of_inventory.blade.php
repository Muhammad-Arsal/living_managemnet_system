@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $rows = [];
        for ($i = 1; $i <= 10; $i++) {
            $row = $f['inv_'.$i] ?? [];
            if (! is_array($row)) {
                $row = [];
            }
            $rows[] = [
                'item' => (string) ($row['item'] ?? ''),
                'description' => (string) ($row['description'] ?? ''),
                'quantity' => (string) ($row['quantity'] ?? ''),
            ];
        }
    @endphp

    <h1>List of Inventory (RH-21)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Resident Name:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Property Address:</span> {{ $f['property_address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date Moved In:</span> {{ $f['date_moved_in'] ?? '' }}</p>
    </div>

    <table class="risk">
        <thead>
            <tr>
                <th style="width:28%">Item</th>
                <th style="width:52%">Item Description</th>
                <th style="width:20%">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['item'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td>{{ $row['quantity'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
