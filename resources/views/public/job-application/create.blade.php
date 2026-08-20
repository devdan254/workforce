<x-public-layout title="Job Application | Altura Workforce Solutions" :meta-description="'Create your free profile with Altura Workforce Solutions to apply for international job opportunities.'">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="padding: var(--space-7) 0 var(--space-6);">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><a href="{{ route('public.jobs.index') }}">Find Jobs</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Application</span></div>
    <span class="hero-mini">{{ $job ? 'Job Application' : 'Join Our Talent Pool' }}</span>
    <h1>{{ $job ? 'Apply for '.$job->title : 'Create Your Free Profile' }}</h1>
    <p>
      @if($job)
        Create your free profile to start your application for this role. Once your account is ready, you'll complete the full application in a few guided steps.
      @else
        Create your free profile and we'll notify you the moment a matching opportunity becomes available.
      @endif
    </p>
  </div>
</section>

<!-- ============ ACCOUNT FORM ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="form-card reveal" style="max-width:560px;margin:0 auto;">

      @if($job)
        <div class="talent-pool-card" style="margin-bottom:24px;">
          <h4 style="margin-bottom:4px;">{{ $job->title }}</h4>
          <p style="margin:0;font-size:13.5px;">{{ $job->city ? $job->city.', ' : '' }}{{ $job->country }} · {{ $job->currency }} {{ number_format($job->salary_min, 0) }}@if($job->salary_max) – {{ number_format($job->salary_max, 0) }}@endif</p>
        </div>
      @endif

      <h3 class="mb-5">Create Your Free Profile</h3>

      @if($errors->any())
        <div class="alert" style="background:var(--color-danger-tint);color:var(--color-danger);padding:14px 16px;border-radius:var(--radius-sm);margin-bottom:20px;font-size:13.5px;">
          @foreach($errors->all() as $error)
            <p style="margin:0;">{{ $error }}</p>
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('public.job-application-form.store') }}">
        @csrf
        @if($job)<input type="hidden" name="job" value="{{ $job->id }}">@endif

        <div class="form-field">
          <label>Full Name <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-field">
          <label>Email Address <span class="req">*</span></label>
          <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="form-field">
          <label>Phone Number</label>
          <input type="tel" name="phone" value="{{ old('phone') }}">
        </div>
        <div class="form-row-2">
          <div class="form-field">
            <label>Password <span class="req">*</span></label>
            <input type="password" name="password" required>
          </div>
          <div class="form-field">
            <label>Confirm Password <span class="req">*</span></label>
            <input type="password" name="password_confirmation" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-4">
          {{ $job ? 'Continue to Application' : 'Create Profile' }} <i class="fa-solid fa-arrow-right"></i>
        </button>
        <p style="text-align:center;font-size:13px;color:var(--color-ink-500);margin-top:16px;">
          Already have an account? <a href="{{ route('login') }}">Log in</a>
        </p>
      </form>
    </div>
  </div>
</section>

</x-public-layout>
