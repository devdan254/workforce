<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudyApplicationRequest;
use App\Models\Course;
use App\Models\Status;
use App\Models\StudyApplication;
use App\Models\University;
use App\Notifications\AdminAlertNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * "My Applications" — spec is explicit: a student can have MULTIPLE
     * applications, so this is always a list, never assumed to be singular.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StudyApplication::class);

        $query = $request->user()->studyApplications()
            ->with(['university', 'course', 'currentStatus']);

        // Filters: Country, University, Course, Application Status, Application Date
        if ($request->filled('country')) {
            $query->whereHas('university', fn ($q) => $q->where('country', $request->string('country')));
        }
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->integer('university_id'));
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->integer('status_id'));
        }
        if ($request->filled('from_date')) {
            $query->whereDate('submitted_at', '>=', $request->date('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('submitted_at', '<=', $request->date('to_date'));
        }

        $applications = $query->latest('submitted_at')->paginate(10)->withQueryString();

        return view('student.applications.index', [
            'applications' => $applications,
            'countries' => University::query()->distinct()->orderBy('country')->pluck('country'),
            'universities' => University::where('is_active', true)->orderBy('name')->get(),
            'courses' => Course::where('is_active', true)->orderBy('name')->get(),
            'applicationStatuses' => Status::where('type', 'application')->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', StudyApplication::class);

        return view('student.applications.create', [
            'universities' => University::with(['courses' => fn ($q) => $q->where('is_active', true)])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreStudyApplicationRequest $request): RedirectResponse
    {
        // Upsert onto the student's profile FIRST — this is the single source
        // of truth for these fields; the application itself never stores a copy.
        $request->user()->update(['phone' => $request->string('phone')]);
        $request->user()->studentProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'country' => $request->string('country'),
                'highest_qualification' => $request->string('highest_qualification'),
            ]
        );

        $startStatus = Status::where('type', 'application')->where('slug', 'application_started')->firstOrFail();
        $course = Course::findOrFail($request->integer('course_id'));

        $application = StudyApplication::create([
            'student_id' => $request->user()->id,
            'university_id' => $request->integer('university_id'),
            'course_id' => $course->id,
            'status_id' => $startStatus->id,
            'intake' => $request->string('intake'),
            'tuition_fee' => $course->tuition_fee,
            'currency' => 'KES', // Altura's own fees are invoiced in KES regardless of the course's tuition currency
            'submitted_at' => now(),
        ]);

        AdminAlertNotification::sendToAdmins(
            heading: 'New Study Abroad Application Received',
            lines: [
                'Student' => $request->user()->name,
                'Email' => $request->user()->email,
                'University' => $application->university->name,
                'Course' => $course->name,
                'Reference' => $application->reference_number ?? "#{$application->id}",
            ],
            actionLabel: 'Review Application',
            actionUrl: route('admin.students.show', $request->user()->id),
        );

        return redirect()
            ->route('student.applications.show', $application)
            ->with('success', 'Your application has been started. Our team will review it shortly.');
    }

    /**
     * The "Application Details workspace" — everything about ONE application,
     * financial info scoped to THIS application only (spec is explicit: never
     * mix unrelated applications' financials together).
     */
    public function show(StudyApplication $application): View
    {
        $this->authorize('view', $application);

        $application->load([
            'university', 'course', 'currentStatus', 'assignedOfficer',
            'statusHistories.toStatus', 'statusHistories.fromStatus',
            'documents.category', 'admission', 'visaApplication.currentStatus',
            'invoices.items', 'invoices.payments',
        ]);

        $financials = [
            'application_fee' => (float) $application->application_fee,
            'tuition_fee' => (float) $application->tuition_fee,
            'service_fee' => (float) $application->service_fee,
            'amount_paid' => (float) $application->invoices->sum('amount_paid'),
            'total' => (float) $application->invoices->sum('total'),
        ];
        $financials['balance'] = $financials['total'] - $financials['amount_paid'];

        $nextStatuses = app(\App\Services\ApplicationStatusService::class)->allowedNextStatuses($application);

        return view('student.applications.show', [
            'application' => $application,
            'financials' => $financials,
            'nextStatuses' => $nextStatuses,
        ]);
    }
}
