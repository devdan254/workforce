<x-admin-layout title="Create Staff Account">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Create Staff Account</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card stat-card p-4" style="max-width:520px;">
        <form method="POST" action="{{ route('admin.users.staff.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Roles &amp; Permissions</label>
                <p class="small text-secondary mb-2">Select at least one. Permissions flow from the role(s) assigned.</p>
                @foreach($assignableRoles as $role)
                    <div class="form-check">
                        <input type="checkbox" name="roles[]" value="{{ $role }}" class="form-check-input" id="role-{{ $role }}" @checked(in_array($role, old('roles', [])))>
                        <label class="form-check-label small" for="role-{{ $role }}">{{ ucwords(str_replace('_', ' ', $role)) }}</label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Create Staff Account</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
