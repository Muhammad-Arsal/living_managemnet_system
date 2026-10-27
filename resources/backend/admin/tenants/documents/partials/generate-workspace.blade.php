@if ($selectedTemplate)
    <div class="lms-docgen__focus-ring">
        @if ($selectedTemplate->isStatic())
            <form method="POST"
                  action="{{ route('admin.tenants.documents.generate.store', $tenant) }}"
                  id="document-generate-form"
                  class="lms-docgen__form"
                  novalidate>
                @csrf
                <input type="hidden" name="template" value="{{ $selectedTemplate->value }}">

                <div class="lms-docgen__step lms-docgen__anchor" id="docgen-step-actions">
                    <h6 class="property-form-section__title">2. Download PDF</h6>
                    <p class="text-muted small mb-3">This document has no fields to fill. Download the PDF as-is and it will be saved to this tenant's documents.</p>
                    <div id="document-preview-alert" class="alert alert-warning d-none" role="alert"></div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="lms-filter-btn lms-filter-btn--ghost" id="document-preview-btn">
                            <i class="iconify" data-icon="bx:bx-show"></i>
                            Preview
                        </button>
                        <button type="submit" class="btn btn-primary lms-btn-add">
                            <i class="iconify" data-icon="bx:bx-download"></i>
                            Download &amp; save PDF
                        </button>
                    </div>
                </div>
            </form>
        @else
            <form method="POST"
                  action="{{ route('admin.tenants.documents.generate.store', $tenant) }}"
                  id="document-generate-form"
                  class="lms-docgen__form"
                  novalidate>
                @csrf
                <input type="hidden" name="template" value="{{ $selectedTemplate->value }}">

                <div class="lms-docgen__step lms-docgen__anchor" id="docgen-step-context">
                    <h6 class="property-form-section__title">2. Property context</h6>
                    <p class="text-muted small mb-3">Address fields are filled from the selected tenancy when available. You can still edit them below.</p>
                    <div class="row g-3">
                        <div class="col-12 col-lg-8">
                            <label class="form-label" for="tenancy_id">Linked property / tenancy</label>
                            <select name="tenancy_id" id="tenancy_id" class="form-select" data-docgen-tenancy>
                                <option value="">No linked property (fill address manually)</option>
                                @foreach ($tenancies as $tenancy)
                                    <option value="{{ $tenancy->id }}" @selected((string) old('tenancy_id', $selectedTenancy?->id) === (string) $tenancy->id)>
                                        {{ $tenancy->property?->name }}
                                          {{ $tenancy->locationLabel() }}
                                        ({{ $tenancy->started_on?->format('d M Y') }}{{ $tenancy->ended_on ? ' → '.$tenancy->ended_on->format('d M Y') : ' · current' }})
                                    </option>
                                @endforeach
                            </select>
                            @if ($property)
                                <p class="text-muted small mt-2 mb-0">Using: {{ $property->formattedAddress() }}</p>
                            @endif
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
                    <p class="text-muted small mb-3">Checkbox and dropdown options match the original document templates. Edit anything before generating.</p>

                    <div class="row g-3">
                        @foreach ($fieldMeta as $key => $meta)
                            @include('backend::admin.tenants.documents.partials.field-input', [
                                'key' => $key,
                                'meta' => $meta,
                                'fieldValues' => $fieldValues,
                            ])
                        @endforeach
                    </div>
                </div>

                <div class="lms-docgen__step" id="docgen-step-actions">
                    <h6 class="property-form-section__title">4. Preview &amp; generate</h6>
                    <p class="text-muted small mb-3">Preview the document in a new tab, then generate a PDF saved to this tenant’s documents.</p>
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
        @endif
    </div>
@else
    <div class="lms-empty-state py-4">
        <div class="lms-empty-state__icon">
            <i class="iconify" data-icon="bx:bx-file"></i>
        </div>
        <p class="mb-0">Select a template above to continue.</p>
    </div>
@endif
