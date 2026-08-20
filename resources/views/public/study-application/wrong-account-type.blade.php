<x-public-layout title="Study Abroad Application | Altura Workforce Solutions">

<section class="section bg-white">
  <div class="container">
    <div class="form-card reveal" style="max-width:560px;margin:0 auto;text-align:center;">
      <div class="icon-circle" style="margin:0 auto 20px;"><i class="fa-solid fa-circle-info"></i></div>
      <h3 class="mb-3">You're Signed In as {{ $accountType }}</h3>
      <p style="color:var(--color-ink-600);margin-bottom:24px;">
        Study Abroad applications are submitted through a Student profile. Your current account is a {{ $accountType }} account,
        so it can't be used to apply for study programs directly. Log out and create a Student profile, or contact us if you
        believe this is a mistake.
      </p>
      <div class="hero-actions" style="justify-content:center;">
        <a href="{{ route('public.contact') }}" class="btn btn-outline-navy">Contact Us</a>
        <a href="{{ route('public.home') }}" class="btn btn-primary">Back to Home</a>
      </div>
    </div>
  </div>
</section>

</x-public-layout>
