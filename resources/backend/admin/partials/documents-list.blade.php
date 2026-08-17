@if ($documents->isNotEmpty())
    <div class="table-responsive lms-table-shell">
        <table class="table table-hover lms-data-table align-middle mb-0">
            <thead>
                <tr>
                    <th>File</th>
                    <th class="d-none d-md-table-cell">Type</th>
                    <th>Size</th>
                    <th class="d-none d-lg-table-cell">Uploaded</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    <tr>
                        <td>
                            <div class="lms-file">
                                <span class="lms-file__icon">
                                    <i class="iconify" data-icon="bx:bx-file"></i>
                                </span>
                                <span class="lms-file__name" title="{{ $document->original_name }}">{{ $document->original_name }}</span>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <span class="lms-file__type">{{ $document->extensionLabel() }}</span>
                        </td>
                        <td>{{ $document->formattedSize() }}</td>
                        <td class="d-none d-lg-table-cell">{{ $document->created_at?->format('d M Y') }}</td>
                        <td class="text-end">
                            <div class="lms-actions justify-content-end">
                                <a href="{{ route($downloadRouteName, [$parent, $document]) }}" class="lms-action-btn lms-action-btn--edit">
                                    <i class="iconify" data-icon="bx:bx-download"></i>
                                    <span>Download</span>
                                </a>
                                <form action="{{ route($destroyRouteName, [$parent, $document]) }}"
                                    method="POST"
                                    class="d-inline"
                                    data-confirm-title="Remove document"
                                    data-confirm-body="Remove {{ $document->original_name }}? This cannot be undone."
                                    data-confirm-submit="Delete">
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
@else
    <div class="lms-empty-state py-4">
        <div class="lms-empty-state__icon">
            <i class="iconify" data-icon="bx:bx-file"></i>
        </div>
        <p class="mb-0">No documents uploaded yet.</p>
    </div>
@endif
