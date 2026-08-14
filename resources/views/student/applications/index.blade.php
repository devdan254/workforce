<x-student-layout title="My Applications">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">My Applications</h2>
            <p class="text-secondary mb-0">Track every study abroad application in one place.</p>
        </div>
        <a href="{{ route('student.applications.create') }}" class="btn btn-primary">+ Start New Study Application</a>
    </div>

    {{-- Filters --}}
    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('student.applications.index') }}" class="row g-2 align-items-end">
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
                <label class="form-label small fw-semibold">University</label>
                <select name="university_id" class="form-select">
                    <option value="">All Universities</option>
                    @foreach($universities as $university)
                        <option value="{{ $university->id }}" @selected((int) request('university_id') === $university->id)>{{ $university->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status_id" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($applicationStatuses as $status)
                        <option value="{{ $status->id }}" @selected((int) request('status_id') === $status->id)>{{ $status->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary flex-grow-1">Filter</button>
                <a href="{{ route('student.applications.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>University</th>
                        <th>Course</th>
                        <th>Country</th>
                        <th>Applied</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td class="fw-semibold">{{ $application->university->name }}</td>
                            <td>{{ $application->course->name }}</td>
                            <td>{{ $application->university->country }}</td>
                            <td>{{ $application->submitted_at?->format('d M Y') ?? '—' }}</td>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis">{{ $application->currentStatus->label }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('student.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                No applications yet. <a href="{{ route('student.applications.create') }}">Start your first one →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $applications->links() }}</div>

</x-student-layout>
