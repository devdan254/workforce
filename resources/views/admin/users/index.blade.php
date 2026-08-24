<x-admin-layout title="All Users">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">All Users</h2>

    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or email">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Role</label>
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="student" @selected(request('role') === 'student')>Students</option>
                    <option value="job_seeker" @selected(request('role') === 'job_seeker')>Job Seekers</option>
                    <option value="employer" @selected(request('role') === 'employer')>Employers</option>
                    <option value="staff" @selected(request('role') === 'staff')>Admin / Staff</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="fw-semibold small">{{ $user->name }}</td>
                            <td class="small text-secondary">{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">{{ $user->is_active ? 'Active' : 'Suspended' }}</span>
                            </td>
                            <td class="small text-secondary">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                @php
                                    $editAbility = match(true) {
                                        $user->isStudent() => 'update',
                                        $user->isJobSeeker() => 'updateJobSeeker',
                                        $user->isEmployer() => 'updateEmployer',
                                        default => 'updateStaff',
                                    };
                                    $suspendAbility = match(true) {
                                        $user->isStudent() => 'suspend',
                                        $user->isJobSeeker() => 'suspendJobSeeker',
                                        $user->isEmployer() => 'suspendEmployer',
                                        default => 'suspendStaff',
                                    };
                                @endphp
                                <div class="d-flex justify-content-end gap-1">
                                    @can($editAbility, $user)
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @endcan
                                    @can($suspendAbility, $user)
                                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">{{ $user->is_active ? 'Suspend' : 'Reactivate' }}</button>
                                        </form>
                                    @endcan
                                    @can('deleteAny', $user)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete {{ $user->name }}\'s account? This cannot be undone and will remove all their related records.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">No users match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>

</x-admin-layout>
