@extends('backend::admin.layouts.app')

@section('title', 'Edit Property')

@php
    use App\Enums\RentalMode;
    $activeTab = request('tab', 'overview');
    if ($errors->has('tenant_id') || $errors->has('started_on') || $errors->has('ended_on')) {
        $activeTab = $property->isPerRoom() ? 'rooms' : 'current';
    }
    if ($errors->has('rooms') || $errors->has('rooms.*') || $errors->has('name') && request('tab') === 'rooms') {
        // keep room name errors on rooms if that tab was intended
    }
    if ($errors->has('documents') || $errors->has('documents.*')) {
        $activeTab = 'documents';
    }
    if ($errors->has('images') || $errors->has('images.*')) {
        $activeTab = 'images';
    }
    if ($errors->has('rental_mode') || $errors->has('rooms')) {
        $activeTab = 'overview';
    }
    $current = $property->currentTenancy;
    $selectedMode = old('rental_mode', $property->rental_mode->value);
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Portfolio · {{ $property->rental_mode->label() }}</p>
                        <h5 class="lms-page-header__title">{{ $property->name }}</h5>
                        <p class="lms-page-header__subtitle">{{ $property->formattedAddress() }}</p>
                    </div>
                    @if ($property->isPartiallyOccupied())
                        <span class="lms-badge lms-badge--warning"><span class="lms-badge__dot"></span>Partially occupied</span>
                    @elseif ($property->isOccupied())
                        <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>{{ $property->isPerRoom() ? 'Fully occupied' : 'Occupied' }}</span>
                    @else
                        <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>Vacant</span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($property->isPerRoom())
                        <div class="lms-occupancy-strip {{ $property->isVacant() ? 'lms-occupancy-strip--vacant' : '' }}">
                            <div class="lms-occupancy-strip__identity">
                                <span class="lms-occupancy-strip__icon">
                                    <i class="iconify" data-icon="bx:bx-door-open"></i>
                                </span>
                                <div>
                                    <p class="lms-occupancy-strip__label">Room occupancy</p>
                                    <p class="lms-occupancy-strip__title">{{ $property->occupancySummary() }}</p>
                                    <p class="lms-occupancy-strip__meta">Assign or end tenants from the Rooms tab.</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.properties.edit', ['property' => $property, 'tab' => 'rooms']) }}" class="btn btn-primary lms-btn-add">
                                <i class="iconify" data-icon="bx:bx-door-open"></i>
                                Manage rooms
                            </a>
                        </div>
                    @elseif ($current)
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
                        @if ($property->isWhole())
                            <button class="lms-segmented__btn @if ($activeTab === 'current') active @endif" data-bs-toggle="tab" data-bs-target="#property-current" type="button">
                                <i class="iconify" data-icon="bx:bx-user-check"></i>
                                Current tenant
                            </button>
                        @else
                            <button class="lms-segmented__btn @if ($activeTab === 'rooms') active @endif" data-bs-toggle="tab" data-bs-target="#property-rooms" type="button">
                                <i class="iconify" data-icon="bx:bx-door-open"></i>
                                Rooms
                            </button>
                        @endif
                        <button class="lms-segmented__btn @if ($activeTab === 'history') active @endif" data-bs-toggle="tab" data-bs-target="#property-history" type="button">
                            <i class="iconify" data-icon="bx:bx-time-five"></i>
                            Tenant history
                        </button>
                        <button class="lms-segmented__btn @if ($activeTab === 'images') active @endif" data-bs-toggle="tab" data-bs-target="#property-images" type="button">
                            <i class="iconify" data-icon="bx:bx-image"></i>
                            Images
                        </button>
                        <button class="lms-segmented__btn @if ($activeTab === 'documents') active @endif" data-bs-toggle="tab" data-bs-target="#property-documents" type="button">
                            <i class="iconify" data-icon="bx:bx-file"></i>
                            Documents
                        </button>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade @if ($activeTab === 'overview') show active @endif" id="property-overview">
                            <form action="{{ route('admin.properties.update', $property) }}" method="POST" id="property-update-form">
                                @csrf
                                @method('PUT')
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Details</h6>
                                    <div class="row">
                                        <div class="col-lg-5 mb-3">
                                            <label for="name" class="form-label">Property name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $property->name) }}">
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-3">
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
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <label for="rental_mode" class="form-label">Rental mode <span class="text-danger">*</span></label>
                                            <select name="rental_mode" id="rental_mode" class="form-select @error('rental_mode') is-invalid @enderror">
                                                @foreach (RentalMode::cases() as $mode)
                                                    <option value="{{ $mode->value }}" @selected($selectedMode === $mode->value)>{{ $mode->label() }}</option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Only when no open tenancies; Whole Property needs rooms with no history.</div>
                                            @error('rental_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="property-form-section" id="switch-rooms-section" @if ($selectedMode !== RentalMode::PerRoom->value || $property->isPerRoom()) hidden @endif>
                                    <div class="lms-rooms-panel">
                                        <div class="lms-rooms-panel__header">
                                            <div>
                                                <h6 class="property-form-section__title mb-1">Rooms</h6>
                                                <p class="lms-rooms-panel__hint mb-0">Required when switching to Per room.</p>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-switch-room-row">
                                                <i class="iconify" data-icon="bx:bx-plus"></i>
                                                Add room
                                            </button>
                                        </div>
                                        @error('rooms')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                                        <div class="lms-rooms-list" id="switch-rooms-list">
                                            @php $switchRooms = old('rooms', [['name' => 'Room 1'], ['name' => 'Room 2']]); @endphp
                                            @foreach ($switchRooms as $index => $room)
                                                <div class="lms-rooms-row switch-room-row">
                                                    <span class="lms-rooms-row__index">{{ $index + 1 }}</span>
                                                    <input type="text" name="rooms[{{ $index }}][name]" class="form-control" value="{{ $room['name'] ?? '' }}" placeholder="Room {{ $index + 1 }}" aria-label="Room {{ $index + 1 }} name">
                                                    <button type="button" class="lms-rooms-row__remove remove-switch-room-row" title="Remove room" aria-label="Remove room">
                                                        <i class="iconify" data-icon="bx:bx-x"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Property address</h6>
                                    @include('backend::admin.partials.uk-address-fields', ['record' => $property])
                                </div>
                                <div class="property-form-actions">
                                    <button type="submit" class="btn btn-primary lms-btn-add">Update Property</button>
                                    <a href="{{ route('admin.properties.index') }}" class="lms-filter-btn lms-filter-btn--ghost">Back</a>
                                </div>
                            </form>
                        </div>

                        @if ($property->isWhole())
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
                                        <form action="{{ route('admin.properties.tenancies.end', $property) }}"
                                            method="POST"
                                            class="row g-3 align-items-end"
                                            data-confirm-title="End tenancy"
                                            data-confirm-body="End this tenancy? History will be kept on both the property and the tenant."
                                            data-confirm-submit="End tenancy">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-4">
                                                <label for="ended_on" class="form-label">End date <span class="text-danger">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('ended_on') is-invalid @enderror"
                                                    id="ended_on"
                                                    name="ended_on"
                                                    value="{{ old('ended_on', $current->started_on->isFuture() ? $current->started_on->toDateString() : now()->toDateString()) }}"
                                                    min="{{ $current->started_on->toDateString() }}"
                                                    @unless ($current->started_on->isFuture()) max="{{ now()->toDateString() }}" @endunless>
                                                <div class="form-text">Must be on or after {{ $current->started_on->format('d M Y') }}.</div>
                                                @error('ended_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn lms-btn-danger">End tenancy</button>
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
                        @else
                            <div class="tab-pane fade @if ($activeTab === 'rooms') show active @endif" id="property-rooms">
                                <div class="lms-panel mb-3">
                                    <h6 class="lms-panel__title">Add room</h6>
                                    <form action="{{ route('admin.properties.rooms.store', $property) }}" method="POST" class="row g-3 align-items-end">
                                        @csrf
                                        <div class="col-md-6">
                                            <label for="room_name" class="form-label">Room name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="room_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Room 3">
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary lms-btn-add">Add room</button>
                                        </div>
                                    </form>
                                </div>

                                @forelse ($property->rooms as $room)
                                    @php $roomTenancy = $room->currentTenancy; @endphp
                                    <div class="lms-panel">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                                            <div>
                                                <h6 class="lms-panel__title mb-1">{{ $room->name }}</h6>
                                                @if ($room->isOccupied())
                                                    <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>Occupied</span>
                                                @else
                                                    <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>Vacant</span>
                                                @endif
                                            </div>
                                            <form action="{{ route('admin.properties.rooms.destroy', [$property, $room]) }}"
                                                method="POST"
                                                data-confirm-title="Delete room"
                                                data-confirm-body="Delete {{ $room->name }}? Rooms with tenancy history cannot be deleted."
                                                data-confirm-submit="Delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="lms-action-btn lms-action-btn--delete">
                                                    <i class="iconify" data-icon="bx:bx-trash"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>

                                        <form action="{{ route('admin.properties.rooms.update', [$property, $room]) }}" method="POST" class="row g-2 align-items-end mb-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-6">
                                                <label class="form-label">Rename</label>
                                                <input type="text" name="name" class="form-control" value="{{ $room->name }}">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="lms-filter-btn lms-filter-btn--ghost">Save name</button>
                                            </div>
                                        </form>

                                        @if ($roomTenancy)
                                            <div class="lms-meta-grid mb-3">
                                                <div>
                                                    <span class="lms-meta-grid__label">Tenant</span>
                                                    <p class="lms-meta-grid__value">
                                                        <a href="{{ route('admin.tenants.edit', $roomTenancy->tenant) }}">{{ $roomTenancy->tenant->full_name }}</a>
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="lms-meta-grid__label">Started</span>
                                                    <p class="lms-meta-grid__value">{{ $roomTenancy->started_on->format('d M Y') }} · {{ $roomTenancy->statusLabel() }}</p>
                                                </div>
                                            </div>
                                            <form action="{{ route('admin.properties.rooms.tenancies.end', [$property, $room]) }}"
                                                method="POST"
                                                class="row g-3 align-items-end"
                                                data-confirm-title="End room tenancy"
                                                data-confirm-body="End the tenancy for {{ $room->name }}? History will be kept."
                                                data-confirm-submit="End tenancy">
                                                @csrf
                                                @method('PUT')
                                                <div class="col-md-4">
                                                    <label class="form-label">End date <span class="text-danger">*</span></label>
                                                    <input type="date"
                                                        class="form-control"
                                                        name="ended_on"
                                                        value="{{ $roomTenancy->started_on->isFuture() ? $roomTenancy->started_on->toDateString() : now()->toDateString() }}"
                                                        min="{{ $roomTenancy->started_on->toDateString() }}"
                                                        @unless ($roomTenancy->started_on->isFuture()) max="{{ now()->toDateString() }}" @endunless>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn lms-btn-danger">End tenancy</button>
                                                </div>
                                            </form>
                                        @else
                                            @if ($assignableTenants->isEmpty())
                                                <p class="mb-0 text-muted">No tenants available to assign.</p>
                                            @else
                                                <form action="{{ route('admin.properties.rooms.tenancies.store', [$property, $room]) }}" method="POST" class="row g-3 align-items-end">
                                                    @csrf
                                                    <div class="col-md-5">
                                                        <label class="form-label">Tenant <span class="text-danger">*</span></label>
                                                        <select name="tenant_id" class="form-select">
                                                            <option value="">Select tenant</option>
                                                            @foreach ($assignableTenants as $tenant)
                                                                <option value="{{ $tenant->id }}">{{ $tenant->full_name }} ({{ $tenant->email }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Start date <span class="text-danger">*</span></label>
                                                        <input type="date" name="started_on" class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="submit" class="btn btn-primary lms-btn-add w-100">Assign</button>
                                                    </div>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No rooms yet. Add at least one room before assigning tenants.</p>
                                @endforelse
                            </div>
                        @endif

                        <div class="tab-pane fade @if ($activeTab === 'history') show active @endif lms-tab-pane-flush" id="property-history">
                            @if ($property->tenancies->count() > 0)
                                <div class="table-responsive lms-table-shell">
                                    <table class="table table-hover lms-data-table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tenant</th>
                                                <th>Location</th>
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
                                                    <td>{{ $tenancy->locationLabel() }}</td>
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

                        <div class="tab-pane fade @if ($activeTab === 'images') show active @endif" id="property-images">
                            <div class="property-form-section">
                                <h6 class="property-form-section__title">Uploaded images</h6>
                                @if ($property->images->count() > 0)
                                    <div class="property-image-grid">
                                        @foreach ($property->images as $image)
                                            <div class="property-image-card">
                                                <img src="{{ $image->url }}" alt="{{ $image->original_name }}">
                                                <form action="{{ route('admin.properties.images.destroy', [$property, $image]) }}"
                                                    method="POST"
                                                    data-confirm-title="Remove image"
                                                    data-confirm-body="Remove {{ $image->original_name }}? This cannot be undone."
                                                    data-confirm-submit="Remove">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="property-image-card__remove" title="Remove image">
                                                        <i class="iconify" data-icon="bx:bx-x"></i>
                                                    </button>
                                                </form>
                                                <p class="property-image-card__caption" title="{{ $image->original_name }}">{{ $image->original_name }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="lms-empty-state py-4">
                                        <div class="lms-empty-state__icon">
                                            <i class="iconify" data-icon="bx:bx-image"></i>
                                        </div>
                                        <p class="mb-0">No images uploaded yet.</p>
                                    </div>
                                @endif
                            </div>
                            <form action="{{ route('admin.properties.images.store', $property) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Add images</h6>
                                    <p class="text-muted small">Existing photos are kept. Upload additional photos (JPG, PNG or WebP, {{ (int) config('properties.images.max_kilobytes') / 1024 }}MB each).</p>
                                    <input type="file" name="images[]" class="form-control mb-3 @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" multiple>
                                    @error('images')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="property-form-actions">
                                    <button type="submit" class="btn btn-primary lms-btn-add">Upload images</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade @if ($activeTab === 'documents') show active @endif" id="property-documents">
                            <div class="property-form-section">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                                    <div>
                                        <h6 class="property-form-section__title mb-1">Generate document</h6>
                                        <p class="text-muted small mb-0">Create a supported-housing document from a template. Property and tenant details are filled in automatically where available.</p>
                                    </div>
                                    <a href="{{ route('admin.properties.documents.generate', $property) }}" class="btn btn-primary lms-btn-add">
                                        <i class="iconify" data-icon="bx:bx-file"></i>
                                        Generate from template
                                    </a>
                                </div>
                            </div>
                            <div class="property-form-section">
                                <h6 class="property-form-section__title">Saved documents</h6>
                                @include('backend::admin.partials.documents-list', [
                                    'documents' => $property->documents,
                                    'downloadRouteName' => 'admin.properties.documents.download',
                                    'destroyRouteName' => 'admin.properties.documents.destroy',
                                    'parent' => $property,
                                ])
                            </div>
                            <form action="{{ route('admin.properties.documents.store', $property) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="property-form-section">
                                    <h6 class="property-form-section__title">Upload documents</h6>
                                    <p class="text-muted small">Existing files are kept. Upload additional documents below.</p>
                                    @include('backend::admin.partials.documents-field', ['required' => true])
                                </div>
                                <div class="property-form-actions">
                                    <button type="submit" class="btn btn-primary lms-btn-add">Upload documents</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            var isCurrentlyPerRoom = @json($property->isPerRoom());
            var perRoomValue = @json(RentalMode::PerRoom->value);
            var modeSelect = document.getElementById('rental_mode');
            var section = document.getElementById('switch-rooms-section');
            var list = document.getElementById('switch-rooms-list');
            var addBtn = document.getElementById('add-switch-room-row');
            if (!modeSelect || !section || !list) return;

            function reindex() {
                list.querySelectorAll('.switch-room-row').forEach(function (row, index) {
                    var badge = row.querySelector('.lms-rooms-row__index');
                    var input = row.querySelector('input[type="text"]');
                    if (badge) badge.textContent = String(index + 1);
                    if (input) {
                        input.name = 'rooms[' + index + '][name]';
                        input.placeholder = 'Room ' + (index + 1);
                        input.setAttribute('aria-label', 'Room ' + (index + 1) + ' name');
                    }
                });
            }

            function sync() {
                var show = modeSelect.value === perRoomValue && !isCurrentlyPerRoom;
                section.hidden = !show;
                list.querySelectorAll('input').forEach(function (input) {
                    input.disabled = !show;
                });
            }

            modeSelect.addEventListener('change', sync);

            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    var index = list.querySelectorAll('.switch-room-row').length;
                    var row = document.createElement('div');
                    row.className = 'lms-rooms-row switch-room-row';
                    row.innerHTML =
                        '<span class="lms-rooms-row__index">' + (index + 1) + '</span>' +
                        '<input type="text" name="rooms[' + index + '][name]" class="form-control" placeholder="Room ' + (index + 1) + '" value="Room ' + (index + 1) + '" aria-label="Room ' + (index + 1) + ' name">' +
                        '<button type="button" class="lms-rooms-row__remove remove-switch-room-row" title="Remove room" aria-label="Remove room">' +
                        '<i class="iconify" data-icon="bx:bx-x"></i></button>';
                    list.appendChild(row);
                    if (window.Iconify) window.Iconify.scan(row);
                    sync();
                });
            }

            list.addEventListener('click', function (event) {
                var btn = event.target.closest('.remove-switch-room-row');
                if (!btn) return;
                if (list.querySelectorAll('.switch-room-row').length <= 1) return;
                btn.closest('.switch-room-row').remove();
                reindex();
            });

            sync();
        })();
    </script>
@endsection
