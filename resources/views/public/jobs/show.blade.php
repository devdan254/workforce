<x-public-layout :title="$job->title.' — '.$job->city.', '.$job->country.' | Altura Workforce Solutions'" :meta-description="\Illuminate\Support\Str::limit($job->description, 150)">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="padding: var(--space-7) 0 var(--space-6); --hero-img:url('https://images.pexels.com/photos/11321790/pexels-photo-11321790.jpeg?auto=compress&cs=tinysrgb&w=1920');">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><a href="{{ route('public.jobs.index') }}">Find Jobs</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Job Details</span></div>
    @if($job->is_featured)
      <span class="hero-mini">Featured Opportunity</span>
    @endif
    <h1>{{ $job->title }} — {{ $job->city ? $job->city.', ' : '' }}{{ $job->country }}</h1>
    <p>{{ \Illuminate\Support\Str::limit($job->description, 160) }}</p>
  </div>
</section>

<!-- ============ JOB DETAILS ============ -->
<section class="section bg-white">
  <div class="container">

    <div class="jobdesc-header reveal">
      @if($job->image_url)
        <img src="{{ $job->image_url }}" alt="{{ $job->title }}" loading="lazy">
      @else
        <img src="https://images.pexels.com/photos/8961345/pexels-photo-8961345.jpeg?auto=compress&cs=tinysrgb&w=800" alt="{{ $job->title }}" loading="lazy">
      @endif
      <div style="flex:1;">
        @if($job->is_featured)
          <span class="job-card-tag" style="position:static;display:inline-block;margin-bottom:10px;">Featured</span>
        @endif
        <h2 style="font-size:24px;">{{ $job->title }}</h2>
        <p style="color:var(--color-ink-600);font-size:14.5px;margin-top:6px;"><i class="fa-solid fa-location-dot" style="color:var(--color-secondary-dark);margin-right:6px;"></i>{{ $job->city ? $job->city.', ' : '' }}{{ $job->country }}</p>
        <div class="jobdesc-meta-grid">
          <div><strong>Salary</strong>{{ $job->currency }} {{ number_format($job->salary_min, 0) }}@if($job->salary_max) – {{ number_format($job->salary_max, 0) }}@endif / Month</div>
          <div><strong>Experience</strong>{{ $job->experience_required ?? 'Not specified' }}</div>
          <div><strong>Education</strong>{{ $job->education_requirement ?? 'Not specified' }}</div>
          <div><strong>Employment Type</strong>{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</div>
        </div>
      </div>
    </div>

    <div class="jobdesc-layout">

      <!-- MAIN CONTENT -->
      <div class="jobdesc-body reveal">
        @if($job->description)
          <h3>Job Overview</h3>
          <p>{{ $job->description }}</p>
        @endif

        @if($job->responsibilitiesList())
          <h3>Responsibilities</h3>
          <ul>
            @foreach($job->responsibilitiesList() as $item)
              <li><i class="fa-solid fa-check"></i>{{ $item }}</li>
            @endforeach
          </ul>
        @endif

        @if($job->requirementsList())
          <h3>Requirements</h3>
          <ul>
            @foreach($job->requirementsList() as $item)
              <li><i class="fa-solid fa-check"></i>{{ $item }}</li>
            @endforeach
          </ul>
        @endif

        @if($job->skillsList())
          <h3>Skills</h3>
          <ul>
            @foreach($job->skillsList() as $item)
              <li><i class="fa-solid fa-check"></i>{{ $item }}</li>
            @endforeach
          </ul>
        @endif

        <h3>What's Included</h3>
        <ul>
          @if($job->accommodation_provided)<li><i class="fa-solid fa-house"></i>Free accommodation provided by the employer.</li>@endif
          @if($job->meals_provided)<li><i class="fa-solid fa-utensils"></i>Meals provided.</li>@endif
          @if($job->visa_support_provided)<li><i class="fa-solid fa-file-contract"></i>Full visa and work permit processing support.</li>@endif
          @if($job->air_ticket_provided)<li><i class="fa-solid fa-plane"></i>Air ticket included.</li>@endif
          @if(!$job->accommodation_provided && !$job->meals_provided && !$job->visa_support_provided && !$job->air_ticket_provided)
            <li><i class="fa-solid fa-circle-info"></i>Contact Altura Workforce Solutions for details on what's included with this role.</li>
          @endif
        </ul>

        @if($job->benefitsList())
          <h3>Benefits</h3>
          <ul>
            @foreach($job->benefitsList() as $item)
              <li><i class="fa-solid fa-check"></i>{{ $item }}</li>
            @endforeach
          </ul>
        @endif

        @if($job->working_hours)
          <h3>Working Hours</h3>
          <p>{{ $job->working_hours }}</p>
        @endif

        <h3>Company Information</h3>
        <div class="jobdesc-company-card">
          <div class="icon-badge"><i class="fa-solid fa-building"></i></div>
          <div>
            <h4 style="font-size:15.5px;color:var(--color-primary);margin-bottom:6px;">Verified Placement Partner</h4>
            <p style="font-size:13.5px;color:var(--color-ink-600);line-height:1.7;">Every employer partner is verified by Altura Workforce Solutions before any placement is confirmed. Full company details are shared directly with you once your application progresses.</p>
          </div>
        </div>
      </div>

      <!-- SIDEBAR -->
      <aside class="jobdesc-side">
        <div class="filter-card jobdesc-summary-card">
          <h4 style="margin-bottom:16px;"><i class="fa-solid fa-clipboard-list"></i> Job Summary</h4>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-sack-dollar"></i>Salary</span><span>{{ $job->currency }} {{ number_format($job->salary_min, 0) }}@if($job->salary_max) – {{ number_format($job->salary_max, 0) }}@endif</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-location-dot"></i>Country</span><span>{{ $job->country }}@if($job->city) ({{ $job->city }})@endif</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-briefcase"></i>Vacancies</span><span>{{ $job->vacancies }}</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-house"></i>Accommodation</span><span>{{ $job->accommodation_provided ? 'Provided' : 'Not provided' }}</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-passport"></i>Visa</span><span>{{ $job->visa_support_provided ? 'Employer-Sponsored' : 'Not included' }}</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-utensils"></i>Meals</span><span>{{ $job->meals_provided ? 'Provided' : 'Not provided' }}</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-plane"></i>Air Ticket</span><span>{{ $job->air_ticket_provided ? 'Included' : 'Not included' }}</span></div>
          <div class="jobdesc-summary-row"><span><i class="fa-solid fa-calendar-xmark"></i>Deadline</span><span>{{ $job->application_deadline?->format('d M Y') ?? 'Rolling Intake' }}</span></div>
          <div class="jobdesc-summary-actions">
            <a href="{{ route('public.job-application-form', ['job' => $job->id]) }}" class="btn btn-primary btn-block">Apply Now <i class="fa-solid fa-arrow-right"></i></a>
            <div class="btn-row">
              <button type="button" class="btn btn-outline-navy btn-sm btn-block"><i class="fa-solid fa-bookmark"></i> Save Job</button>
              <x-share-menu :url="url()->current()" :title="$job->title.' — '.$job->city.', '.$job->country" />
            </div>
          </div>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- ============ SIMILAR OPPORTUNITIES ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Keep Exploring</span>
      <h2>Similar Opportunities</h2>
    </div>
    <div class="jobs-grid reveal-stagger">
      @forelse($similarJobs as $similar)
        <div class="card job-card reveal">
          <div class="job-card-img">
            @if($similar->image_url)
              <img src="{{ $similar->image_url }}" alt="{{ $similar->title }}" loading="lazy">
            @else
              <img src="https://images.pexels.com/photos/8961345/pexels-photo-8961345.jpeg?auto=compress&cs=tinysrgb&w=800" alt="{{ $similar->title }}" loading="lazy">
            @endif
            @if($similar->is_featured)<span class="job-card-tag">Featured</span>@endif
            <span class="job-card-country"><i class="fa-solid fa-location-dot"></i> {{ $similar->country }}</span>
          </div>
          <div class="job-card-body">
            <h3>{{ $similar->title }}</h3>
            <p class="job-desc">{{ \Illuminate\Support\Str::limit($similar->description, 100) }}</p>
            <div class="job-card-salary">{{ $similar->currency }} {{ number_format($similar->salary_min, 0) }}@if($similar->salary_max) – {{ number_format($similar->salary_max, 0) }}@endif</div>
            <a href="{{ route('public.jobs.show', $similar) }}" class="btn btn-navy btn-block">View Job &amp; Apply <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      @empty
        <p style="grid-column:1/-1;text-align:center;color:var(--color-ink-500);">No similar openings right now — <a href="{{ route('public.jobs.index') }}">browse all jobs</a> instead.</p>
      @endforelse
    </div>
  </div>
</section>

</x-public-layout>
