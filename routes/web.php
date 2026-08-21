<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobPostingController as AdminJobPostingController;
use App\Http\Controllers\Admin\StudyPostingController as AdminStudyPostingController;
use App\Http\Controllers\Admin\WorkerRequestController as AdminWorkerRequestController;
use App\Http\Controllers\Admin\OnboardingController as AdminOnboardingController;
use App\Http\Controllers\Admin\Student\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\Student\EngagementController as AdminEngagementController;
use App\Http\Controllers\Admin\Student\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\JobSeeker\ApplicationController as AdminJobSeekerApplicationController;
use App\Http\Controllers\Admin\JobSeeker\EngagementController as AdminJobSeekerEngagementController;
use App\Http\Controllers\Admin\Employer\FinanceController as AdminEmployerFinanceController;
use App\Http\Controllers\Admin\JobSeeker\FinanceController as AdminJobSeekerFinanceController;
use App\Http\Controllers\Admin\EmployerController as AdminEmployerController;
use App\Http\Controllers\Admin\EmployerWorkspaceController;
use App\Http\Controllers\Admin\JobSeekerController as AdminJobSeekerController;
use App\Http\Controllers\Admin\JobSeekerWorkspaceController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\StudentWorkspaceController;
use App\Http\Controllers\JobSeeker\ApplicationController as JobSeekerApplicationController;
use App\Http\Controllers\JobSeeker\ApplicationWizardController;
use App\Http\Controllers\JobSeeker\AppointmentController as JobSeekerAppointmentController;
use App\Http\Controllers\JobSeeker\DashboardController as JobSeekerDashboardController;
use App\Http\Controllers\JobSeeker\DocumentController as JobSeekerDocumentController;
use App\Http\Controllers\JobSeeker\InvoiceController as JobSeekerInvoiceController;
use App\Http\Controllers\JobSeeker\JobController as JobSeekerJobController;
use App\Http\Controllers\JobSeeker\OfferController as JobSeekerOfferController;
use App\Http\Controllers\JobSeeker\PaymentController as JobSeekerPaymentController;
use App\Http\Controllers\JobSeeker\ProfileController as JobSeekerProfileController;
use App\Http\Controllers\JobSeeker\SupportTicketController as JobSeekerSupportTicketController;
use App\Http\Controllers\JobSeeker\VisaController as JobSeekerVisaController;
use App\Http\Controllers\Employer\CandidateController as EmployerCandidateController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Employer\DocumentController as EmployerDocumentController;
use App\Http\Controllers\Employer\InvoiceController as EmployerInvoiceController;
use App\Http\Controllers\Employer\AppointmentController as EmployerAppointmentController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\Employer\PaymentController as EmployerPaymentController;
use App\Http\Controllers\Employer\OfferController as EmployerOfferController;
use App\Http\Controllers\Employer\WorkerController as EmployerWorkerController;
use App\Http\Controllers\Employer\InterviewController as EmployerInterviewController;
use App\Http\Controllers\Employer\ProfileController as EmployerProfileController;
use App\Http\Controllers\Employer\SupportTicketController as EmployerSupportTicketController;
use App\Http\Controllers\Employer\WorkerRequestController as EmployerWorkerRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\HireController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\InquiryController;
use App\Http\Controllers\Public\JobApplicationController;
use App\Http\Controllers\Public\StudyAbroadController;
use App\Http\Controllers\Public\StudyApplicationController;
use App\Http\Controllers\Public\VisaApplicationController;
use App\Http\Controllers\Public\VisaController as PublicVisaController;
use App\Http\Controllers\Public\JobsController;
use App\Http\Controllers\Student\ApplicationController;
use App\Http\Controllers\Student\AppointmentController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\DocumentController;
use App\Http\Controllers\Student\InvoiceController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\ResourceController;
use App\Http\Controllers\Student\SupportTicketController;
use App\Http\Controllers\Student\VisaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public marketing site
|--------------------------------------------------------------------------
| No auth middleware — this is the actual public-facing site, and / is now
| the real home page (previously redirected straight to /login, meaning
| there was no public site at all). Home is fully wired (Featured
| Opportunities reads live JobPosting/StudyPosting data). Everything else
| is a placeholder route so the shared layout's navigation never 404s,
| converted one page at a time going forward — same incremental approach
| used throughout this entire build.
*/
Route::name('public.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/jobs', [JobsController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{jobPosting}', [JobsController::class, 'show'])->name('jobs.show');

    // The general "no specific posting" application must be a genuinely
    // distinct, literal path registered BEFORE the show() wildcard below —
    // an optional {studyPosting?} segment here would collapse to
    // /study-abroad/apply when omitted, which is indistinguishable from
    // /study-abroad/{studyPosting} (2 segments either way) and would have
    // been swallowed by show()'s route-model-binding first.
    Route::get('/study-abroad/apply', [StudyApplicationController::class, 'create'])->name('study-application.create.general');
    Route::post('/study-abroad/apply', [StudyApplicationController::class, 'store'])->name('study-application.store.general');

    Route::get('/study-abroad', [StudyAbroadController::class, 'index'])->name('study-abroad.index');
    Route::get('/study-abroad/{studyPosting}', [StudyAbroadController::class, 'show'])->name('study-abroad.show');
    Route::get('/study-abroad/{studyPosting}/apply', [StudyApplicationController::class, 'create'])->name('study-application.create');
    Route::post('/study-abroad/{studyPosting}/apply', [StudyApplicationController::class, 'store'])->name('study-application.store');

    Route::get('/visa', [PublicVisaController::class, 'index'])->name('visa');
    Route::get('/hire', [HireController::class, 'create'])->name('hire');
    Route::post('/hire', [HireController::class, 'store'])->name('hire.store');
    Route::get('/about', fn () => view('public.about'))->name('about');
    Route::get('/contact', fn () => view('public.contact'))->name('contact');
    Route::post('/inquiries/consultation', [InquiryController::class, 'storeConsultation'])->name('inquiries.consultation');
    Route::post('/inquiries/contact', [InquiryController::class, 'storeContact'])->name('inquiries.contact');
    Route::post('/inquiries/visa-consultation', [InquiryController::class, 'storeVisaConsultation'])->name('inquiries.visa-consultation');

    Route::get('/apply/job', [JobApplicationController::class, 'create'])->name('job-application-form');
    Route::post('/apply/job', [JobApplicationController::class, 'store'])->name('job-application-form.store');
    Route::get('/apply/job/talent-pool-profile', [JobApplicationController::class, 'talentPoolProfile'])->name('job-application-form.talent-pool');
    Route::post('/apply/job/talent-pool-profile', [JobApplicationController::class, 'storeTalentPoolProfile'])->name('job-application-form.talent-pool.store');
    Route::get('/apply/visa', [VisaApplicationController::class, 'create'])->name('visa-application-form');
    Route::post('/apply/visa', [VisaApplicationController::class, 'store'])->name('visa-application-form.store');
});

// Breeze's default profile routes (published into your project already) —
// keep these for password/account changes. Our own richer student profile
// (personal/academic/passport/preferences) lives at student.profile.* below.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Student Portal
|--------------------------------------------------------------------------
| Every route here also gets a Policy check inside its controller —
| this middleware group is the coarse "are you even a student" gate,
| not the full authorization story (see StudyApplicationPolicy etc.).
*/
Route::middleware(['auth', 'verified', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
        Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
        Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
        Route::get('/applications/{application}/visa', [VisaController::class, 'show'])->name('applications.visa');

        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::post('/documents/{document}/upload', [DocumentController::class, 'upload'])->name('documents.upload');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.download');
        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

        Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
        Route::get('/support/create', [SupportTicketController::class, 'create'])->name('support.create');
        Route::post('/support', [SupportTicketController::class, 'store'])->name('support.store');
        Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
        Route::post('/support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');

        Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');

        Route::get('/profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [StudentProfileController::class, 'update'])->name('profile.update');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read_all');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    });

/*
|--------------------------------------------------------------------------
| Job Seeker Portal (Stage 2)
|--------------------------------------------------------------------------
| Only Dashboard is real this step — the rest are TODO placeholders so the
| layout's sidebar links resolve without error, matching exactly how the
| Student Portal routes were built incrementally (see routes.index.* pattern
| history). Each placeholder gets replaced with a real controller action in
| its own dedicated step, per the spec's build order.
*/
Route::middleware(['auth', 'verified', 'role:job_seeker'])
    ->prefix('job-seeker')
    ->name('job-seeker.')
    ->group(function () {
        Route::get('/dashboard', [JobSeekerDashboardController::class, 'index'])->name('dashboard');

        Route::get('/jobs', [JobSeekerJobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{jobPosting}', [JobSeekerJobController::class, 'show'])->name('jobs.show');

        Route::get('/jobs/{jobPosting}/apply', [ApplicationWizardController::class, 'start'])->name('jobs.apply');
        Route::get('/jobs/{jobPosting}/apply/step/{step}', [ApplicationWizardController::class, 'showStep'])->name('jobs.apply.step');
        Route::post('/jobs/{jobPosting}/apply/step/{step}', [ApplicationWizardController::class, 'saveStep'])->name('jobs.apply.save');
        Route::post('/jobs/{jobPosting}/apply/parse-cv', [ApplicationWizardController::class, 'parseCv'])->name('jobs.apply.parse_cv');
        Route::post('/jobs/{jobPosting}/apply/upload/{slot}', [ApplicationWizardController::class, 'uploadDocument'])->name('jobs.apply.upload');
        Route::post('/jobs/{jobPosting}/apply/submit', [ApplicationWizardController::class, 'submit'])->name('jobs.apply.submit');

        Route::get('/applications', [JobSeekerApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [JobSeekerApplicationController::class, 'show'])->name('applications.show');
        Route::post('/applications/{application}/documents/{document}/upload', [JobSeekerApplicationController::class, 'uploadDocument'])->name('applications.documents.upload');
        Route::get('/applications/{application}/visa', [JobSeekerVisaController::class, 'show'])->name('applications.visa');

        Route::get('/documents', [JobSeekerDocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [JobSeekerDocumentController::class, 'store'])->name('documents.store');
        Route::post('/documents/{document}/upload', [JobSeekerDocumentController::class, 'upload'])->name('documents.upload');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

        Route::get('/payments', [JobSeekerPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}/receipt', [JobSeekerPaymentController::class, 'receipt'])->name('payments.receipt');

        Route::get('/invoices', [JobSeekerInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [JobSeekerInvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [JobSeekerInvoiceController::class, 'downloadPdf'])->name('invoices.download');
        Route::post('/invoices/{invoice}/payments', [JobSeekerPaymentController::class, 'store'])->name('invoices.payments.store');

        Route::get('/appointments', [JobSeekerAppointmentController::class, 'index'])->name('appointments.index');
        Route::patch('/appointments/{appointment}', [JobSeekerAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::post('/appointments/{appointment}/cancel', [JobSeekerAppointmentController::class, 'cancel'])->name('appointments.cancel');

        Route::get('/offers', [JobSeekerOfferController::class, 'index'])->name('offers.index');
        Route::get('/offers/{offer}', [JobSeekerOfferController::class, 'show'])->name('offers.show');
        Route::post('/offers/{offer}/accept', [JobSeekerOfferController::class, 'accept'])->name('offers.accept');
        Route::post('/offers/{offer}/decline', [JobSeekerOfferController::class, 'decline'])->name('offers.decline');
        Route::post('/offers/{offer}/clarify', [JobSeekerOfferController::class, 'requestClarification'])->name('offers.clarify');
        Route::get('/offers/{offer}/download-letter', [JobSeekerOfferController::class, 'downloadLetter'])->name('offers.download_letter');

        Route::get('/support', [JobSeekerSupportTicketController::class, 'index'])->name('support.index');
        Route::get('/support/create', [JobSeekerSupportTicketController::class, 'create'])->name('support.create');
        Route::post('/support', [JobSeekerSupportTicketController::class, 'store'])->name('support.store');
        Route::get('/support/{ticket}', [JobSeekerSupportTicketController::class, 'show'])->name('support.show');
        Route::post('/support/{ticket}/reply', [JobSeekerSupportTicketController::class, 'reply'])->name('support.reply');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read_all');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

        Route::get('/profile', [JobSeekerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [JobSeekerProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/experience', [JobSeekerProfileController::class, 'storeExperience'])->name('profile.experience.store');
        Route::delete('/profile/experience/{experience}', [JobSeekerProfileController::class, 'destroyExperience'])->name('profile.experience.destroy');
        Route::post('/profile/education', [JobSeekerProfileController::class, 'storeEducation'])->name('profile.education.store');
        Route::delete('/profile/education/{education}', [JobSeekerProfileController::class, 'destroyEducation'])->name('profile.education.destroy');

        Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    });

/*
|--------------------------------------------------------------------------
| Employer Portal (Stage 3)
|--------------------------------------------------------------------------
| Only Dashboard and Company Profile are real this delivery — the rest are
| TODO placeholders so the layout's sidebar links resolve without error,
| matching exactly the same incremental pattern used throughout Stage 2.
| Each placeholder gets replaced with a real controller action in its own
| dedicated delivery, per the spec's build order (Worker Requests next).
*/
Route::middleware(['auth', 'verified', 'role:employer'])
    ->prefix('employer')
    ->name('employer.')
    ->group(function () {
        Route::get('/dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [EmployerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [EmployerProfileController::class, 'update'])->name('profile.update');

        Route::get('/worker-requests', [EmployerWorkerRequestController::class, 'index'])->name('worker-requests.index');
        Route::get('/worker-requests/create', [EmployerWorkerRequestController::class, 'create'])->name('worker-requests.create');
        Route::post('/worker-requests', [EmployerWorkerRequestController::class, 'store'])->name('worker-requests.store');
        Route::get('/worker-requests/{workerRequest}', [EmployerWorkerRequestController::class, 'show'])->name('worker-requests.show');
        Route::post('/worker-requests/{workerRequest}/respond', [EmployerWorkerRequestController::class, 'respond'])->name('worker-requests.respond');

        Route::get('/jobs', [EmployerJobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{jobPosting}', [EmployerJobController::class, 'show'])->name('jobs.show');
        Route::get('/candidates', [EmployerCandidateController::class, 'index'])->name('candidates.index');
        Route::get('/candidates/{application}', [EmployerCandidateController::class, 'show'])->name('candidates.show');
        Route::get('/candidate-documents/{document}/download', [DocumentController::class, 'download'])->name('candidate-documents.download');
        Route::get('/interviews', [EmployerInterviewController::class, 'index'])->name('interviews.index');
        Route::get('/offers', [EmployerOfferController::class, 'index'])->name('offers.index');
        Route::get('/offers/{offer}', [EmployerOfferController::class, 'show'])->name('offers.show');
        Route::get('/workers', [EmployerWorkerController::class, 'index'])->name('workers.index');
        Route::get('/documents', [EmployerDocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [EmployerDocumentController::class, 'store'])->name('documents.store');
        Route::post('/documents/{document}/upload', [EmployerDocumentController::class, 'upload'])->name('documents.upload');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::get('/payments', [EmployerPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}/receipt', [EmployerPaymentController::class, 'receipt'])->name('payments.receipt');

        Route::get('/invoices', [EmployerInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [EmployerInvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [EmployerInvoiceController::class, 'downloadPdf'])->name('invoices.download');
        Route::post('/invoices/{invoice}/payments', [EmployerPaymentController::class, 'store'])->name('invoices.payments.store');
        Route::get('/appointments', [EmployerAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/support', [EmployerSupportTicketController::class, 'index'])->name('support.index');
        Route::get('/support/create', [EmployerSupportTicketController::class, 'create'])->name('support.create');
        Route::post('/support', [EmployerSupportTicketController::class, 'store'])->name('support.store');
        Route::get('/support/{ticket}', [EmployerSupportTicketController::class, 'show'])->name('support.show');
        Route::post('/support/{ticket}/reply', [EmployerSupportTicketController::class, 'reply'])->name('support.reply');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read_all');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

        Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    });

/*
|--------------------------------------------------------------------------
| Admin Portal (all staff roles)
|--------------------------------------------------------------------------
| Route-level role check is the coarse gate ("are you staff at all").
| Fine-grained permission checks (students.view, applications.change_status,
| payments.confirm, etc.) happen inside controllers via Policies. Never rely
| on this group alone to protect a sensitive action.
*/
Route::middleware([
    'auth', 'verified',
    'role:super_admin|admin_officer|education_officer|finance_officer|visa_officer|support_officer|sales_officer',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/onboarding', [AdminOnboardingController::class, 'index'])->name('onboarding.index');

        Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [AdminStudentController::class, 'create'])->name('students.create');
        Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('students.edit');
        Route::patch('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
        Route::post('/students/{student}/suspend', [AdminStudentController::class, 'suspend'])->name('students.suspend');

        // The unified Student workspace.
        Route::get('/students/{student}', [StudentWorkspaceController::class, 'show'])->name('students.show');
        Route::patch('/students/{student}/profile', [StudentWorkspaceController::class, 'updateProfile'])->name('students.profile.update');

        /*
        |----------------------------------------------------------------
        | Admin Job Seeker Management (Stage 2, Phase B)
        |----------------------------------------------------------------
        | List/Create/Edit/Suspend is real. The 360° Workspace (job-seekers.show)
        | is a TODO placeholder for now — same incremental pattern used
        | throughout this build — filled in as its own dedicated delivery,
        | mirroring how the Student Workspace was the largest single piece
        | of Admin Student Management too.
        */
        Route::get('/job-seekers', [AdminJobSeekerController::class, 'index'])->name('job-seekers.index');
        Route::get('/job-seekers/create', [AdminJobSeekerController::class, 'create'])->name('job-seekers.create');
        Route::post('/job-seekers', [AdminJobSeekerController::class, 'store'])->name('job-seekers.store');
        Route::get('/job-seekers/{jobSeeker}/edit', [AdminJobSeekerController::class, 'edit'])->name('job-seekers.edit');
        Route::patch('/job-seekers/{jobSeeker}', [AdminJobSeekerController::class, 'update'])->name('job-seekers.update');
        Route::post('/job-seekers/{jobSeeker}/suspend', [AdminJobSeekerController::class, 'suspend'])->name('job-seekers.suspend');

        // The unified Job Seeker 360° workspace.
        Route::get('/job-seekers/{jobSeeker}', [JobSeekerWorkspaceController::class, 'show'])->name('job-seekers.show');
        Route::patch('/job-seekers/{jobSeeker}/profile', [JobSeekerWorkspaceController::class, 'updateProfile'])->name('job-seekers.profile.update');
        Route::post('/job-seekers/{jobSeeker}/experience', [JobSeekerWorkspaceController::class, 'storeExperience'])->name('job-seekers.experience.store');
        Route::delete('/job-seekers/{jobSeeker}/experience/{experience}', [JobSeekerWorkspaceController::class, 'destroyExperience'])->name('job-seekers.experience.destroy');
        Route::post('/job-seekers/{jobSeeker}/education', [JobSeekerWorkspaceController::class, 'storeEducation'])->name('job-seekers.education.store');
        Route::delete('/job-seekers/{jobSeeker}/education/{education}', [JobSeekerWorkspaceController::class, 'destroyEducation'])->name('job-seekers.education.destroy');

        Route::post('/job-seekers/{jobSeeker}/documents/{document}/verify', [JobSeekerWorkspaceController::class, 'verifyDocument'])->name('job-seekers.documents.verify');
        Route::post('/job-seekers/{jobSeeker}/documents/{document}/reject', [JobSeekerWorkspaceController::class, 'rejectDocument'])->name('job-seekers.documents.reject');
        Route::post('/job-seekers/{jobSeeker}/documents/request', [JobSeekerWorkspaceController::class, 'requestDocument'])->name('job-seekers.documents.request');
        Route::post('/job-seekers/{jobSeeker}/documents/{document}/upload', [JobSeekerWorkspaceController::class, 'uploadDocument'])->name('job-seekers.documents.upload');
        Route::patch('/job-seekers/{jobSeeker}/documents/{document}', [JobSeekerWorkspaceController::class, 'updateDocument'])->name('job-seekers.documents.update');
        Route::delete('/job-seekers/{jobSeeker}/documents/{document}', [JobSeekerWorkspaceController::class, 'deleteDocument'])->name('job-seekers.documents.destroy');
        Route::post('/job-seekers/{jobSeeker}/payments/{payment}/confirm', [JobSeekerWorkspaceController::class, 'confirmPayment'])->name('job-seekers.payments.confirm');

        Route::post('/job-seekers/{jobSeeker}/invoices/{invoice}/payments', [AdminJobSeekerFinanceController::class, 'storePayment'])->name('job-seekers.invoices.payments.store');
        Route::patch('/job-seekers/{jobSeeker}/payments/{payment}', [AdminJobSeekerFinanceController::class, 'updatePayment'])->name('job-seekers.payments.update');
        Route::post('/job-seekers/{jobSeeker}/payments/{payment}/refund', [AdminJobSeekerFinanceController::class, 'refundPayment'])->name('job-seekers.payments.refund');
        Route::post('/job-seekers/{jobSeeker}/invoices', [AdminJobSeekerFinanceController::class, 'storeInvoice'])->name('job-seekers.invoices.store');
        Route::post('/job-seekers/{jobSeeker}/invoices/{invoice}/send', [AdminJobSeekerFinanceController::class, 'sendInvoice'])->name('job-seekers.invoices.send');
        Route::post('/job-seekers/{jobSeeker}/invoices/{invoice}/cancel', [AdminJobSeekerFinanceController::class, 'cancelInvoice'])->name('job-seekers.invoices.cancel');

        // Applications (+ Interview/Offer/Visa) — Admin\JobSeeker\ApplicationController
        Route::post('/job-seekers/{jobSeeker}/applications', [AdminJobSeekerApplicationController::class, 'store'])->name('job-seekers.applications.store');
        Route::delete('/job-seekers/{jobSeeker}/applications/{application}', [AdminJobSeekerApplicationController::class, 'destroy'])->name('job-seekers.applications.destroy');
        Route::post('/job-seekers/{jobSeeker}/applications/{application}/status', [AdminJobSeekerApplicationController::class, 'changeStatus'])->name('job-seekers.applications.status');
        Route::post('/job-seekers/{jobSeeker}/applications/{application}/assign-officer', [AdminJobSeekerApplicationController::class, 'assignOfficer'])->name('job-seekers.applications.assign_officer');
        Route::post('/job-seekers/{jobSeeker}/applications/{application}/interview', [AdminJobSeekerApplicationController::class, 'scheduleInterview'])->name('job-seekers.applications.interview');
        Route::post('/job-seekers/{jobSeeker}/applications/{application}/offer', [AdminJobSeekerApplicationController::class, 'storeOrUpdateOffer'])->name('job-seekers.applications.offer');
        Route::post('/job-seekers/{jobSeeker}/applications/{application}/offer/send', [AdminJobSeekerApplicationController::class, 'sendOffer'])->name('job-seekers.applications.offer.send');
        Route::post('/job-seekers/{jobSeeker}/applications/{application}/visa', [AdminJobSeekerApplicationController::class, 'updateVisa'])->name('job-seekers.applications.visa');

        // Appointments / Messages / Tasks / Notes — Admin\JobSeeker\EngagementController
        Route::post('/job-seekers/{jobSeeker}/appointments', [AdminJobSeekerEngagementController::class, 'storeAppointment'])->name('job-seekers.appointments.store');
        Route::patch('/job-seekers/{jobSeeker}/appointments/{appointment}', [AdminJobSeekerEngagementController::class, 'updateAppointment'])->name('job-seekers.appointments.update');
        Route::post('/job-seekers/{jobSeeker}/appointments/{appointment}/confirm', [AdminJobSeekerEngagementController::class, 'confirmAppointment'])->name('job-seekers.appointments.confirm');
        Route::post('/job-seekers/{jobSeeker}/appointments/{appointment}/cancel', [AdminJobSeekerEngagementController::class, 'cancelAppointment'])->name('job-seekers.appointments.cancel');
        Route::post('/job-seekers/{jobSeeker}/appointments/{appointment}/complete', [AdminJobSeekerEngagementController::class, 'completeAppointment'])->name('job-seekers.appointments.complete');

        Route::post('/job-seekers/{jobSeeker}/tickets', [AdminJobSeekerEngagementController::class, 'storeTicket'])->name('job-seekers.tickets.store');
        Route::post('/job-seekers/{jobSeeker}/tickets/{ticket}/reply', [AdminJobSeekerEngagementController::class, 'replyTicket'])->name('job-seekers.tickets.reply');
        Route::post('/job-seekers/{jobSeeker}/tickets/{ticket}/status', [AdminJobSeekerEngagementController::class, 'updateTicketStatus'])->name('job-seekers.tickets.status');

        Route::post('/job-seekers/{jobSeeker}/tasks', [AdminJobSeekerEngagementController::class, 'storeTask'])->name('job-seekers.tasks.store');
        Route::post('/job-seekers/{jobSeeker}/tasks/{task}/complete', [AdminJobSeekerEngagementController::class, 'completeTask'])->name('job-seekers.tasks.complete');

        Route::post('/job-seekers/{jobSeeker}/notes', [AdminJobSeekerEngagementController::class, 'storeNote'])->name('job-seekers.notes.store');

        /*
        |----------------------------------------------------------------
        | Job Posting Management (Stage 2, Phase B — final piece)
        |----------------------------------------------------------------
        | The catalog Job Seeker's Browse Jobs page and the Applications
        | tab's "Create Application" dropdown both read from.
        */
        Route::get('/job-postings', [AdminJobPostingController::class, 'index'])->name('job-postings.index');
        Route::get('/job-postings/create', [AdminJobPostingController::class, 'create'])->name('job-postings.create');
        Route::post('/job-postings', [AdminJobPostingController::class, 'store'])->name('job-postings.store');
        Route::get('/job-postings/{jobPosting}/edit', [AdminJobPostingController::class, 'edit'])->name('job-postings.edit');
        Route::get('/job-postings/{jobPosting}', [AdminJobPostingController::class, 'show'])->name('job-postings.show');
        Route::get('/job-postings/{jobPosting}/applicants', [AdminJobPostingController::class, 'applicants'])->name('job-postings.applicants');
        Route::patch('/job-postings/{jobPosting}', [AdminJobPostingController::class, 'update'])->name('job-postings.update');
        Route::post('/job-postings/{jobPosting}/publish', [AdminJobPostingController::class, 'publish'])->name('job-postings.publish');
        Route::post('/job-postings/{jobPosting}/unpublish', [AdminJobPostingController::class, 'unpublish'])->name('job-postings.unpublish');
        Route::post('/job-postings/{jobPosting}/close', [AdminJobPostingController::class, 'close'])->name('job-postings.close');
        Route::post('/job-postings/{jobPosting}/archive', [AdminJobPostingController::class, 'archive'])->name('job-postings.archive');
        Route::post('/job-postings/{jobPosting}/feature', [AdminJobPostingController::class, 'toggleFeatured'])->name('job-postings.feature');
        Route::post('/job-postings/{jobPosting}/duplicate', [AdminJobPostingController::class, 'duplicate'])->name('job-postings.duplicate');

        /*
        |----------------------------------------------------------------
        | Study Postings — Student-side equivalent of Job Postings
        |----------------------------------------------------------------
        | Same lifecycle, same action set, mirrors job-postings.* exactly.
        | This is what the future public frontend / Student Portal's
        | "Browse Universities" page will read from once built.
        */
        Route::get('/study-postings', [AdminStudyPostingController::class, 'index'])->name('study-postings.index');
        Route::get('/study-postings/create', [AdminStudyPostingController::class, 'create'])->name('study-postings.create');
        Route::post('/study-postings', [AdminStudyPostingController::class, 'store'])->name('study-postings.store');
        Route::get('/study-postings/{studyPosting}/edit', [AdminStudyPostingController::class, 'edit'])->name('study-postings.edit');
        Route::get('/study-postings/{studyPosting}', [AdminStudyPostingController::class, 'show'])->name('study-postings.show');
        Route::patch('/study-postings/{studyPosting}', [AdminStudyPostingController::class, 'update'])->name('study-postings.update');
        Route::post('/study-postings/{studyPosting}/publish', [AdminStudyPostingController::class, 'publish'])->name('study-postings.publish');
        Route::post('/study-postings/{studyPosting}/unpublish', [AdminStudyPostingController::class, 'unpublish'])->name('study-postings.unpublish');
        Route::post('/study-postings/{studyPosting}/close', [AdminStudyPostingController::class, 'close'])->name('study-postings.close');
        Route::post('/study-postings/{studyPosting}/archive', [AdminStudyPostingController::class, 'archive'])->name('study-postings.archive');
        Route::post('/study-postings/{studyPosting}/feature', [AdminStudyPostingController::class, 'toggleFeatured'])->name('study-postings.feature');
        Route::post('/study-postings/{studyPosting}/duplicate', [AdminStudyPostingController::class, 'duplicate'])->name('study-postings.duplicate');
        Route::post('/study-postings/{studyPosting}/downloads', [AdminStudyPostingController::class, 'storeDownload'])->name('study-postings.downloads.store');
        Route::delete('/study-postings/{studyPosting}/downloads/{download}', [AdminStudyPostingController::class, 'destroyDownload'])->name('study-postings.downloads.destroy');

        /*
        |----------------------------------------------------------------
        | Admin Employer Management (Stage 3 — foundation)
        |----------------------------------------------------------------
        | List/create/edit/suspend mirrors Admin\JobSeekerController exactly.
        | Workspace (show) covers Overview + Company Profile this delivery —
        | Worker Requests/Jobs/Candidates tabs and Documents/Payments/
        | Invoices/Appointments/Support/Notes/Activity follow as their own
        | deliveries, same phased approach used for the Job Seeker Workspace.
        */
        Route::get('/employers', [AdminEmployerController::class, 'index'])->name('employers.index');
        Route::get('/employers/create', [AdminEmployerController::class, 'create'])->name('employers.create');
        Route::post('/employers', [AdminEmployerController::class, 'store'])->name('employers.store');
        Route::get('/employers/{employer}/edit', [AdminEmployerController::class, 'edit'])->name('employers.edit');
        Route::patch('/employers/{employer}', [AdminEmployerController::class, 'update'])->name('employers.update');
        Route::post('/employers/{employer}/suspend', [AdminEmployerController::class, 'suspend'])->name('employers.suspend');

        Route::get('/employers/{employer}', [EmployerWorkspaceController::class, 'show'])->name('employers.show');
        Route::patch('/employers/{employer}/profile', [EmployerWorkspaceController::class, 'updateProfile'])->name('employers.profile.update');

        Route::post('/employers/{employer}/documents/request', [EmployerWorkspaceController::class, 'requestDocument'])->name('employers.documents.request');
        Route::post('/employers/{employer}/documents/{document}/verify', [EmployerWorkspaceController::class, 'verifyDocument'])->name('employers.documents.verify');
        Route::post('/employers/{employer}/documents/{document}/reject', [EmployerWorkspaceController::class, 'rejectDocument'])->name('employers.documents.reject');
        Route::post('/employers/{employer}/documents/{document}/upload', [EmployerWorkspaceController::class, 'uploadDocument'])->name('employers.documents.upload');
        Route::patch('/employers/{employer}/documents/{document}', [EmployerWorkspaceController::class, 'updateDocument'])->name('employers.documents.update');
        Route::delete('/employers/{employer}/documents/{document}', [EmployerWorkspaceController::class, 'deleteDocument'])->name('employers.documents.destroy');

        Route::post('/employers/{employer}/payments/{payment}/confirm', [EmployerWorkspaceController::class, 'confirmPayment'])->name('employers.payments.confirm');

        Route::post('/employers/{employer}/invoices/{invoice}/payments', [AdminEmployerFinanceController::class, 'storePayment'])->name('employers.invoices.payments.store');
        Route::patch('/employers/{employer}/payments/{payment}', [AdminEmployerFinanceController::class, 'updatePayment'])->name('employers.payments.update');
        Route::post('/employers/{employer}/payments/{payment}/refund', [AdminEmployerFinanceController::class, 'refundPayment'])->name('employers.payments.refund');
        Route::post('/employers/{employer}/invoices', [AdminEmployerFinanceController::class, 'storeInvoice'])->name('employers.invoices.store');
        Route::post('/employers/{employer}/invoices/{invoice}/send', [AdminEmployerFinanceController::class, 'sendInvoice'])->name('employers.invoices.send');
        Route::post('/employers/{employer}/invoices/{invoice}/cancel', [AdminEmployerFinanceController::class, 'cancelInvoice'])->name('employers.invoices.cancel');

        Route::post('/employers/{employer}/appointments', [EmployerWorkspaceController::class, 'storeAppointment'])->name('employers.appointments.store');
        Route::patch('/employers/{employer}/appointments/{appointment}', [EmployerWorkspaceController::class, 'updateAppointment'])->name('employers.appointments.update');
        Route::post('/employers/{employer}/appointments/{appointment}/confirm', [EmployerWorkspaceController::class, 'confirmAppointment'])->name('employers.appointments.confirm');
        Route::post('/employers/{employer}/appointments/{appointment}/cancel', [EmployerWorkspaceController::class, 'cancelAppointment'])->name('employers.appointments.cancel');
        Route::post('/employers/{employer}/appointments/{appointment}/complete', [EmployerWorkspaceController::class, 'completeAppointment'])->name('employers.appointments.complete');

        Route::post('/employers/{employer}/tickets', [EmployerWorkspaceController::class, 'storeTicket'])->name('employers.tickets.store');
        Route::post('/employers/{employer}/tickets/{ticket}/reply', [EmployerWorkspaceController::class, 'replyTicket'])->name('employers.tickets.reply');
        Route::post('/employers/{employer}/tickets/{ticket}/status', [EmployerWorkspaceController::class, 'updateTicketStatus'])->name('employers.tickets.status');

        Route::post('/employers/{employer}/notes', [EmployerWorkspaceController::class, 'storeNote'])->name('employers.notes.store');

        Route::patch('/employers/{employer}/permissions', [EmployerWorkspaceController::class, 'updatePermissions'])->name('employers.permissions.update');

        /*
        |----------------------------------------------------------------
        | Admin Worker Request Review (Stage 3)
        |----------------------------------------------------------------
        | The Altura Review step of Employer → Worker Request → Altura →
        | Official Job Posting. convertToJobPosting() hands off to the
        | EXISTING JobPostingController for everything after the draft
        | is created — no duplicated posting logic here.
        */
        Route::get('/worker-requests', [AdminWorkerRequestController::class, 'index'])->name('worker-requests.index');
        Route::get('/worker-requests/{workerRequest}', [AdminWorkerRequestController::class, 'show'])->name('worker-requests.show');
        Route::post('/worker-requests/{workerRequest}/review', [AdminWorkerRequestController::class, 'review'])->name('worker-requests.review');
        Route::post('/worker-requests/{workerRequest}/convert', [AdminWorkerRequestController::class, 'convertToJobPosting'])->name('worker-requests.convert');

        // Document preview — admin-scoped, fixes the 403 the student-side download
        // route caused (it sat behind role:student middleware, blocking all staff).
        Route::get('/documents/{document}/preview', [StudentWorkspaceController::class, 'previewDocument'])->name('documents.preview');

        Route::post('/students/{student}/documents/{document}/verify', [StudentWorkspaceController::class, 'verifyDocument'])->name('students.documents.verify');
        Route::post('/students/{student}/documents/{document}/reject', [StudentWorkspaceController::class, 'rejectDocument'])->name('students.documents.reject');
        Route::post('/students/{student}/documents/request', [StudentWorkspaceController::class, 'requestDocument'])->name('students.documents.request');
        Route::post('/students/{student}/documents/{document}/upload', [StudentWorkspaceController::class, 'uploadDocument'])->name('students.documents.upload');
        Route::patch('/students/{student}/documents/{document}', [StudentWorkspaceController::class, 'updateDocument'])->name('students.documents.update');
        Route::delete('/students/{student}/documents/{document}', [StudentWorkspaceController::class, 'deleteDocument'])->name('students.documents.destroy');
        Route::post('/students/{student}/payments/{payment}/confirm', [StudentWorkspaceController::class, 'confirmPayment'])->name('students.payments.confirm');

        // Applications / Documents (per-app) / Admission / Visa
        Route::post('/students/{student}/applications', [AdminApplicationController::class, 'store'])->name('students.applications.store');
        Route::patch('/students/{student}/applications/{application}', [AdminApplicationController::class, 'update'])->name('students.applications.update');
        Route::delete('/students/{student}/applications/{application}', [AdminApplicationController::class, 'destroy'])->name('students.applications.destroy');
        Route::post('/students/{student}/applications/{application}/status', [AdminApplicationController::class, 'changeStatus'])->name('students.applications.status');
        Route::post('/students/{student}/applications/{application}/assign-officer', [AdminApplicationController::class, 'assignOfficer'])->name('students.applications.assign_officer');
        Route::post('/students/{student}/applications/{application}/admission', [AdminApplicationController::class, 'updateAdmission'])->name('students.applications.admission');
        Route::post('/students/{student}/applications/{application}/visa', [AdminApplicationController::class, 'updateVisa'])->name('students.applications.visa');

        // Payments / Invoices
        Route::post('/students/{student}/invoices/{invoice}/payments', [AdminFinanceController::class, 'storePayment'])->name('students.invoices.payments.store');
        Route::patch('/students/{student}/payments/{payment}', [AdminFinanceController::class, 'updatePayment'])->name('students.payments.update');
        Route::post('/students/{student}/payments/{payment}/refund', [AdminFinanceController::class, 'refundPayment'])->name('students.payments.refund');
        Route::post('/students/{student}/invoices', [AdminFinanceController::class, 'storeInvoice'])->name('students.invoices.store');
        Route::post('/students/{student}/invoices/{invoice}/send', [AdminFinanceController::class, 'sendInvoice'])->name('students.invoices.send');
        Route::post('/students/{student}/invoices/{invoice}/cancel', [AdminFinanceController::class, 'cancelInvoice'])->name('students.invoices.cancel');

        // Appointments / Messages / Tasks / Notes
        Route::post('/students/{student}/appointments', [AdminEngagementController::class, 'storeAppointment'])->name('students.appointments.store');
        Route::patch('/students/{student}/appointments/{appointment}', [AdminEngagementController::class, 'updateAppointment'])->name('students.appointments.update');
        Route::post('/students/{student}/appointments/{appointment}/confirm', [AdminEngagementController::class, 'confirmAppointment'])->name('students.appointments.confirm');
        Route::post('/students/{student}/appointments/{appointment}/cancel', [AdminEngagementController::class, 'cancelAppointment'])->name('students.appointments.cancel');
        Route::post('/students/{student}/appointments/{appointment}/complete', [AdminEngagementController::class, 'completeAppointment'])->name('students.appointments.complete');

        Route::post('/students/{student}/tickets', [AdminEngagementController::class, 'storeTicket'])->name('students.tickets.store');
        Route::post('/students/{student}/tickets/{ticket}/reply', [AdminEngagementController::class, 'replyTicket'])->name('students.tickets.reply');
        Route::post('/students/{student}/tickets/{ticket}/status', [AdminEngagementController::class, 'updateTicketStatus'])->name('students.tickets.status');

        Route::post('/students/{student}/tasks', [AdminEngagementController::class, 'storeTask'])->name('students.tasks.store');
        Route::post('/students/{student}/tasks/{task}/complete', [AdminEngagementController::class, 'completeTask'])->name('students.tasks.complete');

        Route::post('/students/{student}/notes', [AdminEngagementController::class, 'storeNote'])->name('students.notes.store');
    });

require __DIR__.'/auth.php';
