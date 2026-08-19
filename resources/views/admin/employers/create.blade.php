<x-admin-layout title="Create Employer">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Create Employer</h2>

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
        <form method="POST" action="{{ route('admin.employers.store') }}">
            @csrf

            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Contact Person</h3>
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>

            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Company</h3>
            <div class="mb-3">
                <label class="form-label fw-semibold">Company Name</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Industry</label>
                    <input type="text" name="industry" class="form-control" placeholder="e.g. Healthcare" value="{{ old('industry') }}">
                </div>
            </div>

            <div class="alert alert-info small">
                A temporary password will be generated and shown once after creation — share it with the employer securely.
            </div>

            <button type="submit" class="btn btn-primary">Create Employer</button>
            <a href="{{ route('admin.employers.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
