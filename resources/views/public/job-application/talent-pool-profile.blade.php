<x-public-layout title="Tell Us About Yourself | Altura Workforce Solutions">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="padding: var(--space-7) 0 var(--space-6);">
  <div class="container">
    <span class="hero-mini">Almost There</span>
    <h1>Tell Us What You're Looking For</h1>
    <p>A few quick details help us reach out with opportunities that actually fit you. This takes about a minute — you can always update it later from your dashboard.</p>
  </div>
</section>

<!-- ============ PROFILE FORM ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="form-card reveal" style="max-width:680px;margin:0 auto;">

      @if($errors->any())
        <div class="alert" style="background:var(--color-danger-tint);color:var(--color-danger);padding:14px 16px;border-radius:var(--radius-sm);margin-bottom:20px;font-size:13.5px;">
          @foreach($errors->all() as $error)
            <p style="margin:0;">{{ $error }}</p>
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('public.job-application-form.talent-pool.store') }}">
        @csrf

        <h3 class="mb-5">About You</h3>
        <div class="form-row-2">
          <div class="form-field"><label>Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}"></div>
          <div class="form-field"><label>Gender</label><select name="gender"><option value="">Select gender</option><option @selected(old('gender', $profile?->gender) === 'Male')>Male</option><option @selected(old('gender', $profile?->gender) === 'Female')>Female</option></select></div>
        </div>
        <div class="form-row-2">
          <div class="form-field"><label>Nationality</label><input type="text" name="nationality" value="{{ old('nationality', $profile?->nationality) }}"></div>
          <div class="form-field"><label>Current Country</label><input type="text" name="country" value="{{ old('country', $profile?->country) }}"></div>
        </div>
        <div class="form-field"><label>Current City</label><input type="text" name="city" value="{{ old('city', $profile?->city) }}"></div>

        <h3 class="mb-5 mt-6">What You Do</h3>
        <div class="form-row-2">
          <div class="form-field"><label>Professional Title</label><input type="text" name="professional_title" placeholder="e.g. Registered Nurse, Electrician" value="{{ old('professional_title', $profile?->professional_title) }}"></div>
          <div class="form-field"><label>Industry</label><input type="text" name="industry" placeholder="e.g. Healthcare, Construction" value="{{ old('industry', $profile?->industry) }}"></div>
        </div>

        <h3 class="mb-5 mt-6">What You're Looking For</h3>
        <div class="form-row-2">
          <div class="form-field"><label>Preferred Countries</label><input type="text" name="preferred_countries" placeholder="e.g. UAE, Qatar, Saudi Arabia" value="{{ old('preferred_countries', is_array($profile?->preferred_countries) ? implode(', ', $profile->preferred_countries) : '') }}"></div>
          <div class="form-field"><label>Preferred Industries</label><input type="text" name="preferred_industries" placeholder="e.g. Hospitality, Logistics" value="{{ old('preferred_industries', is_array($profile?->preferred_industries) ? implode(', ', $profile->preferred_industries) : '') }}"></div>
        </div>
        <div class="form-row-2">
          <div class="form-field">
            <label>Expected Salary</label>
            <div class="flex" style="gap:8px;">
              <input type="text" name="expected_salary_currency" placeholder="USD" maxlength="3" style="max-width:80px;" value="{{ old('expected_salary_currency', $profile?->expected_salary_currency) }}">
              <input type="number" name="expected_salary" placeholder="Amount" value="{{ old('expected_salary', $profile?->expected_salary) }}">
            </div>
          </div>
          <div class="form-field"><label>Earliest Availability</label><input type="date" name="earliest_availability" value="{{ old('earliest_availability', $profile?->earliest_availability?->format('Y-m-d')) }}"></div>
        </div>
        <div class="form-row-2">
          <div class="checkbox-field"><input type="checkbox" id="worked-abroad" name="worked_abroad_before" value="1" @checked(old('worked_abroad_before', $profile?->worked_abroad_before))><label for="worked-abroad">I've worked abroad before</label></div>
          <div class="checkbox-field"><input type="checkbox" id="willing-relocate" name="willing_to_relocate" value="1" @checked(old('willing_to_relocate', $profile?->willing_to_relocate ?? true))><label for="willing-relocate">Willing to relocate</label></div>
        </div>

        <div class="hero-actions mt-6">
          <button type="submit" class="btn btn-primary">Save My Profile <i class="fa-solid fa-arrow-right"></i></button>
          <a href="{{ route('job-seeker.dashboard') }}" class="btn btn-ghost">Skip for now</a>
        </div>
      </form>
    </div>
  </div>
</section>

</x-public-layout>
