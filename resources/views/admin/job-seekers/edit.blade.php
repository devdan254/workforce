<x-admin-layout title="Edit Job Seeker">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Edit Job Seeker</h2>

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
        <form method="POST" action="{{ route('admin.job-seekers.update', $jobSeeker) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $jobSeeker->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $jobSeeker->email) }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $jobSeeker->phone) }}">
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" @checked(old('is_active', $jobSeeker->is_active))>
                <label class="form-check-label" for="isActive">Account active</label>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.job-seekers.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
