<x-admin-layout title="Students">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Students</h2>
        @can('create', \App\Models\User::class)
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">+ Create Student</a>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or email">
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
                <a href="{{ route('admin.students.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Country</th>
                        <th>Application</th>
                        <th>Status</th>
                        <th>Assigned Officer</th>
                        <th>Documents</th>
                        <th>Balance</th>
                        <th>Last Activity</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php $primaryApp = $student->studyApplications->first(); @endphp
                        <tr class="{{ ! $student->is_active ? 'opacity-50' : '' }}">
                            <td>
                                <div class="fw-semibold">{{ $student->name }}</div>
                                <div class="small text-secondary">{{ $student->email }}</div>
                                @if(! $student->is_active)
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Suspended</span>
                                @endif
                            </td>
                            <td>{{ $student->studentProfile?->country ?? '—' }}</td>
                            <td class="small">{{ $primaryApp?->university->name ?? 'No application yet' }}</td>
                            <td>
                                @if($primaryApp)
                                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ $primaryApp->currentStatus->label }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">{{ $primaryApp?->assignedOfficer?->name ?? 'Unassigned' }}</td>
                            <td class="small">{{ $student->document_totals['completed'] }} / {{ $student->document_totals['total'] }}</td>
                            <td class="small {{ $student->financial_summary['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $student->financial_summary['currency'] }} {{ number_format($student->financial_summary['balance'], 0) }}
                            </td>
                            <td class="small text-secondary">{{ $student->updated_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    @can('update', $student)
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="POST" action="{{ route('admin.students.suspend', $student) }}" onsubmit="return confirm('{{ $student->is_active ? 'Suspend' : 'Reactivate' }} {{ $student->name }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $student->is_active ? 'danger' : 'success' }}">
                                                {{ $student->is_active ? 'Suspend' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-5">No students match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $students->links() }}</div>

</x-admin-layout>
