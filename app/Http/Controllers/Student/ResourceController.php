<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Resource has no owner column at all — it's a global catalog, not
 * per-student data. Reused directly by Job Seeker and Employer routes
 * (see routes/web.php), with only the view name role-aware, since each
 * portal sees different resource content (study-abroad guides vs.
 * job-search guides vs. employer/hiring guides) even though the
 * underlying query/download logic is identical.
 */
class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        $resources = Resource::published()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->orderBy('category')
            ->orderBy('title')
            ->get()
            ->groupBy('category');

        $view = match (true) {
            $request->user()->isJobSeeker() => 'job-seeker.resources.index',
            $request->user()->isEmployer() => 'employer.resources.index',
            default => 'student.resources.index',
        };

        return view($view, ['groupedResources' => $resources]);
    }

    public function download(Resource $resource): StreamedResponse
    {
        abort_unless($resource->is_published, 404);
        abort_unless($resource->file_path && Storage::disk('public')->exists($resource->file_path), 404, 'File not available.');

        return Storage::disk('public')->download($resource->file_path, $resource->title);
    }
}
