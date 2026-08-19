<x-admin-layout title="Employers">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Employers</h2>
        @can('createEmployer', \App\Models\User::class)
            <a href="{{ route('admin.employers.create') }}" class="btn btn-primary">+ Create Employer</a>
        @endcan
    </div>

    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.employers.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Company, name, or email">
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
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Industry</label>
                <input type="text" name="industry" class="form-control" value="{{ request('industry') }}" placeholder="e.g. Healthcare">
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
                <a href="{{ route('admin.employers.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Company</th>
                        <th>Industry</th>
                        <th>Country</th>
                        <th>Active Jobs</th>
                        <th>Applicants</th>
                        <th>Workers Hired</th>
                        <th>Outstanding Balance</th>
                        <th>Status</th>
                        <th>Assigned Officer</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employers as $employer)
                        <tr class="{{ !$employer->is_active ? 'opacity-50' : '' }}">
                            <td class="fw-semibold">
                                {{ $employer->employerProfile?->company_name ?? $employer->name }}
                                @unless($employer->is_active)
                                    <span class="badge bg-secondary ms-1">Suspended</span>
                                @endunless
                                <div class="text-secondary small fw-normal">{{ $employer->email }}</div>
                            </td>
                            <td class="small">{{ $employer->employerProfile?->industry ?? '—' }}</td>
                            <td class="small">{{ $employer->employerProfile?->country ?? '—' }}</td>
                            <td class="small">{{ $employer->active_jobs_count }}</td>
                            <td class="small">{{ $employer->applicants_count }}</td>
                            <td class="small">{{ $employer->hired_count }}</td>
                            <td class="small {{ $employer->financial_summary['balance'] > 0 ? 'text-danger' : '' }}">
                                {{ $employer->financial_summary['currency'] }} {{ number_format($employer->financial_summary['balance'], 0) }}
                            </td>
                            <td>
                                <span class="badge {{ $employer->is_active ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                    {{ $employer->is_active ? 'Active' : 'Suspended' }}
                                </span>
                            </td>
                            <td class="small">{{ $employer->employerProfile?->assignedOfficer?->name ?? '—' }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.employers.show', $employer) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('admin.employers.edit', $employer) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @can('suspendEmployer', $employer)
                                        <form method="POST" action="{{ route('admin.employers.suspend', $employer) }}" onsubmit="return confirm('{{ $employer->is_active ? 'Suspend' : 'Reactivate' }} {{ $employer->name }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $employer->is_active ? 'danger' : 'success' }}">
                                                {{ $employer->is_active ? 'Suspend' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-secondary py-5">No employers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $employers->links() }}</div>

</x-admin-layout>
