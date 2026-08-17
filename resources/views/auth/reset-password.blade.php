<x-guest-layout title="Reset Password">

    <h2 class="h4 fw-semibold text-center mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Reset Your Password</h2>
    <p class="text-secondary text-center small mb-4">Choose a new password below.</p>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label class="form-label fw-semibold small">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" class="form-control" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">New Password</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
    </form>

</x-guest-layout>