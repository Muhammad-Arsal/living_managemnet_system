@extends('backend::admin.layouts.app')

@section('title', 'Properties')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Portfolio</p>
                        <h5 class="lms-page-header__title">Properties</h5>
                        <p class="lms-page-header__subtitle">UK rental properties. Occupancy comes from the current tenancy.</p>
                    </div>
                    <a href="{{ route('admin.properties.create') }}" class="btn btn-primary lms-btn-add">
                        <i class="iconify" data-icon="bx:bx-plus"></i>
                        Add Property
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.properties.index') }}" class="lms-filter-bar">
                        <div>
                            <label for="column" class="form-label">Select By Column</label>
                            <select name="column" id="column" class="form-select">
                                <option value="">Select By Column</option>
                                @foreach ($filterColumns as $value => $label)
                                    <option value="{{ $value }}" @selected(request('column') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="search" class="form-label">Search</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="iconify" data-icon="bx:bx-search"></i></span>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Search properties" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div>
                            <label for="property_type_id" class="form-label">Type</label>
                            <select name="property_type_id" id="property_type_id" class="form-select">
                                <option value="">All types</option>
                                @foreach ($propertyTypes as $type)
                                    <option value="{{ $type->id }}" @selected((string) request('property_type_id') === (string) $type->id)>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="occupancy" class="form-label">Occupancy</label>
                            <select name="occupancy" id="occupancy" class="form-select">
                                <option value="">All</option>
                                <option value="occupied" @selected(request('occupancy') === 'occupied')>Occupied</option>
                                <option value="vacant" @selected(request('occupancy') === 'vacant')>Vacant</option>
                            </select>
                        </div>
                        <div class="lms-filter-bar__actions">
                            <button type="submit" class="lms-filter-btn lms-filter-btn--primary">
                                <i class="iconify" data-icon="bx:bx-filter-alt"></i>
                                Submit
                            </button>
                            <a href="{{ route('admin.properties.index') }}" class="lms-filter-btn lms-filter-btn--ghost">
                                <i class="iconify" data-icon="bx:bx-reset"></i>
                                Reset
                            </a>
                        </div>
                    </form>

                    @if ($properties->count() > 0)
                        <div class="table-responsive lms-table-shell">
                            <table class="table table-hover lms-data-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Property</th>
                                        <th class="d-none d-md-table-cell">Type</th>
                                        <th>Occupancy</th>
                                        <th class="d-none d-lg-table-cell">Current tenant</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($properties as $property)
                                        <tr>
                                            <td>
                                                <div class="lms-person">
                                                    @if ($property->coverImage)
                                                        <img src="{{ $property->coverImage->url }}" alt="{{ $property->name }}" class="lms-person__avatar property-index-thumb">
                                                    @else
                                                        <span class="lms-person__initials"><i class="iconify" data-icon="bx:bx-home"></i></span>
                                                    @endif
                                                    <div class="lms-person__meta">
                                                        <span class="lms-person__name">{{ $property->name }}</span>
                                                        <span class="lms-person__email">{{ $property->postcode }} · {{ $property->city }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">{{ $property->propertyType?->name ?? '—' }}</td>
                                            <td>
                                                @if ($property->isOccupied())
                                                    <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>Occupied</span>
                                                @else
                                                    <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>Vacant</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                {{ $property->currentTenancy?->tenant?->full_name ?? '—' }}
                                            </td>
                                            <td class="text-end">
                                                <div class="lms-actions justify-content-end">
                                                    @if (! $property->isOccupied())
                                                        <a href="{{ route('admin.properties.edit', ['property' => $property, 'tab' => 'current']) }}" class="lms-action-btn lms-action-btn--edit">
                                                            <i class="iconify" data-icon="bx:bx-user-plus"></i>
                                                            <span>Assign</span>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('admin.properties.edit', $property) }}" class="lms-action-btn lms-action-btn--edit">
                                                        <i class="iconify" data-icon="bx:bx-edit-alt"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <button type="button" class="lms-action-btn lms-action-btn--delete" data-bs-toggle="modal" data-bs-target="#deletePropertyModal" data-delete-url="{{ route('admin.properties.destroy', $property) }}" data-property-name="{{ $property->name }}">
                                                        <i class="iconify" data-icon="bx:bx-trash"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $properties->links() }}</div>
                    @else
                        <div class="lms-empty-state">
                            <div class="lms-empty-state__icon">
                                <i class="iconify" data-icon="bx:bx-building-house"></i>
                            </div>
                            @if (request()->filled('search') || request()->filled('column') || request()->filled('occupancy') || request()->filled('property_type_id'))
                                <p class="mb-3">No properties match your search.</p>
                                <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @else
                                <p class="mb-3">No properties found.</p>
                                <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">Create First Property</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletePropertyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Property</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="deletePropertyName"></strong>? Properties with tenancy history cannot be deleted.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deletePropertyForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            var modalEl = document.getElementById('deletePropertyModal');
            if (!modalEl) return;
            modalEl.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                if (!button) return;
                document.getElementById('deletePropertyForm').action = button.getAttribute('data-delete-url');
                document.getElementById('deletePropertyName').textContent = button.getAttribute('data-property-name') || 'this property';
            });
        })();
    </script>
@endsection
