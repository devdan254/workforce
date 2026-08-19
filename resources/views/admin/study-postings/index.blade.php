<x-admin-layout title="Study Postings">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Study Postings</h2>
        @can('create', \App\Models\StudyPosting::class)
            <a href="{{ route('admin.study-postings.create') }}" class="btn btn-primary">+ Create Study Posting</a>
        @endcan
    </div>

    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.study-postings.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="University name">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['draft', 'open', 'closed', 'archived'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Country</label>
                <select name="country" class="form-select">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Scholarship</label>
                <select name="scholarship_type" class="form-select">
                    <option value="">All</option>
                    <option value="full" @selected(request('scholarship_type') === 'full')>Full</option>
                    <option value="partial" @selected(request('scholarship_type') === 'partial')>Partial</option>
                    <option value="none" @selected(request('scholarship_type') === 'none')>None</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                <a href="{{ route('admin.study-postings.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>University / College</th>
                        <th>Country</th>
                        <th>Scholarship</th>
                        <th>Fees / Semester</th>
                        <th>Downloads</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postings as $posting)
                        <tr>
                            <td class="fw-semibold">
                                {{ $posting->university_name }}
                                @if($posting->is_featured)
                                    <span class="badge bg-warning-subtle text-warning-emphasis ms-1">Featured</span>
                                @endif
                            </td>
                            <td class="small">{{ $posting->country }}</td>
                            <td class="small text-capitalize">{{ $posting->scholarship_type }}</td>
                            <td class="small">
                                @if($posting->school_fees_per_semester)
                                    {{ $posting->fees_currency }} {{ number_format($posting->school_fees_per_semester, 0) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">{{ $posting->downloads_count }}</td>
                            <td>
                                @php
                                    $badgeClass = match($posting->status) {
                                        'open' => 'bg-success-subtle text-success-emphasis',
                                        'draft' => 'bg-secondary-subtle text-secondary-emphasis',
                                        'closed' => 'bg-warning-subtle text-warning-emphasis',
                                        'archived' => 'bg-danger-subtle text-danger-emphasis',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($posting->status) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                    <a href="{{ route('admin.study-postings.show', $posting) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('admin.study-postings.edit', $posting) }}" class="btn btn-sm btn-outline-secondary">Edit</a>

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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">No study postings yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $postings->links() }}</div>

</x-admin-layout>
