<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\RespondToWorkerRequestRequest;
use App\Http\Requests\Employer\StoreWorkerRequestRequest;
use App\Models\WorkerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', WorkerRequest::class);

        $requests = $request->user()->workerRequests()
            ->with('jobPosting')
            ->latest()
            ->paginate(10);

        return view('employer.worker-requests.index', ['requests' => $requests]);
    }

    public function create(): View
    {
        $this->authorize('create', WorkerRequest::class);

        return view('employer.worker-requests.create');
    }

    public function store(StoreWorkerRequestRequest $request): RedirectResponse
    {
        $employer = $request->user();

        $workerRequest = WorkerRequest::create([
            'employer_id' => $employer->id,
            'contact_name' => $request->input('contact_name') ?: $employer->name,
            'contact_job_title' => $request->input('contact_job_title') ?: $employer->employerProfile?->contact_job_title,
            'contact_email' => $request->input('contact_email') ?: $employer->email,
            'contact_phone' => $request->input('contact_phone') ?: $employer->phone,
            'job_title' => $request->string('job_title'),
            'quantity' => $request->integer('quantity'),
            'preferred_experience' => $request->input('preferred_experience'),
            'employment_type' => $request->string('employment_type'),
            'age_range' => $request->input('age_range'),
            'preferred_start_date' => $request->input('preferred_start_date'),
            'work_location' => $request->input('work_location'),
            'salary_min' => $request->input('salary_min'),
            'salary_max' => $request->input('salary_max'),
            'currency' => $request->input('currency'),
            'responsibilities' => $request->input('responsibilities'),
            'skills' => $request->input('skills'),
            'benefits' => $request->input('benefits'),
            'requirements' => $request->input('requirements'),
            'additional_requirements' => $request->input('additional_requirements'),
            'status' => 'submitted',
        ]);

        return redirect()
            ->route('employer.worker-requests.index')
            ->with('success', "Your request for {$workerRequest->quantity} \"{$workerRequest->job_title}\" worker(s) has been submitted — Altura will review it shortly.");
    }

    public function show(WorkerRequest $workerRequest): View
    {
        $this->authorize('view', $workerRequest);

        return view('employer.worker-requests.show', ['workerRequest' => $workerRequest]);
    }

    /**
     * The employer's answer to Admin's clarification question. A single
     * response, not a full message thread — moves the request back to
     * 'submitted' so it reappears in Admin's queue for re-review.
     */
    public function respond(RespondToWorkerRequestRequest $request, WorkerRequest $workerRequest): RedirectResponse
    {
        $workerRequest->update([
            'employer_response' => $request->string('employer_response'),
            'employer_responded_at' => now(),
            'status' => 'submitted',
        ]);

        return redirect()
            ->route('employer.worker-requests.show', $workerRequest)
            ->with('success', 'Your response has been sent to Altura for review.');
    }
}
