<x-public-layout title="Find International Jobs Abroad | Altura Workforce Solutions" :meta-description="'Explore verified overseas jobs from trusted employers across healthcare, construction, hospitality, engineering, logistics and more with Altura Workforce Solutions.'">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="padding: var(--space-8) 0 var(--space-7); --hero-img:url('https://images.pexels.com/photos/7658250/pexels-photo-7658250.jpeg?auto=compress&cs=tinysrgb&w=1920');">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Find Jobs</span></div>
    <span class="hero-mini">{{ $totalOpen }} Open Opportunit{{ $totalOpen === 1 ? 'y' : 'ies' }}</span>
    <h1>Find International Job Opportunities</h1>
    <p>Explore verified overseas jobs from trusted employers across healthcare, construction, hospitality, engineering, logistics, and more.</p>
    <div class="hero-actions">
      <a href="#job-listings" class="btn btn-primary">Browse Openings <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#talent-pool" class="btn btn-outline">Join Talent Pool</a>
    </div>
  </div>
</section>

<!-- ============ JOBS LAYOUT ============ -->
<section class="section bg-white" id="job-listings">
  <div class="container">

    <button class="mobile-filter-toggle btn btn-ghost btn-block mb-5"><span>Filter Jobs</span> <i class="fa-solid fa-sliders"></i></button>

    <div class="jobs-layout">
      <!-- Sidebar — real server-side search, GET so results are shareable/bookmarkable -->
      <aside class="filter-card">
        <form method="GET" action="{{ route('public.jobs.index') }}">
          <h4><i class="fa-solid fa-magnifying-glass"></i> Search Jobs</h4>
          <div class="form-field">
            <label>Keyword</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Job title or keyword" onchange="this.form.submit()">
          </div>
          <div class="form-field">
            <label>Category</label>
            <select name="category" onchange="this.form.submit()">
              <option value="">All Categories</option>
              @foreach($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-field">
            <label>Country</label>
            <select name="country" onchange="this.form.submit()">
              <option value="">All Countries</option>
              @foreach($countries as $country)
                <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
              @endforeach
            </select>
          </div>
          @if(request()->hasAny(['search', 'category', 'country']))
            <a href="{{ route('public.jobs.index') }}" class="btn btn-ghost btn-sm btn-block">Clear Filters</a>
          @endif
        </form>
        <div class="talent-pool-card" id="talent-pool">
          <h4>Can't Find the Right Job?</h4>
          <p>Join our talent pool and we'll notify you when a matching position becomes available.</p>
          <a href="{{ route('public.job-application-form') }}" class="btn btn-primary btn-sm btn-block">Join Talent Pool <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </aside>

      <!-- Job Cards -->
      <div>
        <div class="flex" style="justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
          <span style="font-weight:600;color:var(--color-ink-600);font-size:14.5px;">
            Showing {{ $jobs->firstItem() ?? 0 }}–{{ $jobs->lastItem() ?? 0 }} of {{ $jobs->total() }} Jobs
          </span>
          <a href="{{ route('public.job-application-form') }}" class="btn btn-ghost btn-sm">+ Create New Application</a>
        </div>

        <div class="jobs-grid reveal-stagger">
          @forelse($jobs as $job)
            <div class="card job-card reveal">
              <div class="job-card-img">
                @if($job->image_url)
                  <img src="{{ $job->image_url }}" alt="{{ $job->title }}" loading="lazy">
                @else
                  <img src="https://images.pexels.com/photos/8961345/pexels-photo-8961345.jpeg?auto=compress&cs=tinysrgb&w=800" alt="{{ $job->title }}" loading="lazy">
                @endif
                @if($job->is_featured)
                  <span class="job-card-tag">Featured</span>
                @endif
                <span class="job-card-country"><i class="fa-solid fa-location-dot"></i> {{ $job->country }}</span>
              </div>
              <div class="job-card-body">
                <h3>{{ $job->title }}</h3>
                <p class="job-desc">{{ \Illuminate\Support\Str::limit($job->description, 100) }}</p>
                <div class="job-card-meta">
                  <span><i class="fa-solid fa-briefcase"></i>{{ $job->vacancies }} Vacanc{{ $job->vacancies === 1 ? 'y' : 'ies' }}</span>
                  <span><i class="fa-solid fa-clock"></i>{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</span>
                </div>
                <div class="job-card-salary">
                  {{ $job->currency }} {{ number_format($job->salary_min, 0) }}@if($job->salary_max) – {{ number_format($job->salary_max, 0) }}@endif
                </div>
                <a href="{{ route('public.jobs.show', $job) }}" class="btn btn-navy btn-block">View Job &amp; Apply <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </div>
          @empty
            <div style="grid-column:1/-1;text-align:center;padding:48px 0;color:var(--color-ink-500);">
              <p style="font-size:16px;margin-bottom:16px;">No jobs match your search right now.</p>
              <a href="{{ route('public.jobs.index') }}" class="btn btn-outline-navy">Clear Filters</a>
            </div>
          @endforelse
        </div>

        @if($jobs->hasPages())
          <div class="pagination-wrap">{{ $jobs->onEachSide(1)->links('public.pagination') }}</div>
        @endif
        <p class="pagination-info">Showing {{ $jobs->firstItem() ?? 0 }}–{{ $jobs->lastItem() ?? 0 }} of {{ $jobs->total() }} Jobs</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ CAN'T FIND CTA ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="final-cta reveal">
      <h2>Can't Find the Right Job?</h2>
      <p>Upload your CV and join our talent pool. We'll contact you when a matching opportunity becomes available.</p>
      <div class="hero-actions"><a href="{{ route('public.job-application-form') }}" class="btn btn-primary">Join Our Talent Pool <i class="fa-solid fa-arrow-right"></i></a></div>
    </div>
  </div>
</section>

<!-- ============ APPLICATION PROCESS ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">The Journey</span>
      <h2>How It Works</h2>
    </div>
    <div class="steps-row reveal-stagger">
      <div class="step-item card reveal"><div class="step-num-circle">1</div><h4>Browse Opportunities</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">2</div><h4>Submit Application</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">3</div><h4>Application Review</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">4</div><h4>Interview &amp; Selection</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">5</div><h4>Visa &amp; Travel</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">6</div><h4>Start Your New Career</h4></div>
    </div>
  </div>
</section>

<!-- ============ RECRUITMENT COUNTRIES ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Destinations</span>
      <h2>Recruitment Countries</h2>
    </div>
    <div class="destination-strip reveal">
      <div class="destination-grid">
        <div class="destination-item"><span class="destination-flag">🇦🇪</span><span>UAE</span></div>
        <div class="destination-item"><span class="destination-flag">🇸🇦</span><span>Saudi Arabia</span></div>
        <div class="destination-item"><span class="destination-flag">🇶🇦</span><span>Qatar</span></div>
        <div class="destination-item"><span class="destination-flag">🇰🇼</span><span>Kuwait</span></div>
        <div class="destination-item"><span class="destination-flag">🇧🇭</span><span>Bahrain</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Success Stories</span>
      <h2>What Job Seekers Say</h2>
    </div>
    <div class="testimonial-grid reveal-stagger">
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/7266068/pexels-photo-7266068.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Peter Mwangi" loading="lazy"><div><div class="review-name">Peter Mwangi</div><div class="review-sub">Warehouse Supervisor — UAE</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">The listing was clear, the application was simple, and the team followed up on every step until I was deployed.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/21674912/pexels-photo-21674912.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Grace Wanjiru" loading="lazy"><div><div class="review-name">Grace Wanjiru</div><div class="review-sub">Hotel Attendant — Qatar</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">I found my job on Altura within weeks. The process was transparent and I always knew what stage I was at.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/31454886/pexels-photo-31454886.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Samuel Kiptoo" loading="lazy"><div><div class="review-name">Samuel Kiptoo</div><div class="review-sub">Security Officer — Qatar</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Reliable, honest and fast. Altura's job listings are exactly as advertised — no surprises.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
    </div>
  </div>
</section>

</x-public-layout>
