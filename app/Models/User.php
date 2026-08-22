<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar_path',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /* ---------- Relationships (Student) ---------- */

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function studyApplications(): HasMany
    {
        return $this->hasMany(StudyApplication::class, 'student_id');
    }

    public function assignedApplications(): HasMany
    {
        return $this->hasMany(StudyApplication::class, 'assigned_officer_id');
    }

    /* ---------- Relationships (Job Seeker) ---------- */

    public function jobSeekerProfile(): HasOne
    {
        return $this->hasOne(JobSeekerProfile::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_seeker_id');
    }

    public function assignedJobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'assigned_officer_id');
    }

    /* ---------- Relationships (Employer) ---------- */

    public function employerProfile(): HasOne
    {
        return $this->hasOne(EmployerProfile::class);
    }

    /**
     * Job postings this employer's hiring requests resulted in — an
     * employer never creates these directly (Admin/Altura does, per the
     * spec's core business rule), but they belong to this employer once
     * Altura links them.
     */
    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'employer_id');
    }

    public function workerRequests(): HasMany
    {
        return $this->hasMany(WorkerRequest::class, 'employer_id');
    }

    /* ---------- Relationships (shared — student_id column serves all three) ---------- */

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'student_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'student_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'student_id');
    }

    public function staffAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'staff_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'student_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /* ---------- Convenience ---------- */

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function isJobSeeker(): bool
    {
        return $this->hasRole('job_seeker');
    }

    public function isEmployer(): bool
    {
        return $this->hasRole('employer');
    }

    /**
     * A guest visa applicant with no Student/Job Seeker account yet — see
     * VisaApplication's own class docblock for the full reasoning. This
     * role carries no permissions and has no portal; it exists purely so
     * Documents/Payments/Invoices have a real user_id to attach to before
     * "Convert Applicant" assigns their actual category.
     */
    public function isVisaApplicantOnly(): bool
    {
        return $this->hasRole('visa_applicant') && ! $this->isStudent() && ! $this->isJobSeeker();
    }

    public function isStaff(): bool
    {
        return ! $this->isStudent() && ! $this->isJobSeeker() && ! $this->isEmployer();
    }
}
