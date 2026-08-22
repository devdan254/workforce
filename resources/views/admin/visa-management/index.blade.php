<x-admin-layout title="Visa Management">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Visa Management</h2>
            <p class="text-secondary mb-0">One place for every visa application — Students, Job Seekers, and applicants who came in through the visa wizard directly.</p>
        </div>
        @can('create', \App\Models\VisaApplication::class)
            <a href="{{ route('admin.visa-management.create') }}" class="btn btn-primary">+ Add Visa Application</a>
        @endcan
    </div>

    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.visa-management.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, email, passport, destination">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Applicant Type</label>
                <select name="applicant_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="student" @selected(request('applicant_type') === 'student')>Student</option>
                    <option value="job_seeker" @selected(request('applicant_type') === 'job_seeker')>Job Seeker</option>
                    <option value="guest" @selected(request('applicant_type') === 'guest')>Visa-Only Applicant</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Visa Type</label>
                <select name="visa_type" class="form-select">
                    <option value="">All Visa Types</option>
                    @foreach($visaTypes as $type)
                        <option value="{{ $type }}" @selected(request('visa_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status_id" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" @selected((int) request('status_id') === $status->id)>{{ $status->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                <a href="{{ route('admin.visa-management.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Applicant</th>
                        <th>Type</th>
                        <th>Destination</th>
                        <th>Visa Type</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visaApplications as $visa)
                        <tr>
                            <td class="fw-semibold small">{{ $visa->displayName() }}</td>
                            <td>
                                @php
                                    $typeBadge = match($visa->applicantType()) {
                                        'student' => 'bg-primary-subtle text-primary-emphasis',
                                        'job_seeker' => 'bg-info-subtle text-info-emphasis',
                                        default => 'bg-secondary-subtle text-secondary-emphasis',
                                    };
                                    $typeLabel = match($visa->applicantType()) {
                                        'student' => 'Student',
                                        'job_seeker' => 'Job Seeker',
                                        default => 'Visa-Only',
                                    };
                                @endphp
                                <span class="badge {{ $typeBadge }}">{{ $typeLabel }}</span>
                            </td>
                            <td class="small">{{ $visa->destination_country }}</td>
                            <td class="small">{{ $visa->visa_type ?? '—' }}</td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning-emphasis">{{ $visa->currentStatus?->label ?? '—' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.visa-management.show', $visa) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No visa applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $visaApplications->links() }}</div>

</x-admin-layout>
