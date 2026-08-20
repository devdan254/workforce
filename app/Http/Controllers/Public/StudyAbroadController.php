<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StudyPosting;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Same reasoning as Public\JobsController — real search/filter against the
 * live StudyPosting catalog Admin manages, replacing what was previously a
 * static "Your Path Forward" section with no actual data behind it.
 */
class StudyAbroadController extends Controller
{
    public function index(Request $request): View
    {
        $query = StudyPosting::open();

        if ($request->filled('search')) {
            $query->where('university_name', 'like', '%'.$request->string('search').'%');
        }
        if ($request->filled('country')) {
            $query->where('country', $request->string('country'));
        }
        if ($request->filled('scholarship_type')) {
            $query->where('scholarship_type', $request->string('scholarship_type'));
        }

        $postings = $query->latest()->paginate(9)->withQueryString();

        return view('public.study-abroad.index', [
            'postings' => $postings,
            'countries' => StudyPosting::open()->distinct()->orderBy('country')->pluck('country'),
        ]);
    }

    public function show(StudyPosting $studyPosting): View
    {
        abort_unless($studyPosting->status === 'open', 404);

        $studyPosting->load('downloads');

        $similarPostings = StudyPosting::open()
            ->where('country', $studyPosting->country)
            ->where('id', '!=', $studyPosting->id)
            ->latest()
            ->take(3)
            ->get();

        return view('public.study-abroad.show', [
            'posting' => $studyPosting,
            'similarPostings' => $similarPostings,
        ]);
    }
}
