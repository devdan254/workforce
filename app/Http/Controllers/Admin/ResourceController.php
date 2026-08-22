<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreResourceRequest;
use App\Http\Requests\Admin\UpdateResourceRequest;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * The only place resources can be created, edited, or removed — Student,
 * Job Seeker, and Employer portals only ever read from this same table
 * (Student\ResourceController, reused across all three), never write to
 * it. One catalog, one management surface, per audience-targeted like
 * everything else built for multi-portal reuse in this app.
 */
class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Resource::class);

        $query = Resource::query();

        if ($request->filled('audience')) {
            $query->forAudience($request->string('audience'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        $resources = $query->latest()->paginate(15)->withQueryString();

        return view('admin.resources.index', ['resources' => $resources]);
    }

    public function create(): View
    {
        $this->authorize('create', Resource::class);

        return view('admin.resources.create');
    }

    public function store(StoreResourceRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('file');

        $data['audience'] = $request->input('audience', []);
        $data['is_published'] = $request->boolean('is_published');
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('resources', 'public');
        }

        $resource = Resource::create($data);

        return redirect()->route('admin.resources.index')->with('success', "\"{$resource->title}\" created.");
    }

    public function edit(Resource $resource): View
    {
        $this->authorize('update', $resource);

        return view('admin.resources.edit', ['resource' => $resource]);
    }

    public function update(UpdateResourceRequest $request, Resource $resource): RedirectResponse
    {
        $data = $request->safe()->except('file');

        $data['audience'] = $request->input('audience', []);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $data['file_path'] = $request->file('file')->store('resources', 'public');
        }

        $resource->update($data);

        return redirect()->route('admin.resources.index')->with('success', 'Resource updated.');
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $this->authorize('delete', $resource);

        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }
        $resource->delete();

        return back()->with('success', 'Resource deleted.');
    }
}
