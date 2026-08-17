<x-guest-layout title="Forgot Password">

    <h2 class="h4 fw-semibold text-center mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Forgot Your Password?</h2>
    <p class="text-secondary text-center small mb-4">
        Enter your email and we'll send you a link to reset it.
    </p>

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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label class="form-label fw-semibold small">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Email Password Reset Link</button>

        <p class="text-center small text-secondary mb-0">
            <a href="{{ route('login') }}">← Back to Login</a>
        </p>
    </form>

</x-guest-layout>