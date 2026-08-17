<x-guest-layout title="Verify Email">

    <div class="text-center mb-3">
        <i class="fa-solid fa-envelope-circle-check fs-1 text-primary"></i>
    </div>
    <h2 class="h4 fw-semibold text-center mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Verify Your Email</h2>
    <p class="text-secondary text-center small mb-4">
        Thanks for signing up! Before getting started, please verify your email by clicking the link we just emailed you. If you didn't get it, we'll gladly send another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="d-flex flex-column gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-100">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link w-100 text-secondary">Log Out</button>
        </form>
    </div>

</x-guest-layout>