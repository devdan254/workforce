<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Altura Workforce Solutions' }}</title>
<meta name="description" content="{{ $metaDescription ?? 'Altura Workforce Solutions connects African talent, students and employers with international job placements, study abroad programs, visa support and skilled workforce recruitment.' }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $title ?? 'Altura Workforce Solutions' }}">
<meta property="og:description" content="{{ $metaDescription ?? 'Altura Workforce Solutions connects African talent, students and employers with international job placements, study abroad programs, visa support and skilled workforce recruitment.' }}">
<meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title ?? 'Altura Workforce Solutions' }}">
<meta name="twitter:description" content="{{ $metaDescription ?? 'Altura Workforce Solutions connects African talent, students and employers with international job placements, study abroad programs, visa support and skilled workforce recruitment.' }}">
<link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
<link rel="stylesheet" href="{{ asset('css/components.css') }}">
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "EmploymentAgency",
  "name": "Altura Workforce Solutions",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('assets/images/logo.png') }}",
  "description": "Altura Workforce Solutions connects African talent, students and employers with international job placements, study abroad programs, visa support and workforce recruitment.",
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": "Utalii House, 3rd Floor, Rm No. 321",
    "addressLocality": "Nairobi",
    "addressCountry": "KE"
  },
  "telephone": "+254-758-434-825"
}
</script>
{{ $head ?? '' }}
</head>
<body>

@php
    // One source of truth for which nav item is "active" — every page that
    // uses this layout gets this for free, instead of each page hardcoding
    // class="active" on the wrong link (which is exactly what a copy-pasted
    // static HTML site tends to drift into over time).
    $navItems = [
        ['route' => 'public.home', 'label' => 'Home'],
        ['route' => 'public.jobs.index', 'label' => 'Find Jobs'],
        ['route' => 'public.study-abroad.index', 'label' => 'Study Abroad'],
        ['route' => 'public.visa', 'label' => 'Visa Support'],
        ['route' => 'public.hire', 'label' => 'Hire Talent'],
        ['route' => 'public.about', 'label' => 'About'],
        ['route' => 'public.contact', 'label' => 'Contact'],
    ];
@endphp

<div class="top-bar">
    <div class="container">
      <div class="top-bar-left">
        <a href="tel:+254758434825"><i class="fa-solid fa-phone"></i> +254 758 434 825</a>
        <a href="mailto:alturaworkforcesolutions@gmail.com"><i class="fa-solid fa-envelope"></i> alturaworkforcesolutions@gmail.com</a>
      </div>
      <div class="top-bar-right">
        @auth
          @php
            $dashboardRoute = match(true) {
                auth()->user()->isStudent() => 'student.dashboard',
                auth()->user()->isJobSeeker() => 'job-seeker.dashboard',
                auth()->user()->isEmployer() => 'employer.dashboard',
                default => 'admin.dashboard',
            };
          @endphp
          <a href="{{ route($dashboardRoute) }}">My Account</a>
        @else
          <a href="{{ route('register') }}">Create Account</a>
          <span class="divider"></span>
          <a href="{{ route('login') }}">Login</a>
        @endauth
      </div>
    </div>
  </div>
  <header class="site-header">
    <div class="container nav-wrap">
      <a href="{{ route('public.home') }}" class="brand">
        <span class="brand-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="Altura Workforce Solutions logo"></span>
        <span class="brand-text"><strong>ALTURA</strong><span>Workforce Solutions</span></span>
      </a>
      <nav class="main-nav">
        <ul>
          @foreach($navItems as $item)
            <li><a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">{{ $item['label'] }}</a></li>
          @endforeach
        </ul>
      </nav>
      <div class="nav-cta">
        <a href="{{ route('public.jobs.index') }}" class="btn btn-ghost btn-sm">Go to Jobs</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Create Account <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <button class="mobile-toggle" aria-label="Open menu" aria-expanded="false">
        <span class="mobile-toggle-lines"><span></span><span></span><span></span></span>
        <i class="fa-solid fa-xmark mobile-toggle-x"></i>
      </button>
    </div>
  </header>
  <div class="mobile-menu-backdrop"></div>
  <div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-head">
      <span class="brand-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="Altura Workforce Solutions logo"></span>
      <button class="mobile-menu-close" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <ul>
      @foreach($navItems as $item)
        <li><a href="{{ route($item['route']) }}"><span>{{ $item['label'] }}</span><i class="fa-solid fa-arrow-right"></i></a></li>
      @endforeach
    </ul>
    <div class="mobile-menu-cta">
      <a href="{{ route('login') }}" class="btn btn-outline btn-block">Login</a>
      <a href="{{ route('register') }}" class="btn btn-primary btn-block">Create Account</a>
    </div>
    <div class="mobile-menu-foot">© Altura Workforce Solutions — Where Talent Reaches New Heights</div>
  </div>

{{ $slot }}

<footer class="site-footer">
    <div class="container footer-top">
      <div class="footer-grid">
        <div class="footer-brand-block">
          <a href="{{ route('public.home') }}" class="brand">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Altura Workforce Solutions logo" style="height:56px;border-radius:12px;">
            <span class="brand-text"><strong style="color:#fff;">ALTURA</strong><span>Workforce Solutions</span></span>
          </a>
          <p>Altura Workforce Solutions connects African talent, students and employers with life-changing global opportunities — careers abroad, international education, visa support and skilled workforce recruitment.</p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="{{ route('public.home') }}">Home</a></li>
            <li><a href="{{ route('public.jobs.index') }}">Find Jobs</a></li>
            <li><a href="{{ route('public.study-abroad.index') }}">Study Abroad</a></li>
            <li><a href="{{ route('public.visa') }}">Visa Support</a></li>
            <li><a href="{{ route('public.hire') }}">Hire Talent</a></li>
            <li><a href="{{ route('public.about') }}">About Us</a></li>
            <li><a href="{{ route('public.contact') }}">Contact</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Services</h4>
          <ul>
            <li><a href="{{ route('public.jobs.index') }}">International Recruitment</a></li>
            <li><a href="{{ route('public.study-abroad.index') }}">Study Abroad</a></li>
            <li><a href="{{ route('public.visa') }}">Visa Support</a></li>
            <li><a href="{{ route('public.hire') }}">Employer Recruitment</a></li>
            <li><a href="{{ route('public.hire') }}">Workforce Staffing</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Contact Information</h4>
          <ul class="footer-contact">
            <li><i class="fa-solid fa-phone"></i> +254 758 434 825 / +254 140 132 281</li>
            <li><i class="fa-solid fa-envelope"></i> alturaworkforcesolutions@gmail.com</li>
            <li><i class="fa-solid fa-location-dot"></i> Utalii House, 3rd Floor, Rm No. 321, Nairobi, Kenya</li>
            <li><i class="fa-solid fa-clock"></i> Mon–Fri: 8:00 AM – 5:00 PM</li>
          </ul>
          <form class="newsletter-form" onsubmit="return false;">
            <input type="email" placeholder="Your email address" required>
            <button type="submit">Subscribe</button>
          </form>
        </div>
      </div>
    </div>
    <div class="container footer-bottom">
      <span>&copy; {{ date('Y') }} Altura Workforce Solutions. All rights reserved. — Where Talent Reaches New Heights.</span>
      <div class="footer-bottom-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>
  </footer>
  <button class="back-to-top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
  <a href="https://wa.me/254758434825" class="whatsapp-float" aria-label="Chat on WhatsApp" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i></a>
<script src="{{ asset('js/main.js') }}"></script>
{{ $scripts ?? '' }}
</body>
</html>
