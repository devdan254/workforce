<x-admin-layout title="Job Seekers">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Job Seekers</h2>
        @can('createJobSeeker', \App\Models\User::class)
            <a href="{{ route('admin.job-seekers.create') }}" class="btn btn-primary">+ Create Job Seeker</a>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.job-seekers.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or email">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Country</label>
                <select name="country" class="form-select">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Industry</label>
                <input type="text" name="industry" class="form-control" value="{{ request('industry') }}" placeholder="e.g. Healthcare">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Application Status</label>
                <select name="status_id" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($applicationStatuses as $status)
                        <option value="{{ $status->id }}" @selected((int) request('status_id') === $status->id)>{{ $status->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Assigned Officer</label>
                <select name="assigned_officer_id" class="form-select">
                    <option value="">All Officers</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" @selected((int) request('assigned_officer_id') === $officer->id)>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                <a href="{{ route('admin.job-seekers.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Nationality</th>
                        <th>Professional Title</th>
                        <th>Applications</th>
                        <th>Current Status</th>
                        <th>Assigned Officer</th>
                        <th>Profile</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobSeekers as $jobSeeker)
                        @php
                            $primaryApplication = $jobSeeker->jobApplications->first();
                            $profilePercent = $jobSeeker->jobSeekerProfile?->profile_completion_percent ?? 0;
                        @endphp
                        <tr class="{{ !$jobSeeker->is_active ? 'opacity-50' : '' }}">
                            <td class="fw-semibold">
                                {{ $jobSeeker->name }}
                                @unless($jobSeeker->is_active)
                                    <span class="badge bg-secondary ms-1">Suspended</span>
                                @endunless
                            </td>
                            <td class="small">{{ $jobSeeker->email }}</td>
                            <td class="small">{{ $jobSeeker->phone ?? '—' }}</td>
                            <td class="small">{{ $jobSeeker->jobSeekerProfile?->nationality ?? '—' }}</td>
                            <td class="small">{{ $jobSeeker->jobSeekerProfile?->professional_title ?? '—' }}</td>
                            <td class="small">{{ $jobSeeker->application_totals['active'] }} active / {{ $jobSeeker->application_totals['total'] }} total</td>
                            <td>
                                @if($primaryApplication)
                                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ $primaryApplication->currentStatus->label }}</span>
                                @else
                                    <span class="text-secondary small">No applications</span>
                                @endif
                            </td>
                            <td class="small">{{ $primaryApplication?->assignedOfficer?->name ?? '—' }}</td>
                            <td class="small">{{ $profilePercent }}%</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.job-seekers.show', $jobSeeker) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('admin.job-seekers.edit', $jobSeeker) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @can('suspendJobSeeker', $jobSeeker)
                                        <form method="POST" action="{{ route('admin.job-seekers.suspend', $jobSeeker) }}" onsubmit="return confirm('{{ $jobSeeker->is_active ? 'Suspend' : 'Reactivate' }} {{ $jobSeeker->name }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $jobSeeker->is_active ? 'danger' : 'success' }}">
                                                {{ $jobSeeker->is_active ? 'Suspend' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-secondary py-5">No job seekers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $jobSeekers->links() }}</div>

</x-admin-layout>
