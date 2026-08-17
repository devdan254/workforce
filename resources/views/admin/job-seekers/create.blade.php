<x-admin-layout title="Create Job Seeker">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Create Job Seeker</h2>

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
        <form method="POST" action="{{ route('admin.job-seekers.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Phone</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Professional Title</label>
                    <input type="text" name="professional_title" class="form-control" placeholder="e.g. Registered Nurse" value="{{ old('professional_title') }}">
                </div>
            </div>

            <div class="alert alert-info small">
                A temporary password will be generated and shown once after creation — share it with the candidate securely.
            </div>

            <button type="submit" class="btn btn-primary">Create Job Seeker</button>
            <a href="{{ route('admin.job-seekers.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
