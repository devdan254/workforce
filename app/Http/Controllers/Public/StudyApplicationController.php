<?php

namespace App\Http\Controllers\Public;

use App\Mail\AdminNotificationMail;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudyApplication;
use App\Models\StudyPosting;
use App\Models\University;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * A genuine architectural seam, not a design choice: the authenticated
 * Student Portal's application flow (Student\ApplicationController,
 * built in Stage 1) is keyed off University + Course — an older, separate
 * catalog from StudyPosting (built this session for the public marketing
 * site). The two were never unified. Rather than retrofit the existing,
 * working Student Portal flow to understand StudyPosting — risking
 * something that already works — this controller bridges the gap: it
 * looks up (or creates) a matching University + Course record for
 * whichever StudyPosting someone applied from, then creates the
 * StudyApplication using the exact same shape Student\ApplicationController
 * already produces. Some field-mapping logic is necessarily duplicated
 * here rather than reused, since that existing controller is a plain
 * single-request form (not a resumable session wizard the way the Job
 * application flow was) — there's no clean mid-flow handoff point to
 * redirect into the way there was for Jobs.
 */
class StudyApplicationController extends Controller
{
    private const DESTINATIONS = ['Germany', 'Australia', 'England', 'South Africa', 'USIU – Kenya', 'China'];

    public function create(Request $request, ?StudyPosting $studyPosting = null): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = $request->user();

            if (! $user->isStudent()) {
                return view('public.study-application.wrong-account-type', ['accountType' => match (true) {
                    $user->isJobSeeker() => 'Job Seeker',
                    $user->isEmployer() => 'Employer',
                    default => 'Staff',
                }]);
            }
        }

        return view('public.study-application.create', [
            'posting' => $studyPosting,
            'destinations' => self::DESTINATIONS,
        ]);
    }

    public function store(Request $request, ?StudyPosting $studyPosting = null): RedirectResponse
    {
        $isGuest = ! Auth::check();

        $rules = [
            'country' => ['required', 'string', 'max:100'],
            'highest_qualification' => ['required', 'string', 'max:100'],
            'destination' => ['required', 'string', 'max:100'],
            'course' => ['required', 'string', 'max:150'],
            'intake' => ['nullable', 'string', 'max:100'],
            'declaration' => ['accepted'],
        ];

        if ($isGuest) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255'];
            $rules['phone'] = ['required', 'string', 'max:30'];
            $rules['password'] = ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()];
        } else {
            $rules['phone'] = ['required', 'string', 'max:30'];
        }

        $request->validate($rules);

        if ($isGuest) {
            // Same "never silently attach to someone else's account" rule
            // as the Job Application flow — an existing email means log in,
            // not a fresh registration. Unlike Jobs' resumable session
            // wizard, Student\ApplicationController is a plain form with no
            // state to resume — after login they land on their dashboard's
            // application page, but do need to re-enter these specific
            // answers (course/destination/etc). Honest limitation, not
            // something a redirect alone can fix.
            if (User::where('email', $request->input('email'))->exists()) {
                session(['url.intended' => route('student.applications.create')]);

                return redirect()->route('login')->withErrors([
                    'email' => 'An account already exists with this email. Please log in, then apply from your dashboard.',
                ])->withInput($request->except('password', 'password_confirmation'));
            }

            $user = User::create([
                'name' => $request->string('name'),
                'email' => $request->string('email'),
                'phone' => $request->string('phone'),
                'password' => Hash::make($request->string('password')),
                'is_active' => true,
            ]);
            $user->assignRole('student');
            event(new Registered($user));
            Auth::login($user);
        } else {
            $user = $request->user();
            $user->update(['phone' => $request->string('phone')]);
        }

        $user->studentProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'country' => $request->string('country'),
                'highest_qualification' => $request->string('highest_qualification'),
            ]
        );

        $application = DB::transaction(function () use ($request, $studyPosting) {
            // Bridge: find-or-create the University/Course this StudyPosting
            // (or, if none, the chosen destination) maps onto. is_active
            // false-by-default would hide these from the authenticated
            // portal's own "Browse Universities" dropdown, which isn't the
            // intent — a real applicant just applied here, so it's real.
            $university = University::firstOrCreate(
                [
                    'name' => $studyPosting?->university_name ?? $request->string('destination'),
                    'country' => $studyPosting?->country ?? $request->string('destination'),
                ],
                ['is_active' => true]
            );

            $course = Course::firstOrCreate(
                ['university_id' => $university->id, 'name' => $request->string('course')],
                [
                    'tuition_fee' => $studyPosting?->school_fees_per_semester ?? 0,
                    'currency' => $studyPosting?->fees_currency ?? 'USD',
                    'is_active' => true,
                ]
            );

            $startStatus = \App\Models\Status::where('type', 'application')->where('slug', 'application_started')->firstOrFail();

            return StudyApplication::create([
                'student_id' => request()->user()->id,
                'university_id' => $university->id,
                'course_id' => $course->id,
                'status_id' => $startStatus->id,
                'intake' => $request->input('intake'),
                'tuition_fee' => $course->tuition_fee,
                'currency' => 'KES',
                'submitted_at' => now(),
            ]);
        });

        Mail::to(config('notifications.admin_email'))->send(new AdminNotificationMail(
            heading: 'New Study Abroad Application Received',
            lines: [
                'Applicant' => $user->name,
                'Email' => $user->email,
                'University' => $studyPosting?->university_name ?? $request->string('destination'),
                'Course' => $request->string('course'),
                'Destination' => $request->string('destination'),
            ],
            actionLabel: 'Review Application',
            actionUrl: route('admin.students.show', $user->id),
        ));

        return redirect()->route('student.applications.show', $application)
            ->with('success', 'Your application has been started. Our education advisors will review it shortly.');
    }
}
