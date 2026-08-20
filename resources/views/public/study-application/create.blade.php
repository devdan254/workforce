<x-public-layout title="Study Abroad Application | Altura Workforce Solutions">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="padding: var(--space-7) 0 var(--space-6);">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><a href="{{ route('public.study-abroad.index') }}">Study Abroad</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Application</span></div>
    <span class="hero-mini">Study Abroad Application</span>
    <h1>{{ $posting ? 'Apply to '.$posting->university_name : 'Start Your Study Abroad Application' }}</h1>
    <p>Complete your application and let our education advisors help you turn your study abroad dream into reality.</p>
  </div>
</section>

<!-- ============ APPLICATION FORM ============ -->
<section class="section bg-white" id="application">
  <div class="container">
    <div class="form-card reveal" style="max-width:900px;margin:0 auto;">

      @if($posting)
        <div class="talent-pool-card" style="margin-bottom:24px;">
          <h4 style="margin-bottom:4px;">{{ $posting->university_name }}</h4>
          <p style="margin:0;font-size:13.5px;">{{ $posting->country }} @if($posting->school_fees_per_semester) · {{ $posting->fees_currency }} {{ number_format($posting->school_fees_per_semester, 0) }} / Semester @endif</p>
        </div>
      @endif

      @if($errors->any())
        <div class="alert" style="background:var(--color-danger-tint);color:var(--color-danger);padding:14px 16px;border-radius:var(--radius-sm);margin-bottom:20px;font-size:13.5px;">
          @foreach($errors->all() as $error)
            <p style="margin:0;">{{ $error }}</p>
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ $posting ? route('public.study-application.store', $posting) : route('public.study-application.store.general') }}">
        @csrf

        @guest
          <h3 class="mb-5">Create Your Free Profile</h3>
          <div class="form-row-2">
            <div class="form-field"><label>Full Name <span class="req">*</span></label><input type="text" name="name" value="{{ old('name') }}" required></div>
            <div class="form-field"><label>Email <span class="req">*</span></label><input type="email" name="email" value="{{ old('email') }}" required></div>
          </div>
          <div class="form-row-2">
            <div class="form-field"><label>Password <span class="req">*</span></label><input type="password" name="password" required></div>
            <div class="form-field"><label>Confirm Password <span class="req">*</span></label><input type="password" name="password_confirmation" required></div>
          </div>
          <h3 class="mb-5 mt-6">Application Details</h3>
        @endguest

        <div class="form-row-2">
          <div class="form-field"><label>Phone Number <span class="req">*</span></label><input type="tel" name="phone" value="{{ old('phone') }}" required></div>
          <div class="form-field"><label>Country <span class="req">*</span></label><input type="text" name="country" value="{{ old('country') }}" required></div>
        </div>
        <div class="form-row-2">
          <div class="form-field">
            <label>Preferred Study Destination <span class="req">*</span></label>
            <select name="destination" required>
              <option value="">Select destination</option>
              @foreach($destinations as $destination)
                <option value="{{ $destination }}" @selected(old('destination', $posting?->country) === $destination)>{{ $destination }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-field"><label>Preferred Course <span class="req">*</span></label><input type="text" name="course" value="{{ old('course') }}" required></div>
        </div>
        <div class="form-row-2">
          <div class="form-field">
            <label>Highest Education Level <span class="req">*</span></label>
            <select name="highest_qualification" required>
              <option value="">Select level</option>
              @foreach(['High School', 'Diploma', "Bachelor's Degree", "Master's Degree"] as $level)
                <option value="{{ $level }}" @selected(old('highest_qualification') === $level)>{{ $level }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-field"><label>Preferred Intake</label><input type="text" name="intake" placeholder="e.g. January 2027" value="{{ old('intake', $posting?->intake) }}"></div>
        </div>
        <div class="checkbox-field"><input type="checkbox" id="study-agree" name="declaration" value="1" required><label for="study-agree">I agree to be contacted regarding my application.</label></div>
        <button type="submit" class="btn btn-primary btn-block">{{ $posting ? 'Apply to '.$posting->university_name : 'Start My Application' }}</button>

        @guest
          <p style="text-align:center;font-size:13px;color:var(--color-ink-500);margin-top:16px;">
            Already have an account? <a href="{{ route('login') }}">Log in</a>
          </p>
        @endguest
      </form>
    </div>
  </div>
</section>

</x-public-layout>
