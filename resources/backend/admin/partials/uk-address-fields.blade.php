@php
    $record = $record ?? null;
@endphp

<div class="row">
    <div class="col-12 mb-3">
        <label for="address_line_1" class="form-label">Address line 1 <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('address_line_1') is-invalid @enderror" id="address_line_1"
            name="address_line_1" value="{{ old('address_line_1', $record?->address_line_1) }}" autocomplete="address-line1">
        @error('address_line_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="address_line_2" class="form-label">Address line 2</label>
        <input type="text" class="form-control @error('address_line_2') is-invalid @enderror" id="address_line_2"
            name="address_line_2" value="{{ old('address_line_2', $record?->address_line_2) }}" autocomplete="address-line2">
        @error('address_line_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="address_line_3" class="form-label">Address line 3</label>
        <input type="text" class="form-control @error('address_line_3') is-invalid @enderror" id="address_line_3"
            name="address_line_3" value="{{ old('address_line_3', $record?->address_line_3) }}">
        @error('address_line_3')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="city" class="form-label">City / town <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city"
            value="{{ old('city', $record?->city) }}" autocomplete="address-level2">
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="county" class="form-label">County</label>
        <input type="text" class="form-control @error('county') is-invalid @enderror" id="county" name="county"
            value="{{ old('county', $record?->county) }}">
        @error('county')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="postcode" class="form-label">Postcode <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('postcode') is-invalid @enderror" id="postcode" name="postcode"
            value="{{ old('postcode', $record?->postcode) }}" autocomplete="postal-code">
        @error('postcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country"
            value="{{ old('country', $record?->country ?? 'United Kingdom') }}" autocomplete="country-name">
        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
