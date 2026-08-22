<x-admin-layout title="Resources">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Resources</h2>
        <a href="{{ route('admin.resources.create') }}" class="btn btn-primary">+ Create Resource</a>
    </div>

    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.resources.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Title">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Audience</label>
                <select name="audience" class="form-select">
                    <option value="">All Audiences</option>
                    <option value="student" @selected(request('audience') === 'student')>Students</option>
                    <option value="job_seeker" @selected(request('audience') === 'job_seeker')>Job Seekers</option>
                    <option value="employer" @selected(request('audience') === 'employer')>Employers</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['guide' => 'Guide', 'faq' => 'FAQ', 'form' => 'Form', 'checklist' => 'Checklist'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                <a href="{{ route('admin.resources.index') }}" class="btn btn-light btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Audience</th>
                        <th>File</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resources as $resource)
                        <tr>
                            <td class="fw-semibold">{{ $resource->title }}</td>
                            <td class="small text-secondary">{{ $resource->category ?? '—' }}</td>
                            <td class="small text-capitalize">{{ $resource->type }}</td>
                            <td class="small">{{ $resource->audienceLabel() }}</td>
                            <td class="small">
                                @if($resource->file_path)
                                    <i class="fa-solid fa-paperclip text-secondary"></i> Yes
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $resource->is_published ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">{{ $resource->is_published ? 'Published' : 'Draft' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.resources.edit', $resource) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" onsubmit="return confirm('Delete this resource? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">No resources yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $resources->links() }}</div>

</x-admin-layout>
