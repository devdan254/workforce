@php
    $ctaUrl = match(true) {
        ! auth()->check() => route('public.home'),
        auth()->user()->isStudent() => route('student.dashboard'),
        auth()->user()->isJobSeeker() => route('job-seeker.dashboard'),
        auth()->user()->isEmployer() => route('employer.dashboard'),
        auth()->user()->isStaff() => route('admin.dashboard'),
        default => route('public.home'),
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} — {{ $heading }} | Altura Workforce Solutions</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(160deg, #071c4d 0%, #0b2159 45%, #123a8c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(200,155,60,0.08);
        }
        body::before { width: 520px; height: 520px; top: -180px; right: -140px; }
        body::after { width: 380px; height: 380px; bottom: -160px; left: -120px; background: rgba(255,255,255,0.04); }
        .card {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.98);
            border-radius: 24px;
            max-width: 480px;
            width: 100%;
            padding: 56px 40px 44px;
            text-align: center;
            box-shadow: 0 30px 80px rgba(0,0,0,0.35);
        }
        .icon-wrap {
            width: 88px;
            height: 88px;
            margin: 0 auto 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fdf3df, #f6e3b4);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-wrap svg { width: 40px; height: 40px; }
        .code {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 3px;
            color: #C89B3C;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 26px;
            font-weight: 700;
            color: #082159;
            margin-bottom: 14px;
            line-height: 1.3;
        }
        p {
            font-size: 15px;
            color: #6B7590;
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #082159;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 14.5px;
            padding: 13px 28px;
            border-radius: 10px;
            transition: background 0.2s ease, transform 0.2s ease;
        }
        .btn:hover { background: #123a8c; transform: translateY(-1px); }
        .btn svg { width: 16px; height: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">{!! $icon !!}</div>
        <div class="code">ERROR {{ $code }}</div>
        <h1>{{ $heading }}</h1>
        <p>{{ $message }}</p>
        <a href="{{ $ctaUrl }}" class="btn">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            Back to Home
        </a>
    </div>
</body>
</html>
