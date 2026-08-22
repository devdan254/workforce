{{--
    Mirrors admin/employers/partials/_document-actions.blade.php exactly —
    same reasoning, just visa-management.* route names.
    Expects: $document, $visaApplication, $categories, $prefix (unique per context).
--}}
<div class="d-flex gap-1 flex-wrap justify-content-end">
    @if($document->file_path)
        <a href="{{ route('admin.documents.preview', $document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
    @endif

    @if(in_array($document->status, ['required', 'rejected']))
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#upload-{{ $prefix }}-{{ $document->id }}">Upload</button>
    @endif

    @if($document->status === 'under_review')
        <form method="POST" action="{{ route('admin.visa-management.documents.verify', [$visaApplication, $document]) }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">Verify</button>
        </form>
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $prefix }}-{{ $document->id }}">Reject</button>
    @endif

    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit-{{ $prefix }}-{{ $document->id }}">Edit</button>

    @can('delete', $document)
        <form method="POST" action="{{ route('admin.visa-management.documents.destroy', [$visaApplication, $document]) }}" onsubmit="return confirm('Delete this document? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    @endcan
</div>

@if(in_array($document->status, ['required', 'rejected']))
<div class="modal fade" id="upload-{{ $prefix }}-{{ $document->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.visa-management.documents.upload', [$visaApplication, $document]) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Upload "{{ $document->name }}"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($document->status === 'under_review')
<div class="modal fade" id="reject-{{ $prefix }}-{{ $document->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.visa-management.documents.reject', [$visaApplication, $document]) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Reject "{{ $document->name }}"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Reason the applicant will see"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="edit-{{ $prefix }}-{{ $document->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.visa-management.documents.update', [$visaApplication, $document]) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Edit Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" value="{{ $document->name }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Category</label>
                        <select name="document_category_id" class="form-select form-select-sm" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected($document->document_category_id === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
