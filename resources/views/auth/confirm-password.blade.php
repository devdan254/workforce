<x-guest-layout title="Confirm Password">

    <h2 class="h4 fw-semibold text-center mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Confirm Your Password</h2>
    <p class="text-secondary text-center small mb-4">
        This is a secure area. Please confirm your password before continuing.
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

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <label class="form-label fw-semibold small">Password</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password" autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100">Confirm</button>
    </form>

</x-guest-layout>