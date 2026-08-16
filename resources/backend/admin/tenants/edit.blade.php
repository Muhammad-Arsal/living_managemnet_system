@extends('backend::admin.layouts.app')

@section('title', 'Edit Tenant')

@php
    $activeTab = request('tab', 'overview');
    if ($errors->has('property_id') || $errors->has('tenant_id') || $errors->has('started_on') || $errors->has('ended_on')) {
        $activeTab = 'current';
    }
    $current = $tenant->currentTenancy;
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Occupancy</p>
                        <h5 class="lms-page-header__title">{{ $tenant->full_name }}</h5>
                        <p class="lms-page-header__subtitle">{{ $tenant->email }}</p>
                    </div>
                    @if ($tenant->isCurrent())
                        <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>Current</span>
                    @else
                        <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>Past</span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($current)
                        <div class="lms-occupancy-strip">
                            <div class="lms-occupancy-strip__identity">
                                <span class="lms-occupancy-strip__icon">
                                    <i class="iconify" data-icon="bx:bx-building-house"></i>
                                </span>
                                <div>
                                    <p class="lms-occupancy-strip__label">Current property</p>
                                    <p class="lms-occupancy-strip__title">{{ $current->property->name }}</p>
                                    <p class="lms-occupancy-strip__meta">{{ $current->property->postcode }} · started {{ $current->started_on->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.properties.edit', $current->property) }}" class="lms-filter-btn lms-filter-btn--ghost">View property</a>
                                <a href="{{ route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'current']) }}" class="btn btn-primary lms-btn-add">Manage occupancy</a>
                            </div>
                        </div>
                    @else
                        <div class="lms-occupancy-strip lms-occupancy-strip--vacant">
                            <div class="lms-occupancy-strip__identity">
                                <span class="lms-occupancy-strip__icon">
                                    <i class="iconify" data-icon="bx:bx-home-alt"></i>
                                </span>
                                <div>
                                    <p class="lms-occupancy-strip__label">Occupancy</p>
                                    <p class="lms-occupancy-strip__title">No current property</p>
                                    <p class="lms-occupancy-strip__meta">Assign a vacant property here, or assign this tenant from a property record.</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'current']) }}" class="btn btn-primary lms-btn-add">
                                <i class="iconify" data-icon="bx:bx-home-smile"></i>
                                Assign property
                            </a>
                        </div>
                    @endif

                    <div class="lms-segmented" role="tablist">
                        <button class="lms-segmented__btn @if ($activeTab === 'overview') active @endif" data-bs-toggle="tab" data-bs-target="#tenant-overview" type="button">
                            <i class="iconify" data-icon="bx:bx-user"></i>
                            Overview
                        </button>
                        <button class="lms-segmented__btn @if ($activeTab === 'current') active @endif" data-bs-toggle="tab" data-bs-target="#tenant-current" type="button">
                            <i class="iconify" data-icon="bx:bx-building-house"></i>
                            Current property
                        </button>
                        <button class="lms-segmented__btn @if ($activeTab === 'history') active @endif" data-bs-toggle="tab" data-bs-target="#tenant-history" type="button">
                            <i class="iconify" data-icon="bx:bx-time-five"></i>
                            Tenancy history
                        </button>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade @if ($activeTab === 'overview') show active @endif" id="tenant-overview">
                            <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Basic information</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">First name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $tenant->first_name) }}">
                                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">Last name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $tenant->last_name) }}">
                                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="mobile_number" class="form-label">Mobile number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $tenant->mobile_number) }}">
                                            @error('mobile_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $tenant->email) }}">
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Correspondence address</h6>
                                    @include('backend::admin.partials.uk-address-fields', ['record' => $tenant])
                                </div>
                                <div class="property-form-actions">
                                    <button type="submit" class="btn btn-primary lms-btn-add">Update Tenant</button>
                                    <a href="{{ route('admin.tenants.index') }}" class="lms-filter-btn lms-filter-btn--ghost">Back</a>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade @if ($activeTab === 'current') show active @endif" id="tenant-current">
                            @if ($current)
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Current property</h6>
                                    <div class="lms-meta-grid">
                                        <div>
                                            <span class="lms-meta-grid__label">Property</span>
                                            <p class="lms-meta-grid__value">{{ $current->property->name }}</p>
                                        </div>
                                        <div>
                                            <span class="lms-meta-grid__label">Address</span>
                                            <p class="lms-meta-grid__value">{{ $current->property->formattedAddress() }}</p>
                                        </div>
                                        <div>
                                            <span class="lms-meta-grid__label">Started</span>
                                            <p class="lms-meta-grid__value">{{ $current->started_on->format('d M Y') }} · {{ $current->statusLabel() }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.properties.edit', $current->property) }}" class="lms-filter-btn lms-filter-btn--ghost">Open property record</a>
                                </div>

                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">End tenancy</h6>
                                    <p class="text-muted mb-3">Ending keeps history on both records. The tenant becomes past and the property becomes vacant.</p>
                                    <form action="{{ route('admin.tenants.tenancies.end', $tenant) }}" method="POST" class="row g-3 align-items-end">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-md-4">
                                            <label for="ended_on" class="form-label">End date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('ended_on') is-invalid @enderror" id="ended_on" name="ended_on" value="{{ old('ended_on', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
                                            @error('ended_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn lms-btn-danger" onclick="return confirm('End this tenancy? History will be kept.');">End tenancy</button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Assign property</h6>
                                    <p class="text-muted mb-3">Choose a vacant property and a start date. You can also do this from the property record.</p>
                                    @if ($vacantProperties->isEmpty())
                                        <p class="mb-0">There are no vacant properties to assign. Create a property first, or end another current tenancy.</p>
                                    @else
                                        <form action="{{ route('admin.tenants.tenancies.store', $tenant) }}" method="POST" class="row g-3 align-items-end">
                                            @csrf
                                            <div class="col-md-6">
                                                <label for="property_id" class="form-label">Property <span class="text-danger">*</span></label>
                                                <select name="property_id" id="property_id" class="form-select @error('property_id') is-invalid @enderror">
                                                    <option value="">Select property</option>
                                                    @foreach ($vacantProperties as $property)
                                                        <option value="{{ $property->id }}" @selected((string) old('property_id') === (string) $property->id)>
                                                            {{ $property->name }} ({{ $property->postcode }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('property_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                @error('tenant_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="started_on" class="form-label">Start date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control @error('started_on') is-invalid @enderror" id="started_on" name="started_on" value="{{ old('started_on') }}">
                                                @error('started_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary lms-btn-add w-100">Assign</button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane fade @if ($activeTab === 'history') show active @endif lms-tab-pane-flush" id="tenant-history">
                            @if ($tenant->tenancies->count() > 0)
                                <div class="table-responsive lms-table-shell">
                                    <table class="table table-hover lms-data-table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Property</th>
                                                <th>Start</th>
                                                <th>End</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tenant->tenancies as $tenancy)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.properties.edit', $tenancy->property) }}">{{ $tenancy->property->name }}</a>
                                                        <div class="small text-muted">{{ $tenancy->property->postcode }}</div>
                                                    </td>
                                                    <td>{{ $tenancy->started_on->format('d M Y') }}</td>
                                                    <td>{{ $tenancy->ended_on?->format('d M Y') ?? '—' }}</td>
                                                    <td>
                                                        @if ($tenancy->isCurrent())
                                                            <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>{{ $tenancy->statusLabel() }}</span>
                                                        @else
                                                            <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>{{ $tenancy->statusLabel() }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="mb-0 text-muted">No tenancy records.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
