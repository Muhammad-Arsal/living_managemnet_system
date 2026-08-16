@extends('backend::admin.layouts.app')

@section('title', 'Create Tenant')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Occupancy</p>
                        <h5 class="lms-page-header__title">Create tenant</h5>
                        <p class="lms-page-header__subtitle">Add contact details and correspondence address. Occupancy is assigned from a property.</p>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tenants.store') }}" method="POST">
                        @csrf
                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Basic information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}">
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}">
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mobile_number" class="form-label">Mobile number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}">
                                    @error('mobile_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Correspondence address</h6>
                            @include('backend::admin.partials.uk-address-fields')
                        </div>
                        <div class="property-form-actions">
                            <button type="submit" class="btn btn-primary lms-btn-add">Create Tenant</button>
                            <a href="{{ route('admin.tenants.index') }}" class="lms-filter-btn lms-filter-btn--ghost">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
