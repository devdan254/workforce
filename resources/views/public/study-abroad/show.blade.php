<x-public-layout :title="$posting->university_name.' — '.$posting->country.' | Altura Workforce Solutions'" :meta-description="\Illuminate\Support\Str::limit($posting->description, 150)">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="padding: var(--space-7) 0 var(--space-6); --hero-img:url('https://images.pexels.com/photos/7683693/pexels-photo-7683693.jpeg?auto=compress&cs=tinysrgb&w=1920');">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><a href="{{ route('public.study-abroad.index') }}">Study Abroad</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>{{ $posting->university_name }}</span></div>
    @if($posting->scholarship_type !== 'none')
      <span class="hero-mini">{{ ucfirst($posting->scholarship_type) }} Scholarship Available</span>
    @endif
    <h1>{{ $posting->university_name }} — {{ $posting->country }}</h1>
    <p>{{ \Illuminate\Support\Str::limit($posting->description, 160) }}</p>
  </div>
</section>

<!-- ============ DETAILS ============ -->
<section class="section bg-white">
  <div class="container">

    <div class="jobdesc-header reveal">
      @if($posting->image_url)
        <img src="{{ $posting->image_url }}" alt="{{ $posting->university_name }}" loading="lazy">
      @else
        <img src="https://images.pexels.com/photos/5875778/pexels-photo-5875778.jpeg?auto=compress&cs=tinysrgb&w=800" alt="{{ $posting->university_name }}" loading="lazy">
      @endif
      <div style="flex:1;">
        <h2 style="font-size:24px;">{{ $posting->university_name }}</h2>
        <p style="color:var(--color-ink-600);font-size:14.5px;margin-top:6px;"><i class="fa-solid fa-location-dot" style="color:var(--color-secondary-dark);margin-right:6px;"></i>{{ $posting->country }}</p>
        <div class="jobdesc-meta-grid">
          <div><strong>School Fees</strong>{{ $posting->school_fees_per_semester ? $posting->fees_currency.' '.number_format($posting->school_fees_per_semester, 0).' / Semester' : 'Contact us' }}</div>
          <div><strong>Scholarship</strong>{{ ucfirst($posting->scholarship_type) }}</div>
          <div><strong>Age Requirement</strong>{{ $posting->age_requirement ?? 'Not specified' }}</div>
          <div><strong>Intake</strong>{{ $posting->intake ?? 'Rolling Intake' }}</div>
        </div>
      </div>
    </div>

    <div class="jobdesc-layout">

      <!-- MAIN CONTENT -->
      <div class="jobdesc-body reveal">
        @if($posting->description)
          <h3>Overview</h3>
          <p>{{ $posting->description }}</p>
        @endif

        <h3>Courses Offered</h3>
        <ul>
          @foreach($posting->coursesList() as $course)
            <li><i class="fa-solid fa-check"></i>{{ $course }}</li>
          @endforeach
        </ul>

        @if($posting->requirements)
          <h3>Requirements</h3>
          <p>{{ $posting->requirements }}</p>
        @endif

        @if($posting->application_eligibility)
          <h3>Application Eligibility</h3>
          <p>{{ $posting->application_eligibility }}</p>
        @endif

        @if($posting->downloads->isNotEmpty())
          <h3>Downloads</h3>
          <div class="jobdesc-company-card" style="flex-direction:column;align-items:stretch;gap:10px;">
            @foreach($posting->downloads as $download)
              <a href="{{ $download->file_url }}" target="_blank" rel="noopener" style="display:flex;align-items:center;gap:10px;color:var(--color-primary);font-weight:600;font-size:14px;text-decoration:none;">
                <i class="fa-solid fa-file-arrow-down"></i> {{ $download->title }}
              </a>
            @endforeach
          </div>
        @endif
      </div>

      <!-- SIDEBAR -->
      <aside class="jobdesc-side">
        <div class="filter-card jobdesc-summary-card">
          <h4 style="margin-bottom:16px;"><i class="fa-solid fa-clipboard-list"></i> Summary</h4>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-location-dot"></i>Country</span><span>{{ $posting->country }}</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-award"></i>Scholarship</span><span>{{ ucfirst($posting->scholarship_type) }}</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-sack-dollar"></i>Fees / Semester</span><span>{{ $posting->school_fees_per_semester ? $posting->fees_currency.' '.number_format($posting->school_fees_per_semester, 0) : 'Contact us' }}</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-calendar"></i>Intake</span><span>{{ $posting->intake ?? 'Rolling Intake' }}</span></div>
          <div class="jobdesc-summary-actions">
            <a href="{{ route('public.study-application.create', $posting) }}" class="btn btn-primary btn-block">Apply Now <i class="fa-solid fa-arrow-right"></i></a>
            <div class="btn-row">
              <x-share-menu :url="url()->current()" :title="$posting->university_name.' — '.$posting->country" />
            </div>
          </div>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- ============ SIMILAR UNIVERSITIES ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Keep Exploring</span>
      <h2>More in {{ $posting->country }}</h2>
    </div>
    <div class="jobs-grid reveal-stagger">
      @forelse($similarPostings as $similar)
        <div class="card job-card reveal">
          <div class="job-card-img">
            @if($similar->image_url)
              <img src="{{ $similar->image_url }}" alt="{{ $similar->university_name }}" loading="lazy">
            @else
              <img src="https://images.pexels.com/photos/5875778/pexels-photo-5875778.jpeg?auto=compress&cs=tinysrgb&w=800" alt="{{ $similar->university_name }}" loading="lazy">
            @endif
            <span class="job-card-country"><i class="fa-solid fa-location-dot"></i> {{ $similar->country }}</span>
          </div>
          <div class="job-card-body">
            <h3>{{ $similar->university_name }}</h3>
            <p class="job-desc">{{ \Illuminate\Support\Str::limit($similar->description, 100) }}</p>
            <a href="{{ route('public.study-abroad.show', $similar) }}" class="btn btn-navy btn-block">View Details &amp; Apply <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      @empty
        <p style="grid-column:1/-1;text-align:center;color:var(--color-ink-500);">No other universities in {{ $posting->country }} right now — <a href="{{ route('public.study-abroad.index') }}">browse all universities</a> instead.</p>
      @endforelse
    </div>
  </div>
</section>

</x-public-layout>
