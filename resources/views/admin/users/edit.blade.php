<x-admin-layout title="Edit User">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Edit User</h2>

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
        <div class="mb-3">
            @foreach($targetUser->roles as $role)
                <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
            @endforeach
        </div>

        <form method="POST" action="{{ route('admin.users.update', $targetUser) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $targetUser->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $targetUser->email) }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $targetUser->phone) }}">
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
