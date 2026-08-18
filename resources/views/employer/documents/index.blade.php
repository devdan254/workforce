<x-employer-layout title="Documents">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Documents</h2>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary-emphasis fs-6">{{ $completed }} of {{ $total }} documents uploaded</span>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                <i class="fa-solid fa-plus"></i> Add Document
            </button>
        </div>
    </div>

    {{-- Status tabs — "needed / uploaded / required / added" maps to the
         real status values: Required (needed, not yet added), Under Review
         (added, awaiting Altura), Verified, Rejected. Each tab preserves
         the current Job filter, and vice versa, same composable pattern
         used on Candidates. --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <ul class="nav nav-pills flex-wrap gap-1 mb-0">
            @foreach(['' => 'All', 'required' => 'Required', 'under_review' => 'Under Review', 'verified' => 'Verified', 'rejected' => 'Rejected'] as $key => $label)
                <li class="nav-item">
                    <a class="nav-link {{ $activeStatus === $key ? 'active' : '' }}" href="{{ route('employer.documents.index', ['status' => $key, 'job_posting_id' => request('job_posting_id')]) }}">{{ $label }}</a>
                </li>
            @endforeach
        </ul>

        <form method="GET" action="{{ route('employer.documents.index') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="status" value="{{ $activeStatus }}">
            <label class="form-label small fw-semibold mb-0 text-nowrap">Job</label>
            <select name="job_posting_id" class="form-select form-select-sm" style="min-width:220px;" onchange="this.form.submit()">
                <option value="">All / General</option>
                @foreach($jobPostings as $posting)
                    <option value="{{ $posting->id }}" @selected((int) request('job_posting_id') === $posting->id)>{{ $posting->title }} — {{ $posting->country }}</option>
                @endforeach
            </select>
            @if(request('job_posting_id'))
                <a href="{{ route('employer.documents.index', ['status' => $activeStatus]) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    @forelse($groupedDocuments as $category => $documents)
        <div class="card stat-card mb-4">
            <div class="p-3 border-bottom bg-light">
                <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">{{ $category }}</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Document</th>
                            <th>Job</th>
                            <th>Status</th>
                            <th>Uploaded</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                            <tr>
                                <td class="fw-semibold">{{ $document->name }}</td>
                                <td class="small text-secondary">{{ $document->jobPosting?->title ?? 'General' }}</td>
                                <td>
                                    @switch($document->status)
                                        @case('required')
                                            <span class="badge bg-warning-subtle text-warning-emphasis">Required</span>
                                            @break
                                        @case('under_review')
                                            <span class="badge bg-primary-subtle text-primary-emphasis">Under Review</span>
                                            @break
                                        @case('verified')
                                            <span class="badge bg-success-subtle text-success-emphasis">Verified</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger-subtle text-danger-emphasis">Rejected</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="small text-secondary">{{ $document->uploaded_at?->format('d M Y') ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        @if(in_array($document->status, ['under_review', 'verified']))
                                            <a href="{{ route('employer.documents.download', $document) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                        @endif
                                        @if(in_array($document->status, ['required', 'rejected']))
                                            <form method="POST" action="{{ route('employer.documents.upload', $document) }}" enctype="multipart/form-data" class="d-flex gap-1">
                                                @csrf
                                                <input type="file" name="file" class="form-control form-control-sm" style="max-width: 160px;" required>
                                                <button type="submit" class="btn btn-sm btn-primary">{{ $document->status === 'rejected' ? 'Replace' : 'Upload' }}</button>
                                            </form>
                                        @endif
                                    </div>
                                    @if($document->status === 'rejected' && $document->rejection_reason)
                                        <div class="small text-danger mt-1">{{ $document->rejection_reason }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card stat-card p-5 text-center text-secondary">
            No documents match these filters.
        </div>
    @endforelse

    <div class="modal fade" id="addDocumentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('employer.documents.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Add Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger small">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="document_category_id" class="form-select" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Document Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Business License" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Related Job (optional)</label>
                            <select name="job_posting_id" class="form-select">
                                <option value="">General — not tied to a specific job</option>
                                @foreach($jobPostings as $posting)
                                    <option value="{{ $posting->id }}">{{ $posting->title }} — {{ $posting->country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold">File(s)</label>
                            <input type="file" name="files[]" class="form-control" multiple required accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">You can select multiple files at once. PDF, JPG or PNG, up to 10MB each.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('addDocumentModal')).show();
            });
        </script>
    @endif

</x-employer-layout>
