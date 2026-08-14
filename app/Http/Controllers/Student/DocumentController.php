<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreVaultDocumentRequest;
use App\Http\Requests\Student\UploadDocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Services\DocumentVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(private DocumentVerificationService $documentService) {}

    /**
     * The Document Vault — EVERY document across ALL of the student's
     * applications, grouped by category, regardless of which application
     * (or none) it's tied to. Same underlying `documents` table as the
     * per-application view inside Application Details — no duplicate data,
     * just a different slice of the same source of truth.
     */
    public function index(Request $request): View
    {
        $documents = $request->user()->documents()
            ->with(['category', 'studyApplication.university'])
            ->get()
            ->groupBy(fn ($document) => $document->category->name ?? 'Other Documents');

        $total = $request->user()->documents()->count();
        $completed = $request->user()->documents()->whereIn('status', ['uploaded', 'under_review', 'verified'])->count();

        return view('student.documents.index', [
            'groupedDocuments' => $documents,
            'total' => $total,
            'completed' => $completed,
            'categories' => DocumentCategory::orderBy('name')->get(),
        ]);
    }

    /**
     * Student proactively ADDING a document — not uploading against a
     * pre-created "required" row (that's upload() below), but creating the
     * row themselves: pick a category, name it, attach one or more files.
     * Each file becomes its own Document — no cap on total vault size,
     * only a per-submission cap (see StoreVaultDocumentRequest) so one
     * request can't be abused to upload hundreds of files at once.
     */
    public function store(StoreVaultDocumentRequest $request): RedirectResponse
    {
        $files = $request->file('files');
        $baseName = $request->string('name');
        $multiple = count($files) > 1;

        foreach ($files as $index => $file) {
            $document = Document::create([
                'student_id' => $request->user()->id,
                'study_application_id' => null,
                'document_category_id' => $request->integer('document_category_id'),
                'name' => $multiple ? "{$baseName} (".($index + 1).')' : (string) $baseName,
                'status' => 'required',
            ]);

            $path = $file->store("documents/{$request->user()->id}", 'public');

            $this->documentService->markUploaded(
                $document,
                $path,
                $file->getClientMimeType(),
                $file->getSize(),
            );
        }

        $count = count($files);

        return back()->with('success', $count === 1
            ? "\"{$baseName}\" added — awaiting review."
            : "{$count} documents added under \"{$baseName}\" — awaiting review."
        );
    }

    /**
     * Handles both first upload AND "replace rejected document" —
     * same action, DocumentVerificationService::markUploaded() resets
     * status to under_review and clears any prior rejection either way.
     */
    public function upload(UploadDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('upload', $document);

        $path = $request->file('file')->store("documents/{$document->student_id}", 'public');

        $this->documentService->markUploaded(
            $document,
            $path,
            $request->file('file')->getClientMimeType(),
            $request->file('file')->getSize(),
        );

        return back()->with('success', "\"{$document->name}\" uploaded — awaiting review.");
    }

    public function download(Document $document)
    {
        $this->authorize('view', $document);

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not available.');
        }

        return Storage::disk('public')->download($document->file_path, $document->name);
    }
}
