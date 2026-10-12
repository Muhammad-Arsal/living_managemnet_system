@if ($selectedTemplate)
    <form method="POST"
          action="{{ route('admin.properties.documents.generate.store', $property) }}"
          id="document-generate-form"
          class="lms-docgen__form"
          novalidate>
        @csrf
        <input type="hidden" name="template" value="{{ $selectedTemplate->value }}">

        <div class="lms-docgen__step" id="docgen-step-resident">
            <h6 class="property-form-section__title">2. Select resident</h6>
            <p class="text-muted small mb-3">Documents are generated for a specific resident linked to this property.</p>
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label class="form-label" for="tenant_id">Resident <span class="text-danger">*</span></label>
                    <select name="tenant_id" id="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required data-docgen-tenant>
                        <option value="">Select resident…</option>
                        @if ($currentTenants->isNotEmpty())
                            <optgroup label="Current at this property">
                                @foreach ($currentTenants as $tenant)
                                    <option value="{{ $tenant->id }}" @selected((string) old('tenant_id', $selectedTenant?->id) === (string) $tenant->id)>
                                        {{ $tenant->full_name }} ({{ $tenant->email }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                        <optgroup label="All tenants">
                            @foreach ($allTenants as $tenant)
                                <option value="{{ $tenant->id }}" @selected((string) old('tenant_id', $selectedTenant?->id) === (string) $tenant->id)>
                                    {{ $tenant->full_name }} ({{ $tenant->email }})
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('tenant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="lms-docgen__step" id="docgen-step-fields">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <h6 class="property-form-section__title mb-0">3. Review &amp; edit fields</h6>
                <div class="lms-docgen-legend">
                    <span class="lms-field-badge lms-field-badge--prefilled">Prefilled</span>
                    <span class="lms-field-badge lms-field-badge--editable">Editable</span>
                    <span class="lms-field-badge lms-field-badge--missing">Missing required</span>
                </div>
            </div>
            <p class="text-muted small mb-3">Prefilled values come from property, tenant, tenancy, or site settings. Edit anything before generating.</p>

            <div class="row g-3">
                @foreach ($fieldMeta as $key => $meta)
                    @php
                        $status = $meta['status'];
                        if (old('fields.'.$key) !== null) {
                            $status = filled(old('fields.'.$key))
                                ? ($meta['source_label'] ? 'prefilled' : 'editable')
                                : ($meta['required'] ? 'missing' : 'editable');
                        }
                        $value = old('fields.'.$key, $fieldValues[$key] ?? '');
                    @endphp
                    <div class="col-12 @if ($meta['type'] !== 'textarea') col-md-6 @endif">
                        <label class="form-label d-flex flex-wrap align-items-center gap-2" for="field_{{ $key }}">
                            <span>{{ $meta['label'] }} @if ($meta['required']) <span class="text-danger">*</span> @endif</span>
                            <span class="lms-field-badge lms-field-badge--{{ $status }}">
                                @if ($status === 'prefilled')
                                    Prefilled{{ $meta['source_label'] ? ' · '.$meta['source_label'] : '' }}
                                @elseif ($status === 'missing')
                                    Missing
                                @else
                                    Manual
                                @endif
                            </span>
                        </label>
                        @if ($meta['type'] === 'textarea')
                            <textarea
                                name="fields[{{ $key }}]"
                                id="field_{{ $key }}"
                                rows="3"
                                class="form-control @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif"
                                @if ($meta['required']) required @endif
                            >{{ $value }}</textarea>
                        @elseif ($meta['type'] === 'date')
                            <input
                                type="date"
                                name="fields[{{ $key }}]"
                                id="field_{{ $key }}"
                                value="{{ $value }}"
                                class="form-control @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif"
                                @if ($meta['required']) required @endif
                            >
                        @else
                            <input
                                type="text"
                                name="fields[{{ $key }}]"
                                id="field_{{ $key }}"
                                value="{{ $value }}"
                                class="form-control @error('fields.'.$key) is-invalid @enderror @if ($status === 'missing') is-invalid @endif"
                                @if ($meta['required']) required @endif
                            >
                        @endif
                        @error('fields.'.$key)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lms-docgen__step" id="docgen-step-actions">
            <h6 class="property-form-section__title">4. Preview &amp; generate</h6>
            <p class="text-muted small mb-3">Preview the document in a new tab, then generate a PDF saved to this property’s documents.</p>
            <div id="document-preview-alert" class="alert alert-warning d-none" role="alert"></div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="lms-filter-btn lms-filter-btn--ghost" id="document-preview-btn">
                    <i class="iconify" data-icon="bx:bx-show"></i>
                    Preview
                </button>
                <button type="submit" class="btn btn-primary lms-btn-add">
                    <i class="iconify" data-icon="bx:bx-file"></i>
                    Generate &amp; save PDF
                </button>
            </div>
        </div>
    </form>
@else
    <div class="lms-empty-state py-4">
        <div class="lms-empty-state__icon">
            <i class="iconify" data-icon="bx:bx-file"></i>
        </div>
        <p class="mb-0">Select a template above to continue.</p>
    </div>
@endif
