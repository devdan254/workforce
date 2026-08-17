<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobSeeker\StoreVaultDocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Services\DocumentVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(private DocumentVerificationService $documentService) {}

    public function index(Request $request): View
    {
        $documents = $request->user()->documents()
            ->with(['category', 'jobApplication.jobPosting'])
            ->get()
            ->groupBy(fn ($document) => $document->category->name ?? 'Other Documents');

        $total = $request->user()->documents()->count();
        $completed = $request->user()->documents()->whereIn('status', ['uploaded', 'under_review', 'verified'])->count();

        return view('job-seeker.documents.index', [
            'groupedDocuments' => $documents,
            'total' => $total,
            'completed' => $completed,
            'categories' => DocumentCategory::orderBy('name')->get(),
        ]);
    }

    public function store(StoreVaultDocumentRequest $request): RedirectResponse
    {
        $files = $request->file('files');
        $baseName = $request->string('name');
        $multiple = count($files) > 1;

        foreach ($files as $index => $file) {
            $document = Document::create([
                'student_id' => $request->user()->id,
                'document_category_id' => $request->integer('document_category_id'),
                'name' => $multiple ? "{$baseName} (".($index + 1).')' : (string) $baseName,
                'status' => 'required',
            ]);

            $path = $file->store("documents/{$request->user()->id}", 'public');

            $this->documentService->markUploaded($document, $path, $file->getClientMimeType(), $file->getSize());
        }

        $count = count($files);

        return back()->with('success', $count === 1
            ? "\"{$baseName}\" added — awaiting review."
            : "{$count} documents added under \"{$baseName}\" — awaiting review."
        );
    }

    public function upload(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('upload', $document);

        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png']]);

        $file = $request->file('file');
        $path = $file->store("documents/{$document->student_id}", 'public');

        $this->documentService->markUploaded($document, $path, $file->getClientMimeType(), $file->getSize());

        return back()->with('success', "\"{$document->name}\" uploaded — awaiting review.");
    }
}
