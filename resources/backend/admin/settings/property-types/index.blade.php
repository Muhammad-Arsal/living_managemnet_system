@extends('backend::admin.layouts.app')

@section('title', 'Property Types')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Settings</p>
                        <h5 class="lms-page-header__title">Property Types</h5>
                        <p class="lms-page-header__subtitle">Configurable types shown when creating and editing properties.</p>
                    </div>
                    <a href="{{ route('admin.settings.property-types.create') }}" class="btn btn-primary lms-btn-add">
                        <i class="iconify" data-icon="bx:bx-plus"></i>
                        Add Property Type
                    </a>
                </div>
                <div class="card-body">
                    @if ($propertyTypes->count() > 0)
                        <div class="table-responsive lms-table-shell">
                            <table class="table table-hover lms-data-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th class="d-none d-md-table-cell">Order</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($propertyTypes as $propertyType)
                                        <tr>
                                            <td><span class="lms-person__name">{{ $propertyType->name }}</span></td>
                                            <td>
                                                @if ($propertyType->is_active)
                                                    <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span>Active</span>
                                                @else
                                                    <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span>Inactive</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-md-table-cell">{{ $propertyType->sort_order }}</td>
                                            <td class="text-end">
                                                <div class="lms-actions justify-content-end">
                                                    <a href="{{ route('admin.settings.property-types.edit', $propertyType) }}" class="lms-action-btn lms-action-btn--edit">
                                                        <i class="iconify" data-icon="bx:bx-edit-alt"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <form action="{{ route('admin.settings.property-types.destroy', $propertyType) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this property type? Properties already using it must keep their reference — delete only unused types.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="lms-action-btn lms-action-btn--delete">
                                                            <i class="iconify" data-icon="bx:bx-trash"></i>
                                                            <span>Delete</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $propertyTypes->links() }}</div>
                    @else
                        <div class="lms-empty-state">
                            <div class="lms-empty-state__icon">
                                <i class="iconify" data-icon="bx:bx-home"></i>
                            </div>
                            <p class="mb-3">No property types found.</p>
                            <a href="{{ route('admin.settings.property-types.create') }}" class="btn btn-primary">Create First Type</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
