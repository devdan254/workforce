<x-admin-layout title="Edit Employer">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Edit Employer Account</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card stat-card p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('admin.employers.update', $employer) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $employer->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $employer->email) }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $employer->phone) }}">
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" @checked(old('is_active', $employer->is_active))>
                <label class="form-check-label" for="isActive">Account active</label>
            </div>

            <p class="text-secondary small">Company details (name, industry, country) are edited from the Employer's own Workspace, not here — this form is account-level only, matching how Job Seeker's account form works.</p>

            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.employers.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
