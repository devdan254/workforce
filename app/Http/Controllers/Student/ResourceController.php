<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        return view('student.resources.index', ['groupedResources' => $resources]);
    }

    public function download(Resource $resource): StreamedResponse
    {
        abort_unless($resource->is_published, 404);
        abort_unless($resource->file_path && Storage::disk('public')->exists($resource->file_path), 404, 'File not available.');

        return Storage::disk('public')->download($resource->file_path, $resource->title);
    }
}
