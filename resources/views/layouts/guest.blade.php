<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Altura Workforce Solutions' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body style="background: linear-gradient(135deg, #071c4d 0%, #123a8c 100%); min-height: 100vh;">
    <div class="d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">
        <div style="width: 100%; max-width: 440px;" class="px-3">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Altura Workforce Solutions" style="height: 64px; border-radius: 12px;">
                </a>
            </div>

            <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="card-body p-4 p-md-5">
                    {{ $slot }}
                </div>
            </div>

            <p class="text-center text-white-50 small mt-4 mb-0">
                &copy; {{ date('Y') }} Altura Workforce Solutions — Where Talent Reaches New Heights
            </p>
        </div>
    </div>
</body>
</html>