<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\Student\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\Student\EngagementController as AdminEngagementController;
use App\Http\Controllers\Admin\Student\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\StudentWorkspaceController;
use App\Http\Controllers\ProfileController;
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

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

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

        Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [AdminStudentController::class, 'create'])->name('students.create');
        Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('students.edit');
        Route::patch('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
        Route::post('/students/{student}/suspend', [AdminStudentController::class, 'suspend'])->name('students.suspend');

        // The unified Student workspace.
        Route::get('/students/{student}', [StudentWorkspaceController::class, 'show'])->name('students.show');
        Route::patch('/students/{student}/profile', [StudentWorkspaceController::class, 'updateProfile'])->name('students.profile.update');

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
