@extends('backend::admin.layouts.app')

@section('title', 'Create Property')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Portfolio</p>
                        <h5 class="lms-page-header__title">Create property</h5>
                        <p class="lms-page-header__subtitle">Add the rental address, type, and optional photos. Occupancy is assigned after create.</p>
                    </div>
                </div>
                <div class="card-body">
                    @if ($propertyTypes->isEmpty())
                        <div class="alert alert-warning">
                            Create at least one active property type in
                            <a href="{{ route('admin.settings.property-types.create') }}">Settings → Property Types</a>
                            before adding a property.
                        </div>
                    @endif
                    <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Overview</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Property name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. 12 High Street — Flat 2">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="property_type_id" class="form-label">Property type <span class="text-danger">*</span></label>
                                    <select name="property_type_id" id="property_type_id" class="form-select @error('property_type_id') is-invalid @enderror">
                                        <option value="">Select type</option>
                                        @foreach ($propertyTypes as $type)
                                            <option value="{{ $type->id }}" @selected((string) old('property_type_id') === (string) $type->id)>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('property_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Property address</h6>
                            @include('backend::admin.partials.uk-address-fields')
                        </div>
                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Images</h6>
                            <p class="text-muted small">You can upload as many photos as you need (JPG, PNG or WebP, {{ (int) config('properties.images.max_kilobytes') / 1024 }}MB each).</p>
                            <input type="file" name="images[]" id="images" class="form-control mb-3 @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" multiple>
                            @error('images')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="property-form-actions">
                            <button type="submit" class="btn btn-primary lms-btn-add" @disabled($propertyTypes->isEmpty())>Create Property</button>
                            <a href="{{ route('admin.properties.index') }}" class="lms-filter-btn lms-filter-btn--ghost">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
