@extends('backend::admin.layouts.app')

@section('title', 'Create Property')

@php
    use App\Enums\RentalMode;
    $rentalMode = old('rental_mode', RentalMode::Whole->value);
    $oldRooms = old('rooms', [['name' => 'Room 1'], ['name' => 'Room 2']]);
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Portfolio</p>
                        <h5 class="lms-page-header__title">Create property</h5>
                        <p class="lms-page-header__subtitle">Add the property details, choose rental mode, then set address and optional files.</p>
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
                    <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data" id="property-create-form">
                        @csrf
                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Overview</h6>
                            <div class="row">
                                <div class="col-lg-5 mb-3">
                                    <label for="name" class="form-label">Property name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. 12 High Street — Flat 2">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label for="property_type_id" class="form-label">Property type <span class="text-danger">*</span></label>
                                    <select name="property_type_id" id="property_type_id" class="form-select @error('property_type_id') is-invalid @enderror">
                                        <option value="">Select type</option>
                                        @foreach ($propertyTypes as $type)
                                            <option value="{{ $type->id }}" @selected((string) old('property_type_id') === (string) $type->id)>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('property_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <label for="rental_mode" class="form-label">Rental mode <span class="text-danger">*</span></label>
                                    <select name="rental_mode" id="rental_mode" class="form-select @error('rental_mode') is-invalid @enderror">
                                        @foreach (RentalMode::cases() as $mode)
                                            <option value="{{ $mode->value }}" @selected($rentalMode === $mode->value)>{{ $mode->label() }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Whole Property = one unit. Per room = separate tenants.</div>
                                    @error('rental_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="property-form-section" id="rooms-section" @if ($rentalMode !== RentalMode::PerRoom->value) hidden @endif>
                            <div class="lms-rooms-panel">
                                <div class="lms-rooms-panel__header">
                                    <div>
                                        <h6 class="property-form-section__title mb-1">Rooms</h6>
                                        <p class="lms-rooms-panel__hint mb-0">Add the rooms that can be rented separately.</p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-room-row">
                                        <i class="iconify" data-icon="bx:bx-plus"></i>
                                        Add room
                                    </button>
                                </div>
                                @error('rooms')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                                <div class="lms-rooms-list" id="rooms-list">
                                    @foreach ($oldRooms as $index => $room)
                                        <div class="lms-rooms-row room-row">
                                            <span class="lms-rooms-row__index">{{ $index + 1 }}</span>
                                            <input type="text"
                                                name="rooms[{{ $index }}][name]"
                                                class="form-control @error('rooms.'.$index.'.name') is-invalid @enderror"
                                                value="{{ $room['name'] ?? '' }}"
                                                placeholder="Room name"
                                                aria-label="Room {{ $index + 1 }} name"
                                                @disabled($rentalMode !== RentalMode::PerRoom->value)>
                                            <button type="button" class="lms-rooms-row__remove remove-room-row" title="Remove room" aria-label="Remove room">
                                                <i class="iconify" data-icon="bx:bx-x"></i>
                                            </button>
                                            @error('rooms.'.$index.'.name')<div class="invalid-feedback d-block w-100">{{ $message }}</div>@enderror
                                        </div>
                                    @endforeach
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
                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Documents</h6>
                            @include('backend::admin.partials.documents-field')
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

@section('scripts')
    <script>
        (function () {
            var modeSelect = document.getElementById('rental_mode');
            var section = document.getElementById('rooms-section');
            var list = document.getElementById('rooms-list');
            var addBtn = document.getElementById('add-room-row');
            var perRoom = @json(RentalMode::PerRoom->value);
            if (!modeSelect || !section || !list || !addBtn) return;

            function reindex() {
                list.querySelectorAll('.room-row').forEach(function (row, index) {
                    var badge = row.querySelector('.lms-rooms-row__index');
                    var input = row.querySelector('input[type="text"]');
                    if (badge) badge.textContent = String(index + 1);
                    if (input) {
                        input.name = 'rooms[' + index + '][name]';
                        input.setAttribute('aria-label', 'Room ' + (index + 1) + ' name');
                        input.placeholder = 'Room ' + (index + 1);
                    }
                });
            }

            function syncMode() {
                var isPerRoom = modeSelect.value === perRoom;
                section.hidden = !isPerRoom;
                list.querySelectorAll('input').forEach(function (input) {
                    input.disabled = !isPerRoom;
                });
            }

            modeSelect.addEventListener('change', syncMode);

            addBtn.addEventListener('click', function () {
                var index = list.querySelectorAll('.room-row').length;
                var row = document.createElement('div');
                row.className = 'lms-rooms-row room-row';
                row.innerHTML =
                    '<span class="lms-rooms-row__index">' + (index + 1) + '</span>' +
                    '<input type="text" name="rooms[' + index + '][name]" class="form-control" placeholder="Room ' + (index + 1) + '" value="Room ' + (index + 1) + '" aria-label="Room ' + (index + 1) + ' name">' +
                    '<button type="button" class="lms-rooms-row__remove remove-room-row" title="Remove room" aria-label="Remove room">' +
                    '<i class="iconify" data-icon="bx:bx-x"></i></button>';
                list.appendChild(row);
                if (window.Iconify) window.Iconify.scan(row);
                syncMode();
            });

            list.addEventListener('click', function (event) {
                var btn = event.target.closest('.remove-room-row');
                if (!btn) return;
                if (list.querySelectorAll('.room-row').length <= 1) return;
                btn.closest('.room-row').remove();
                reindex();
            });

            syncMode();
        })();
    </script>
@endsection
