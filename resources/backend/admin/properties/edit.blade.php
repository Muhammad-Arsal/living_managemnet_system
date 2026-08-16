@extends('backend::admin.layouts.app')

@section('title', 'Edit Property')

@php
    $activeTab = request('tab', 'overview');
    if ($errors->has('tenant_id') || $errors->has('started_on') || $errors->has('ended_on')) {
        $activeTab = 'current';
    }
    $current = $property->currentTenancy;
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Portfolio</p>
                        <h5 class="lms-page-header__title">{{ $property->name }}</h5>
                        <p class="lms-page-header__subtitle">{{ $property->formattedAddress() }}</p>
                    </div>
                    @if ($property->isOccupied())
                        <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>Occupied</span>
                    @else
                        <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>Vacant</span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($current)
                        <div class="lms-occupancy-strip">
                            <div class="lms-occupancy-strip__identity">
                                <span class="lms-occupancy-strip__icon">
                                    <i class="iconify" data-icon="bx:bx-user"></i>
                                </span>
                                <div>
                                    <p class="lms-occupancy-strip__label">Current tenant</p>
                                    <p class="lms-occupancy-strip__title">{{ $current->tenant->full_name }}</p>
                                    <p class="lms-occupancy-strip__meta">{{ $current->tenant->email }} · started {{ $current->started_on->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.tenants.edit', $current->tenant) }}" class="lms-filter-btn lms-filter-btn--ghost">View tenant</a>
                                <a href="{{ route('admin.properties.edit', ['property' => $property, 'tab' => 'current']) }}" class="btn btn-primary lms-btn-add">Manage occupancy</a>
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
                                    <p class="lms-occupancy-strip__title">This property is vacant</p>
                                    <p class="lms-occupancy-strip__meta">Assign a tenant from this page. You do not need to open the tenant record first.</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.properties.edit', ['property' => $property, 'tab' => 'current']) }}" class="btn btn-primary lms-btn-add">
                                <i class="iconify" data-icon="bx:bx-user-plus"></i>
                                Assign tenant
                            </a>
                        </div>
                    @endif

                    <div class="lms-segmented" role="tablist">
                        <button class="lms-segmented__btn @if ($activeTab === 'overview') active @endif" data-bs-toggle="tab" data-bs-target="#property-overview" type="button">
                            <i class="iconify" data-icon="bx:bx-info-circle"></i>
                            Overview
                        </button>
                        <button class="lms-segmented__btn @if ($activeTab === 'current') active @endif" data-bs-toggle="tab" data-bs-target="#property-current" type="button">
                            <i class="iconify" data-icon="bx:bx-user-check"></i>
                            Current tenant
                        </button>
                        <button class="lms-segmented__btn @if ($activeTab === 'history') active @endif" data-bs-toggle="tab" data-bs-target="#property-history" type="button">
                            <i class="iconify" data-icon="bx:bx-time-five"></i>
                            Tenant history
                        </button>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade @if ($activeTab === 'overview') show active @endif" id="property-overview">
                            <form action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Details</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Property name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $property->name) }}">
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="property_type_id" class="form-label">Property type <span class="text-danger">*</span></label>
                                            <select name="property_type_id" id="property_type_id" class="form-select @error('property_type_id') is-invalid @enderror">
                                                @foreach ($propertyTypes as $type)
                                                    <option value="{{ $type->id }}" @selected((string) old('property_type_id', $property->property_type_id) === (string) $type->id)>
                                                        {{ $type->name }}{{ $type->is_active ? '' : ' (inactive)' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('property_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Property address</h6>
                                    @include('backend::admin.partials.uk-address-fields', ['record' => $property])
                                </div>
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Images</h6>
                                    @if ($property->images->count() > 0)
                                        <div class="property-image-grid mb-3">
                                            @foreach ($property->images as $image)
                                                <div class="property-image-card">
                                                    <img src="{{ $image->url }}" alt="{{ $image->original_name }}">
                                                    <form action="{{ route('admin.properties.images.destroy', [$property, $image]) }}" method="POST" onsubmit="return confirm('Remove this image?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="property-image-card__remove" title="Remove image">
                                                            <i class="iconify" data-icon="bx:bx-x"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <p class="text-muted small">Add more photos (JPG, PNG or WebP). There is no limit on how many images a property can have.</p>
                                    <input type="file" name="images[]" class="form-control mb-3 @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" multiple>
                                    @error('images')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="property-form-actions">
                                    <button type="submit" class="btn btn-primary lms-btn-add">Update Property</button>
                                    <a href="{{ route('admin.properties.index') }}" class="lms-filter-btn lms-filter-btn--ghost">Back</a>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade @if ($activeTab === 'current') show active @endif" id="property-current">
                            @if ($current)
                                <div class="lms-panel">
                                    <h6 class="lms-panel__title">Current occupant</h6>
                                    <div class="lms-meta-grid">
                                        <div>
                                            <span class="lms-meta-grid__label">Tenant</span>
                                            <p class="lms-meta-grid__value">{{ $current->tenant->full_name }}</p>
                                        </div>
                                        <div>
                                            <span class="lms-meta-grid__label">Email</span>
                                            <p class="lms-meta-grid__value">{{ $current->tenant->email }}</p>
                                        </div>
                                        <div>
                                            <span class="lms-meta-grid__label">Mobile</span>
                                            <p class="lms-meta-grid__value">{{ $current->tenant->mobile_number }}</p>
                                        </div>
                                        <div>
                                            <span class="lms-meta-grid__label">Started</span>
                                            <p class="lms-meta-grid__value">{{ $current->started_on->format('d M Y') }} · {{ $current->statusLabel() }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.tenants.edit', $current->tenant) }}" class="lms-filter-btn lms-filter-btn--ghost">Open tenant record</a>
                                </div>

                                <div class="lms-panel lms-panel--danger">
                                    <h6 class="lms-panel__title">End tenancy</h6>
                                    <p class="lms-panel__lede">Ending keeps the history on both the property and the tenant. The property becomes vacant and can be assigned again.</p>
                                    <form action="{{ route('admin.properties.tenancies.end', $property) }}" method="POST" class="row g-3 align-items-end">
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
                                <div class="lms-panel">
                                    <h6 class="lms-panel__title">Assign tenant</h6>
                                    <p class="lms-panel__lede">Choose a tenant who does not already have a current property, then set the tenancy start date.</p>
                                    @if ($assignableTenants->isEmpty())
                                        <p class="mb-0">There are no tenants available to assign. Create a tenant first, or end another current tenancy.</p>
                                    @else
                                        <form action="{{ route('admin.properties.tenancies.store', $property) }}" method="POST" class="row g-3 align-items-end">
                                            @csrf
                                            <div class="col-md-6">
                                                <label for="tenant_id" class="form-label">Tenant <span class="text-danger">*</span></label>
                                                <select name="tenant_id" id="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror">
                                                    <option value="">Select tenant</option>
                                                    @foreach ($assignableTenants as $tenant)
                                                        <option value="{{ $tenant->id }}" @selected((string) old('tenant_id') === (string) $tenant->id)>
                                                            {{ $tenant->full_name }} ({{ $tenant->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tenant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

                        <div class="tab-pane fade @if ($activeTab === 'history') show active @endif lms-tab-pane-flush" id="property-history">
                            @if ($property->tenancies->count() > 0)
                                <div class="table-responsive lms-table-shell">
                                    <table class="table table-hover lms-data-table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tenant</th>
                                                <th>Start</th>
                                                <th>End</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($property->tenancies as $tenancy)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.tenants.edit', $tenancy->tenant) }}">{{ $tenancy->tenant->full_name }}</a>
                                                        <div class="small text-muted">{{ $tenancy->tenant->email }}</div>
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
                                <p class="mb-0 text-muted">No tenant history for this property.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
