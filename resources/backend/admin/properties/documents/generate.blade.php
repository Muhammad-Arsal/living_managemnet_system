@extends('backend::admin.layouts.app')

@section('title', 'Generate Document')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Documents · Generate</p>
                        <h5 class="lms-page-header__title">{{ $property->name }}</h5>
                        <p class="lms-page-header__subtitle">{{ $property->formattedAddress() }}</p>
                    </div>
                    <a href="{{ route('admin.properties.edit', ['property' => $property, 'tab' => 'documents']) }}" class="lms-filter-btn lms-filter-btn--ghost">
                        <i class="iconify" data-icon="bx:bx-arrow-back"></i>
                        Back to documents
                    </a>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                    @endif

                    <div class="lms-docgen"
                         id="lms-docgen"
                         data-base-url="{{ route('admin.properties.documents.generate', $property) }}"
                         data-preview-url="{{ route('admin.properties.documents.generate.preview', $property) }}">
                        <div class="lms-docgen__step">
                            <h6 class="property-form-section__title">1. Choose a template</h6>
                            <p class="text-muted small mb-3">Select a document template. Available property and tenant details will be filled in automatically where possible.</p>
                            <div class="lms-docgen-grid" id="lms-docgen-templates">
                                @foreach ($templates as $template)
                                    @php $key = $template['key']; @endphp
                                    <button type="button"
                                            class="lms-docgen-card @if ($selectedTemplate === $key) is-active @endif"
                                            data-docgen-template="{{ $key->value }}"
                                            aria-pressed="{{ $selectedTemplate === $key ? 'true' : 'false' }}">
                                        <span class="lms-docgen-card__icon">
                                            <i class="iconify" data-icon="{{ $template['icon'] }}"></i>
                                        </span>
                                        <span class="lms-docgen-card__body">
                                            <span class="lms-docgen-card__title">{{ $template['label'] }}</span>
                                            <span class="lms-docgen-card__desc">{{ $template['description'] }}</span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div id="lms-docgen-workspace" class="lms-docgen__workspace" aria-live="polite">
                            @include('backend::admin.properties.documents.partials.generate-workspace')
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
            var root = document.getElementById('lms-docgen');
            if (!root) return;

            var baseUrl = root.getAttribute('data-base-url');
            var previewUrl = root.getAttribute('data-preview-url');
            var workspace = document.getElementById('lms-docgen-workspace');
            var templates = document.getElementById('lms-docgen-templates');
            var loading = false;

            function selectedTemplate() {
                var active = templates.querySelector('.lms-docgen-card.is-active');
                return active ? active.getAttribute('data-docgen-template') : '';
            }

            function selectedTenantId() {
                var select = workspace.querySelector('[data-docgen-tenant]');
                return select ? select.value : '';
            }

            function setActiveTemplate(templateKey) {
                templates.querySelectorAll('.lms-docgen-card').forEach(function (card) {
                    var isActive = card.getAttribute('data-docgen-template') === templateKey;
                    card.classList.toggle('is-active', isActive);
                    card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });
            }

            function updateUrl(templateKey, tenantId) {
                var url = new URL(baseUrl, window.location.origin);
                if (templateKey) url.searchParams.set('template', templateKey);
                if (tenantId) url.searchParams.set('tenant_id', tenantId);
                window.history.replaceState({}, '', url.pathname + url.search);
            }

            function refreshIconify(scope) {
                if (window.Iconify && typeof window.Iconify.scan === 'function') {
                    window.Iconify.scan(scope);
                }
            }

            function loadWorkspace(templateKey, tenantId) {
                if (loading) return Promise.resolve();
                loading = true;
                workspace.classList.add('is-loading');

                var url = new URL(baseUrl, window.location.origin);
                url.searchParams.set('partial', '1');
                if (templateKey) url.searchParams.set('template', templateKey);
                if (tenantId) url.searchParams.set('tenant_id', tenantId);

                return fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        if (!response.ok) throw new Error('Failed to load template form.');
                        return response.text();
                    })
                    .then(function (html) {
                        workspace.innerHTML = html;
                        refreshIconify(workspace);
                        bindWorkspace();
                        updateUrl(templateKey, tenantId);
                    })
                    .catch(function () {
                        workspace.innerHTML = '<div class="alert alert-danger mb-0">Could not load the template form. Please try again.</div>';
                    })
                    .finally(function () {
                        loading = false;
                        workspace.classList.remove('is-loading');
                    });
            }

            function firstInvalidField(form) {
                var fields = form.querySelectorAll('input, select, textarea');
                for (var i = 0; i < fields.length; i++) {
                    if (!fields[i].checkValidity()) return fields[i];
                }
                return null;
            }

            function showPreviewAlert(message) {
                var alert = document.getElementById('document-preview-alert');
                if (!alert) return;
                alert.textContent = message;
                alert.classList.remove('d-none');
            }

            function hidePreviewAlert() {
                var alert = document.getElementById('document-preview-alert');
                if (!alert) return;
                alert.classList.add('d-none');
                alert.textContent = '';
            }

            function openPreview(form) {
                hidePreviewAlert();

                if (!form.checkValidity()) {
                    var invalid = firstInvalidField(form);
                    form.reportValidity();
                    if (invalid) invalid.focus({ preventScroll: false });
                    showPreviewAlert('Complete all required fields before previewing the document.');
                    return;
                }

                var previewBtn = document.getElementById('document-preview-btn');
                if (previewBtn) {
                    previewBtn.disabled = true;
                    previewBtn.classList.add('is-busy');
                }

                fetch(previewUrl, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    redirect: 'manual'
                })
                    .then(function (response) {
                        if (response.status === 422) {
                            return response.json().then(function (data) {
                                var message = 'Complete all required fields before previewing the document.';
                                if (data && data.errors) {
                                    var firstKey = Object.keys(data.errors)[0];
                                    if (firstKey && data.errors[firstKey] && data.errors[firstKey][0]) {
                                        message = data.errors[firstKey][0];
                                    }
                                } else if (data && data.message) {
                                    message = data.message;
                                }
                                showPreviewAlert(message);
                                form.reportValidity();
                                throw new Error('validation');
                            });
                        }
                        if (response.type === 'opaqueredirect' || (response.status >= 300 && response.status < 400)) {
                            showPreviewAlert('Complete all required fields before previewing the document.');
                            form.reportValidity();
                            throw new Error('validation');
                        }
                        if (!response.ok) throw new Error('preview-failed');
                        var contentType = response.headers.get('content-type') || '';
                        return response.text().then(function (body) {
                            if (contentType.indexOf('application/json') !== -1) {
                                throw new Error('preview-failed');
                            }
                            if (!body || body.indexOf('<html') === -1) {
                                throw new Error('preview-failed');
                            }
                            return body;
                        });
                    })
                    .then(function (html) {
                        var previewWindow = window.open('', '_blank');
                        if (!previewWindow) {
                            showPreviewAlert('Pop-up blocked. Allow pop-ups for this site to preview documents.');
                            return;
                        }
                        previewWindow.document.open();
                        previewWindow.document.write(html);
                        previewWindow.document.close();
                    })
                    .catch(function (error) {
                        if (error && error.message === 'validation') return;
                        showPreviewAlert('Preview could not be generated. Please check the fields and try again.');
                    })
                    .finally(function () {
                        if (previewBtn) {
                            previewBtn.disabled = false;
                            previewBtn.classList.remove('is-busy');
                        }
                    });
            }

            function bindWorkspace() {
                var form = document.getElementById('document-generate-form');
                if (!form) return;

                var tenantSelect = form.querySelector('[data-docgen-tenant]');
                if (tenantSelect) {
                    tenantSelect.addEventListener('change', function () {
                        loadWorkspace(selectedTemplate(), tenantSelect.value);
                    });
                }

                var previewBtn = document.getElementById('document-preview-btn');
                if (previewBtn) {
                    previewBtn.addEventListener('click', function () {
                        openPreview(form);
                    });
                }

                form.addEventListener('submit', function (event) {
                    hidePreviewAlert();
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        form.reportValidity();
                        showPreviewAlert('Complete all required fields before generating the document.');
                    }
                });
            }

            templates.addEventListener('click', function (event) {
                var card = event.target.closest('[data-docgen-template]');
                if (!card || !templates.contains(card)) return;

                var templateKey = card.getAttribute('data-docgen-template');
                if (!templateKey || templateKey === selectedTemplate()) return;

                var tenantId = selectedTenantId();
                setActiveTemplate(templateKey);
                loadWorkspace(templateKey, tenantId);
            });

            bindWorkspace();
        })();
    </script>
@endsection
