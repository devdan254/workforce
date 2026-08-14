<x-admin-layout title="Edit Student">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Edit {{ $student->name }}</h2>

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
            This edits the student's core account details only. Personal/academic/passport/preferences
            live on their Profile — visible on the Personal tab of their workspace.
        </p>
        <form method="POST" action="{{ route('admin.students.update', $student) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
            </div>
            <div class="form-check mb-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $student->is_active))>
                <label class="form-check-label" for="is_active">Account Active</label>
                <div class="form-text">Unchecking this has the same effect as "Suspend" on the students list.</div>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.students.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
