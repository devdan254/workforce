<x-admin-layout title="Create Student">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Create Student</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card stat-card p-4" style="max-width: 560px;">
        <p class="text-secondary small mb-4">
            Use this for offline/walk-in enrollments. A temporary password is generated and shown once —
            share it with the student securely so they can log in and set their own.
        </p>
        <form method="POST" action="{{ route('admin.students.store') }}">
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
            <div class="mb-4">
                <label class="form-label fw-semibold">Country of Residence</label>
                <input type="text" name="country" class="form-control" value="{{ old('country') }}">
            </div>
            <button type="submit" class="btn btn-primary">Create Student</button>
            <a href="{{ route('admin.students.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
