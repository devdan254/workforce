<x-admin-layout title="Study Posting">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('admin.study-postings.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Study Postings</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $posting->university_name }}
                    @if($posting->is_featured)
                        <span class="badge bg-warning-subtle text-warning-emphasis ms-1">Featured</span>
                    @endif
                </h2>
                <p class="text-secondary mb-0">{{ $posting->country }} · <span class="text-capitalize">{{ $posting->scholarship_type }}</span> scholarship</p>
            </div>
            @php
                $badgeClass = match($posting->status) {
                    'open' => 'bg-success-subtle text-success-emphasis',
                    'draft' => 'bg-secondary-subtle text-secondary-emphasis',
                    'closed' => 'bg-warning-subtle text-warning-emphasis',
                    'archived' => 'bg-danger-subtle text-danger-emphasis',
                    default => 'bg-light text-dark',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($posting->status) }}</span>
        </div>

        <hr>
        <div class="d-flex flex-wrap gap-2">
            @can('update', $posting)
                <a href="{{ route('admin.study-postings.edit', $posting) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            @endcan

            @can('publish', $posting)
                @if($posting->status === 'draft')
                    <form method="POST" action="{{ route('admin.study-postings.publish', $posting) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Publish</button>
                    </form>
                @elseif($posting->status === 'open')
                    <form method="POST" action="{{ route('admin.study-postings.unpublish', $posting) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Unpublish</button>
                    </form>
                    <form method="POST" action="{{ route('admin.study-postings.close', $posting) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning">Close</button>
                    </form>
                @endif
                @if($posting->status !== 'archived')
                    <form method="POST" action="{{ route('admin.study-postings.archive', $posting) }}" onsubmit="return confirm('Archive this posting?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Archive</button>
                    </form>
                @endif
            @endcan

            <form method="POST" action="{{ route('admin.study-postings.feature', $posting) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">{{ $posting->is_featured ? 'Unfeature' : 'Feature' }}</button>
            </form>

            <form method="POST" action="{{ route('admin.study-postings.duplicate', $posting) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">Duplicate</button>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Details</h3>
                <dl class="row small mb-0">
                    <dt class="col-4 text-secondary fw-normal">School Fees / Semester</dt>
                    <dd class="col-8">{{ $posting->school_fees_per_semester ? $posting->fees_currency.' '.number_format($posting->school_fees_per_semester, 0) : '—' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Age Requirement</dt><dd class="col-8">{{ $posting->age_requirement ?? '—' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Intake</dt><dd class="col-8">{{ $posting->intake ?? '—' }}</dd>
                </dl>

                <hr>
                <strong class="small">Courses Offered</strong>
                <ul class="small mb-0 mt-1">
                    @foreach($posting->coursesList() as $course)
                        <li>{{ $course }}</li>
                    @endforeach
                </ul>

                @if($posting->description)
                    <hr><strong class="small">Description</strong><p class="small mb-0 mt-1">{{ $posting->description }}</p>
                @endif
                @if($posting->requirements)
                    <hr><strong class="small">Requirements</strong><p class="small mb-0 mt-1">{{ $posting->requirements }}</p>
                @endif
                @if($posting->application_eligibility)
                    <hr><strong class="small">Application Eligibility</strong><p class="small mb-0 mt-1">{{ $posting->application_eligibility }}</p>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            @if($posting->image_url)
                <div class="card stat-card p-3">
                    <img src="{{ $posting->image_url }}" alt="{{ $posting->university_name }}" class="w-100 rounded" style="max-height:180px;object-fit:cover;">
                </div>
            @endif
        </div>
    </div>

    <div class="card stat-card">
        <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Downloads</h3>
            @can('update', $posting)
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDownloadModal">+ Add Download</button>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Title</th><th>Added</th><th class="text-end">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($posting->downloads as $download)
                        <tr>
                            <td class="fw-semibold small">{{ $download->title }}</td>
                            <td class="small text-secondary">{{ $download->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ $download->file_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                                    @can('update', $posting)
                                        <form method="POST" action="{{ route('admin.study-postings.downloads.destroy', [$posting, $download]) }}" onsubmit="return confirm('Remove this download?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary py-4">No downloads added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @can('update', $posting)
        <div class="modal fade" id="addDownloadModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.study-postings.downloads.store', $posting) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Add Download</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Fee Structure 2026" required>
                            </div>
                            <div>
                                <label class="form-label fw-semibold">File</label>
                                <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Download</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

</x-admin-layout>
