<x-guest-layout title="Log In">

    <h2 class="h4 fw-semibold text-center mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Welcome Back</h2>
    <p class="text-secondary text-center small mb-4">Log in to your Altura account</p>

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold small">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label class="form-label fw-semibold small">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
            <label for="remember_me" class="form-check-label small">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Log In</button>

        <p class="text-center small text-secondary mb-0">
            Don't have an account?
            <a href="{{ route('register') }}">Register as a Student</a>,
            <a href="{{ route('register', ['as' => 'job_seeker']) }}">Job Seeker</a>,
            or <a href="{{ route('register', ['as' => 'employer']) }}">Employer</a>
        </p>
    </form>

</x-guest-layout>
