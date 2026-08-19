<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobPostingRequest;
use App\Http\Requests\Admin\UpdateJobPostingRequest;
use App\Models\JobCategory;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Job postings are Altura's controlled catalog — per the spec, "Even if an
 * employer later has an employer account, employers do NOT directly publish
 * jobs." This controller is where that catalog actually gets managed; the
 * Job Seeker portal's Browse Jobs page and Admin's "Create Application"
 * dropdown both read from what's created here.
 */
class JobPostingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', JobPosting::class);

        $query = JobPosting::with('category')->withCount('applications');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('job_category_id')) {
            $query->where('job_category_id', $request->integer('job_category_id'));
        }
        if ($request->filled('country')) {
            $query->where('country', $request->string('country'));
        }

        $postings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.job-postings.index', [
            'postings' => $postings,
            'categories' => JobCategory::orderBy('name')->get(),
            'countries' => JobPosting::distinct()->orderBy('country')->pluck('country'),
        ]);
    }

    /**
     * The "View" page — job posting's own details + the full action set
     * (same as the listing row's buttons). Deliberately does NOT include
     * the applicants list — that's applicants() below, a separate page,
     * per explicit instruction to keep the two distinct rather than
     * merging them into one page with an anchor link.
     */
    public function show(JobPosting $jobPosting): View
    {
        $this->authorize('viewAny', JobPosting::class);

        $jobPosting->load('category')->loadCount('applications');

        return view('admin.job-postings.show', ['posting' => $jobPosting]);
    }

    /**
     * The "View Applicants" page — who applied to THIS posting. Kept
     * exactly as originally built; only its route/method name changed
     * (was show(), the applicants list was folded into the View page and
     * back out again per explicit instruction to keep them separate).
     */
    public function applicants(JobPosting $jobPosting): View
    {
        $this->authorize('viewAny', JobPosting::class);

        $jobPosting->load('category');

        $applications = $jobPosting->applications()
            ->with(['jobSeeker.jobSeekerProfile', 'currentStatus'])
            ->latest('applied_at')
            ->get();

        return view('admin.job-postings.applicants', [
            'posting' => $jobPosting,
            'applications' => $applications,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', JobPosting::class);

        return view('admin.job-postings.create', ['categories' => JobCategory::orderBy('name')->get()]);
    }

    public function store(StoreJobPostingRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'accommodation_provided', 'meals_provided', 'visa_support_provided', 'air_ticket_provided']);

        // Explicit boolean() rather than raw pass-through — an unchecked
        // checkbox doesn't submit at all, so relying on the validated array
        // would silently omit the key instead of actually setting it false.
        $data['accommodation_provided'] = $request->boolean('accommodation_provided');
        $data['meals_provided'] = $request->boolean('meals_provided');
        $data['visa_support_provided'] = $request->boolean('visa_support_provided');
        $data['air_ticket_provided'] = $request->boolean('air_ticket_provided');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('job-postings', 'public');
        }

        $data['posted_by'] = $request->user()->id;

        $posting = JobPosting::create($data);

        return redirect()->route('admin.job-postings.index')->with('success', "\"{$posting->title}\" created as {$posting->status}.");
    }

    public function edit(JobPosting $jobPosting): View
    {
        $this->authorize('update', $jobPosting);

        return view('admin.job-postings.edit', ['posting' => $jobPosting, 'categories' => JobCategory::orderBy('name')->get()]);
    }

    public function update(UpdateJobPostingRequest $request, JobPosting $jobPosting): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'accommodation_provided', 'meals_provided', 'visa_support_provided', 'air_ticket_provided']);

        $data['accommodation_provided'] = $request->boolean('accommodation_provided');
        $data['meals_provided'] = $request->boolean('meals_provided');
        $data['visa_support_provided'] = $request->boolean('visa_support_provided');
        $data['air_ticket_provided'] = $request->boolean('air_ticket_provided');

        if ($request->hasFile('image')) {
            if ($jobPosting->image_path) {
                Storage::disk('public')->delete($jobPosting->image_path);
            }
            $data['image_path'] = $request->file('image')->store('job-postings', 'public');
        }

        $jobPosting->update($data);

        return back()->with('success', 'Job posting updated.');
    }

    public function publish(JobPosting $jobPosting): RedirectResponse
    {
        $this->authorize('publish', $jobPosting);

        $jobPosting->update(['status' => 'open']);

        return back()->with('success', "\"{$jobPosting->title}\" is now published and visible to job seekers.");
    }

    public function unpublish(JobPosting $jobPosting): RedirectResponse
    {
        $this->authorize('publish', $jobPosting);

        $jobPosting->update(['status' => 'draft']);

        return back()->with('success', "\"{$jobPosting->title}\" unpublished — back to draft, no longer visible to job seekers.");
    }

    public function close(JobPosting $jobPosting): RedirectResponse
    {
        $this->authorize('publish', $jobPosting);

        $jobPosting->update(['status' => 'closed']);

        return back()->with('success', "\"{$jobPosting->title}\" closed to new applications.");
    }

    public function archive(JobPosting $jobPosting): RedirectResponse
    {
        $this->authorize('publish', $jobPosting);

        $jobPosting->update(['status' => 'archived']);

        return back()->with('success', "\"{$jobPosting->title}\" archived.");
    }

    public function toggleFeatured(JobPosting $jobPosting): RedirectResponse
    {
        $this->authorize('update', $jobPosting);

        $jobPosting->update(['is_featured' => ! $jobPosting->is_featured]);

        return back()->with('success', $jobPosting->is_featured ? "\"{$jobPosting->title}\" is now featured." : "\"{$jobPosting->title}\" is no longer featured.");
    }

    /**
     * Duplicate always lands as a draft, regardless of the source posting's
     * status — a duplicate is a starting point for a new listing, not an
     * instant re-publish, and a fresh slug/timestamps/application count are
     * generated automatically (slug via the model's booted() hook,
     * applications relation simply has nothing to copy since none exist yet).
     */
    public function duplicate(JobPosting $jobPosting): RedirectResponse
    {
        $this->authorize('create', JobPosting::class);

        $copy = $jobPosting->replicate(['slug']);
        $copy->title = $jobPosting->title.' (Copy)';
        $copy->status = 'draft';
        $copy->is_featured = false;
        $copy->posted_by = auth()->id();
        $copy->save();

        return redirect()->route('admin.job-postings.edit', $copy)->with('success', 'Duplicated as a new draft — review before publishing.');
    }
}
