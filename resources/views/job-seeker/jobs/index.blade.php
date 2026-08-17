<x-job-seeker-layout title="Available Jobs">

    <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Available Jobs</h2>
    <p class="text-secondary mb-4">Browse real opportunities managed by Altura — apply directly, no middlemen.</p>

    {{-- Recommended Jobs --}}
    @if($recommendedJobs->isNotEmpty())
        <div class="mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Recommended For You</h3>
            <div class="row g-3">
                @foreach($recommendedJobs as $job)
                    <div class="col-md-4">
                        <div class="card stat-card h-100 border-warning-subtle overflow-hidden">
                            <div class="d-flex align-items-center justify-content-center" style="height:110px;background:{{ $job->colorForCategory() }};">
                                @if($job->image_url)
                                    <img src="{{ $job->image_url }}" alt="{{ $job->title }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <i class="fa-solid {{ $job->iconForCategory() }} text-white" style="font-size:2.5rem;opacity:.9;"></i>
                                @endif
                            </div>
                            <div class="p-3 d-flex flex-column" style="flex:1;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="fw-semibold">{{ $job->title }}</div>
                                    <div class="text-secondary small">{{ $job->country }}</div>
                                </div>
                                <span class="badge bg-warning-subtle text-warning-emphasis">{{ $job->match_score }}% Match</span>
                            </div>
                            <div class="fw-semibold text-primary mb-2">
                                {{ $job->currency }} {{ number_format($job->salary_min, 0) }}@if($job->salary_max) – {{ number_format($job->salary_max, 0) }}@endif
                            </div>
                            <a href="{{ route('job-seeker.jobs.show', $job) }}" class="btn btn-sm btn-outline-primary mt-auto">View Job</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('job-seeker.jobs.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Job title or keyword">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Country</label>
                <select name="country" class="form-select">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Category</label>
                <select name="job_category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) request('job_category_id') === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Employment Type</label>
                <select name="employment_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="permanent" @selected(request('employment_type') === 'permanent')>Permanent</option>
                    <option value="contract" @selected(request('employment_type') === 'contract')>Contract</option>
                    <option value="temporary" @selected(request('employment_type') === 'temporary')>Temporary</option>
                    <option value="seasonal" @selected(request('employment_type') === 'seasonal')>Seasonal</option>
                </select>
            </div>
            <div class="col-md-2 form-check ms-2">
                <input type="checkbox" name="featured" value="1" class="form-check-input" id="featuredOnly" @checked(request('featured'))>
                <label class="form-check-label small" for="featuredOnly">Featured only</label>
            </div>
            <div class="col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            @if(request()->anyFilled(['search','country','job_category_id','employment_type','featured']))
                <div class="col-12">
                    <a href="{{ route('job-seeker.jobs.index') }}" class="small">Reset filters</a>
                </div>
            @endif
        </form>
    </div>

    {{-- Job Grid --}}
    <div class="row g-3">
        @forelse($jobs as $job)
            <div class="col-1 col-sm-2 col-md-3 col-lg-3">
                <div class="card stat-card h-100 d-flex flex-column overflow-hidden">
                    <div class="d-flex align-items-center justify-content-center position-relative" style="height:140px;background:{{ $job->colorForCategory() }};">
                        @if($job->image_url)
                            <img src="{{ $job->image_url }}" alt="{{ $job->title }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <i class="fa-solid {{ $job->iconForCategory() }} text-white" style="font-size:3rem;opacity:.9;"></i>
                        @endif
                        @if($job->is_featured)
                            <span class="badge bg-white text-dark position-absolute top-0 end-0 m-2">Featured</span>
                        @endif
                    </div>
                    <div class="p-3 d-flex flex-column" style="flex:1;">
                    <div class="fw-semibold">{{ $job->title }}</div>
                    <div class="text-secondary small mb-2">{{ $job->category->name }} · {{ $job->city ? $job->city.', ' : '' }}{{ $job->country }}</div>
                    <div class="fw-semibold text-primary mb-2">
                        {{ $job->currency }} {{ number_format($job->salary_min, 0) }}@if($job->salary_max) – {{ number_format($job->salary_max, 0) }}@endif
                        <span class="text-secondary small fw-normal">/mo</span>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge bg-light text-dark border text-capitalize">{{ $job->employment_type }}</span>
                        @if($job->visa_support_provided)<span class="badge bg-light text-dark border">Visa Support</span>@endif
                        @if($job->accommodation_provided)<span class="badge bg-light text-dark border">Accommodation</span>@endif
                    </div>
                    <a href="{{ route('job-seeker.jobs.show', $job) }}" class="btn btn-sm btn-primary mt-auto">View & Apply</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card stat-card p-5 text-center text-secondary">No jobs match these filters right now.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $jobs->links() }}</div>

</x-job-seeker-layout>
