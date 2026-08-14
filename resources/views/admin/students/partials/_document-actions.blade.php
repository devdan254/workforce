{{--
    Reusable document action set — used identically in the per-application
    Documents section AND the full vault Documents tab, so both stay in sync
    by construction instead of risking drift between two hand-copied blocks.

    Expects: $document, $student, $categories (all already in scope from the
    parent view). $prefix must be passed explicitly and be unique per context
    (e.g. 'app' vs 'vault') — Bootstrap tabs don't unload their DOM, so if the
    same document renders in two tab-panes, its modal IDs must not collide.
--}}
<div class="d-flex gap-1 flex-wrap justify-content-end">
    @if($document->file_path)
        <a href="{{ route('admin.documents.preview', $document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
    @endif

    @if(in_array($document->status, ['required', 'rejected']))
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#upload-{{ $prefix }}-{{ $document->id }}">Upload</button>
    @endif

    @if($document->status === 'under_review')
        <form method="POST" action="{{ route('admin.students.documents.verify', [$student, $document]) }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">Verify</button>
        </form>
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $prefix }}-{{ $document->id }}">Reject</button>
    @endif

    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit-{{ $prefix }}-{{ $document->id }}">Edit</button>

    @can('delete', $document)
        <form method="POST" action="{{ route('admin.students.documents.destroy', [$student, $document]) }}" onsubmit="return confirm('Delete this document? This cannot be undone.');">
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
            <form method="POST" action="{{ route('admin.students.documents.upload', [$student, $document]) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Upload "{{ $document->name }}"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary small">Use this when you've received the file directly (email, in person) and are logging it on the student's behalf.</p>
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
            <form method="POST" action="{{ route('admin.students.documents.reject', [$student, $document]) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Reject "{{ $document->name }}"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Reason the student will see"></textarea>
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
            <form method="POST" action="{{ route('admin.students.documents.update', [$student, $document]) }}">
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
