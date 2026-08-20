<x-public-layout title="Study Abroad Programs | Altura Workforce Solutions" :meta-description="'Get expert guidance on university admissions, student visas, accommodation and travel preparation for studying abroad.'">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="--hero-img:url('https://images.pexels.com/photos/7683693/pexels-photo-7683693.jpeg?auto=compress&cs=tinysrgb&w=1920');">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Study Abroad</span></div>
    <span class="hero-mini">Study Abroad with Altura Workforce Solutions</span>
    <h1>Your Journey to an International Education &amp; Career Starts Here</h1>
    <p>We make your journey to study abroad simple yet successful.</p>
    <div class="hero-actions">
      <a href="#consultation" class="btn btn-primary">Request Free Consultation <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#universities" class="btn btn-outline">Browse Universities</a>
    </div>
  </div>
</section>

<!-- ============ SERVICES + CONSULTATION ============ -->
<section class="section bg-white" id="consultation">
  <div class="container">
    <div class="about-split" style="align-items:flex-start;">
      <div>
        <span class="eyebrow">Our Services</span>
        <h2 class="mb-6">We Make It Look Simple, Though</h2>
        <div class="why-grid reveal-stagger">
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-university"></i></div><div><h4>University &amp; College Application</h4><p>We assist with your university/college application and placements, including follow-ups.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-list-check"></i></div><div><h4>University/College Selection</h4><p>We guide you on which university will best fit your needs.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-comments"></i></div><div><h4>Career Counselling</h4><p>We guide you on what course will best suit your passion.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-passport"></i></div><div><h4>Visa Processing</h4><p>We give you expert visa assistance including completion and submissions.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-house"></i></div><div><h4>Accommodation Booking</h4><p>We assist you to secure comfortable accommodation.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-plane"></i></div><div><h4>Flight Booking</h4><p>We assist you to make your flight arrangements.</p></div></div>
        </div>
      </div>

      <div class="form-card reveal">
        <h3>Not Sure Where to Begin?</h3>
        <p style="font-size:14.5px;color:var(--color-ink-600);margin:12px 0 24px;line-height:1.7;">Speak with one of our education advisors for personalized guidance based on your academic background, career goals, and preferred destination.</p>
        <form data-ajax-form action="{{ route('public.inquiries.consultation') }}">
          @csrf
          <div class="form-row-2">
            <div class="form-field"><label>Full Name <span class="req">*</span></label><input type="text" name="name" required><span class="field-error">Please enter your full name.</span></div>
            <div class="form-field"><label>Email Address <span class="req">*</span></label><input type="email" name="email" required><span class="field-error">Please enter a valid email.</span></div>
          </div>
          <div class="form-row-2">
          <div class="form-field"><label>Phone Number <span class="req">*</span></label><input type="tel" name="phone" required><span class="field-error">Please enter your phone number.</span></div>
          <div class="form-field"><label>Preferred Study Destination</label><select name="destination"><option value="">Select destination</option><option>Germany</option><option>Australia</option><option>England</option><option>South Africa</option><option>USIU – Kenya</option><option>China</option><option>Other</option></select></div>
          </div>
          <div class="form-field"><label>Intended Course</label><input type="text" name="course" placeholder="e.g. Business Administration"></div>
          <button type="submit" class="btn btn-primary btn-block">Request Free Consultation</button>
        </form>
        <div class="form-submit-error" style="display:none;background:var(--color-danger-tint);color:var(--color-danger);padding:12px 16px;border-radius:var(--radius-sm);margin-top:16px;font-size:13.5px;">Something went wrong sending your request. Please try again or contact us directly.</div>
        <div class="form-success">
          <div class="icon-circle"><i class="fa-solid fa-check"></i></div>
          <h3>Request Received!</h3>
          <p style="color:var(--color-ink-600);margin-top:10px;">One of our education advisors will reach out within 1–2 business days.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ ADMISSION REQUIREMENTS ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="about-split">
      <div>
        <span class="eyebrow">Get Ready</span>
        <h2 class="mb-4">Admission Requirements</h2>
        <p style="font-size:15.5px;color:var(--color-ink-600);line-height:1.75;margin-bottom:24px;">Documents you may need. Requirements may vary depending on the university, country, and course — our advisors will guide you through the exact requirements for your application.</p>
        <div class="checklist-grid reveal-stagger">
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Academic Certificates</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Academic Transcripts</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Passport</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Passport-Sized Photographs</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>English Proficiency Results</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Personal Statement</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Recommendation Letters</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Curriculum Vitae</span></div>
        </div>
      </div>
      <div class="about-media reveal">
        <img src="https://images.pexels.com/photos/8093004/pexels-photo-8093004.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Africans graduating abroad" loading="lazy" style="height:480px;">
      </div>
    </div>
  </div>
</section>

<!-- ============ UNIVERSITIES / COLLEGES (DYNAMIC — replaces "Your Path Forward") ============ -->
<section class="section bg-white" id="universities">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Where You Could Study</span>
      <h2>Universities &amp; Colleges</h2>
      <p>Browse current opportunities and apply directly to the one that fits you.</p>
    </div>

    <div class="d-flex justify-content-center mb-6" style="gap:12px;flex-wrap:wrap;">
      <form method="GET" action="{{ route('public.study-abroad.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="University name" style="min-width:200px;">
        <select name="country" onchange="this.form.submit()">
          <option value="">All Countries</option>
          @foreach($countries as $country)
            <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
          @endforeach
        </select>
        <select name="scholarship_type" onchange="this.form.submit()">
          <option value="">All Scholarships</option>
          <option value="full" @selected(request('scholarship_type') === 'full')>Full Scholarship</option>
          <option value="partial" @selected(request('scholarship_type') === 'partial')>Partial Scholarship</option>
          <option value="none" @selected(request('scholarship_type') === 'none')>No Scholarship</option>
        </select>
        <button type="submit" class="btn btn-outline-navy btn-sm">Search</button>
        @if(request()->hasAny(['search', 'country', 'scholarship_type']))
          <a href="{{ route('public.study-abroad.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
      </form>
    </div>

    <div class="jobs-grid reveal-stagger">
      @forelse($postings as $posting)
        <div class="card job-card reveal">
          <div class="job-card-img">
            @if($posting->image_url)
              <img src="{{ $posting->image_url }}" alt="{{ $posting->university_name }}" loading="lazy">
            @else
              <img src="https://images.pexels.com/photos/5875778/pexels-photo-5875778.jpeg?auto=compress&cs=tinysrgb&w=800" alt="{{ $posting->university_name }}" loading="lazy">
            @endif
            @if($posting->scholarship_type !== 'none')
              <span class="job-card-tag">{{ ucfirst($posting->scholarship_type) }} Scholarship</span>
            @endif
            <span class="job-card-country"><i class="fa-solid fa-location-dot"></i> {{ $posting->country }}</span>
          </div>
          <div class="job-card-body">
            <h3>{{ $posting->university_name }}</h3>
            <p class="job-desc">{{ \Illuminate\Support\Str::limit($posting->description, 100) }}</p>
            @if($posting->school_fees_per_semester)
              <div class="job-card-salary">{{ $posting->fees_currency }} {{ number_format($posting->school_fees_per_semester, 0) }} / Semester</div>
            @endif
            <a href="{{ route('public.study-abroad.show', $posting) }}" class="btn btn-navy btn-block">View Details &amp; Apply <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      @empty
        <div style="grid-column:1/-1;text-align:center;padding:48px 0;color:var(--color-ink-500);">
          <p style="font-size:16px;">No universities match your search right now.</p>
          @if(request()->hasAny(['search', 'country', 'scholarship_type']))
            <a href="{{ route('public.study-abroad.index') }}" class="btn btn-outline-navy">Clear Filters</a>
          @endif
        </div>
      @endforelse
    </div>

    @if($postings->hasPages())
      <div class="pagination-wrap">{{ $postings->onEachSide(1)->links('public.pagination') }}</div>
    @endif
  </div>
</section>

<!-- ============ COUNTRIES ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Top Study Destinations</span>
      <h2>Countries You Can Study In</h2>
    </div>
    <div class="sticky-bg-section reveal" style="background-image:url('https://images.pexels.com/photos/34066027/pexels-photo-34066027.jpeg?auto=compress&cs=tinysrgb&w=1920');">
      <div class="country-grid">
        <div class="country-card"><span class="flag-emoji">🇩🇪</span><h4>Germany</h4></div>
        <div class="country-card"><span class="flag-emoji">🇦🇺</span><h4>Australia</h4></div>
        <div class="country-card"><span class="flag-emoji">🇬🇧</span><h4>England</h4></div>
        <div class="country-card"><span class="flag-emoji">🇿🇦</span><h4>South Africa</h4></div>
        <div class="country-card"><span class="flag-emoji">🇰🇪</span><h4>USIU – Kenya</h4></div>
        <div class="country-card"><span class="flag-emoji">🇨🇳</span><h4>China</h4></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Success Stories</span>
      <h2>Success Stories from Our Students</h2>
    </div>
    <div class="testimonial-grid reveal-stagger">
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/19039168/pexels-photo-19039168.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Brian O." loading="lazy"><div><div class="review-name">Brian O.</div><div class="review-sub">Canada</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Altura made my university application process simple and stress-free. Today I'm studying in Canada, and I couldn't be happier.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/29086752/pexels-photo-29086752.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Faith A." loading="lazy"><div><div class="review-name">Faith A.</div><div class="review-sub">Australia</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">From choosing the right course to securing my student visa, the team supported me every step of the way.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/6348688/pexels-photo-6348688.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Kevin M." loading="lazy"><div><div class="review-name">Kevin M.</div><div class="review-sub">United Kingdom</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Professional, responsive, and genuinely committed to helping students succeed abroad.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
    </div>
    <div class="text-center mt-7"><a href="{{ route('public.contact') }}" class="btn btn-outline-navy">View More Reviews <i class="fa-solid fa-arrow-right"></i></a></div>
  </div>
</section>

</x-public-layout>
