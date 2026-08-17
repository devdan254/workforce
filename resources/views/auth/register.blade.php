<x-guest-layout title="Create Account">

    <div class="btn-group w-100 mb-4" role="group">
        <a href="{{ route('register') }}" class="btn {{ $role === 'student' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="fa-solid fa-graduation-cap"></i> Student
        </a>
        <a href="{{ route('register', ['as' => 'job_seeker']) }}" class="btn {{ $role === 'job_seeker' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="fa-solid fa-briefcase"></i> Job Seeker
        </a>
    </div>

    <h2 class="h4 fw-semibold text-center mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
        Create Your {{ $role === 'job_seeker' ? 'Job Seeker' : 'Student' }} Account
    </h2>
    <p class="text-secondary text-center small mb-4">
        {{ $role === 'job_seeker' ? "Find your next career abroad — let's get started." : "Begin your study abroad journey with Altura." }}
    </p>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="hidden" name="role" value="{{ $role }}">

        <div class="mb-3">
            <label class="form-label fw-semibold small">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus autocomplete="name">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autocomplete="username">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Password</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            Create {{ $role === 'job_seeker' ? 'Job Seeker' : 'Student' }} Account
        </button>

        <p class="text-center small text-secondary mb-0">
            Already have an account? <a href="{{ route('login') }}">Log in</a>
        </p>
    </form>

</x-guest-layout>