<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\StoreEmployerDocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Services\DocumentVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mirrors JobSeeker\DocumentController's vault pattern exactly — same
 * grouped-by-category index, same multi-file self-add, same upload-against-
 * required-row flow. The one genuine addition: job_posting_id lets Admin
 * or the employer themselves tag a document as relating to a specific job
 * ("Employment Contract for the Nurse role") rather than being a general
 * company document — which is what makes a real Job filter possible here,
 * unlike Student/Job Seeker's vault which never needed one.
 */
class DocumentController extends Controller
{
    public function __construct(private DocumentVerificationService $documentService) {}

    private const STATUS_FILTERS = ['required', 'under_review', 'verified', 'rejected'];

    public function index(Request $request): View
    {
        $employer = $request->user();

        $query = $employer->documents()->with(['category', 'jobPosting']);

        $status = $request->string('status')->value();
        if (in_array($status, self::STATUS_FILTERS, true)) {
            $query->where('status', $status);
        }

        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->integer('job_posting_id'));
        }

        $documents = $query->get()->groupBy(fn ($document) => $document->category->name ?? 'Other Documents');

        $total = $employer->documents()->count();
        $completed = $employer->documents()->whereIn('status', ['under_review', 'verified'])->count();

        return view('employer.documents.index', [
            'groupedDocuments' => $documents,
            'total' => $total,
            'completed' => $completed,
            'categories' => DocumentCategory::orderBy('name')->get(),
            'jobPostings' => $employer->jobPostings()->orderBy('title')->get(),
            'activeStatus' => in_array($status, self::STATUS_FILTERS, true) ? $status : '',
        ]);
    }

    public function store(StoreEmployerDocumentRequest $request): RedirectResponse
    {
        $files = $request->file('files');
        $baseName = $request->string('name');
        $multiple = count($files) > 1;

        foreach ($files as $index => $file) {
            $document = Document::create([
                'student_id' => $request->user()->id,
                'document_category_id' => $request->integer('document_category_id'),
                'job_posting_id' => $request->input('job_posting_id'),
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
