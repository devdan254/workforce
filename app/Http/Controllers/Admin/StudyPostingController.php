<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudyPostingDownloadRequest;
use App\Http\Requests\Admin\StoreStudyPostingRequest;
use App\Http\Requests\Admin\UpdateStudyPostingRequest;
use App\Models\StudyPosting;
use App\Models\StudyPostingDownload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Mirrors Admin\JobPostingController exactly — same lifecycle, same action
 * set, same reasoning throughout. Study postings are Altura's controlled
 * catalog the same way job postings are; this is what the future public
 * frontend and the Student Portal's "Browse Universities" page (not built
 * yet) will both read from.
 */
class StudyPostingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StudyPosting::class);

        $query = StudyPosting::withCount('downloads');

        if ($request->filled('search')) {
            $query->where('university_name', 'like', '%'.$request->string('search').'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('country')) {
            $query->where('country', $request->string('country'));
        }
        if ($request->filled('scholarship_type')) {
            $query->where('scholarship_type', $request->string('scholarship_type'));
        }

        $postings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.study-postings.index', [
            'postings' => $postings,
            'countries' => StudyPosting::distinct()->orderBy('country')->pluck('country'),
        ]);
    }

    /**
     * Same "View" vs "Edit" split JobPostingController's fix established —
     * a genuine detail page plus the full action set, separate from the
     * edit form.
     */
    public function show(StudyPosting $studyPosting): View
    {
        $this->authorize('viewAny', StudyPosting::class);

        $studyPosting->load('downloads');

        return view('admin.study-postings.show', ['posting' => $studyPosting]);
    }

    public function create(): View
    {
        $this->authorize('create', StudyPosting::class);

        return view('admin.study-postings.create');
    }

    public function store(StoreStudyPostingRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('study-postings', 'public');
        }

        $data['posted_by'] = $request->user()->id;

        $posting = StudyPosting::create($data);

        return redirect()->route('admin.study-postings.index')->with('success', "\"{$posting->university_name}\" created as {$posting->status}.");
    }

    public function edit(StudyPosting $studyPosting): View
    {
        $this->authorize('update', $studyPosting);

        return view('admin.study-postings.edit', ['posting' => $studyPosting]);
    }

    public function update(UpdateStudyPostingRequest $request, StudyPosting $studyPosting): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($studyPosting->image_path) {
                Storage::disk('public')->delete($studyPosting->image_path);
            }
            $data['image_path'] = $request->file('image')->store('study-postings', 'public');
        }

        $studyPosting->update($data);

        return back()->with('success', 'Study posting updated.');
    }

    public function publish(StudyPosting $studyPosting): RedirectResponse
    {
        $this->authorize('publish', $studyPosting);

        $studyPosting->update(['status' => 'open']);

        return back()->with('success', "\"{$studyPosting->university_name}\" is now published and visible to students.");
    }

    public function unpublish(StudyPosting $studyPosting): RedirectResponse
    {
        $this->authorize('publish', $studyPosting);

        $studyPosting->update(['status' => 'draft']);

        return back()->with('success', "\"{$studyPosting->university_name}\" unpublished — back to draft.");
    }

    public function close(StudyPosting $studyPosting): RedirectResponse
    {
        $this->authorize('publish', $studyPosting);

        $studyPosting->update(['status' => 'closed']);

        return back()->with('success', "\"{$studyPosting->university_name}\" closed to new applications.");
    }

    public function archive(StudyPosting $studyPosting): RedirectResponse
    {
        $this->authorize('publish', $studyPosting);

        $studyPosting->update(['status' => 'archived']);

        return back()->with('success', "\"{$studyPosting->university_name}\" archived.");
    }

    public function toggleFeatured(StudyPosting $studyPosting): RedirectResponse
    {
        $this->authorize('update', $studyPosting);

        $studyPosting->update(['is_featured' => ! $studyPosting->is_featured]);

        return back()->with('success', $studyPosting->is_featured ? "\"{$studyPosting->university_name}\" is now featured." : "\"{$studyPosting->university_name}\" is no longer featured.");
    }

    public function duplicate(StudyPosting $studyPosting): RedirectResponse
    {
        $this->authorize('create', StudyPosting::class);

        $copy = $studyPosting->replicate(['slug']);
        $copy->university_name = $studyPosting->university_name.' (Copy)';
        $copy->status = 'draft';
        $copy->is_featured = false;
        $copy->posted_by = auth()->id();
        $copy->save();

        return redirect()->route('admin.study-postings.edit', $copy)->with('success', 'Duplicated as a new draft — review before publishing.');
    }

    /**
     * "Downloads — this can be fee structure or necessary downloads" — a
     * study posting can have several, so this is add-one-at-a-time rather
     * than a single file field on the main form.
     */
    public function storeDownload(StoreStudyPostingDownloadRequest $request, StudyPosting $studyPosting): RedirectResponse
    {
        $path = $request->file('file')->store('study-posting-downloads', 'public');

        StudyPostingDownload::create([
            'study_posting_id' => $studyPosting->id,
            'title' => $request->string('title'),
            'file_path' => $path,
        ]);

        return back()->with('success', 'Download added.');
    }

    public function destroyDownload(StudyPosting $studyPosting, StudyPostingDownload $download): RedirectResponse
    {
        $this->authorize('update', $studyPosting);
        abort_unless($download->study_posting_id === $studyPosting->id, 404);

        Storage::disk('public')->delete($download->file_path);
        $download->delete();

        return back()->with('success', 'Download removed.');
    }
}
