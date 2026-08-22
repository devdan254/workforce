<?php

namespace App\Models;

use App\Concerns\HasExclusiveApplicationLink;
use App\Contracts\HasStatusWorkflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * The one central Visa Management entity — deliberately not split into
 * separate Student/Job Seeker/Guest systems. Three ways a row can be
 * "owned":
 *   1. Student-linked   — study_application_id set, owner is that
 *      application's student.
 *   2. Job-Seeker-linked — job_application_id set, owner is that
 *      application's job seeker.
 *   3. Standalone/guest  — NEITHER set (HasExclusiveApplicationLink already
 *      allows this — see its own docblock), user_id set directly instead.
 *      This is someone who applied for a visa through the public wizard
 *      with no existing Student/Job Seeker account; Admin can later
 *      "convert" them once their real category is known.
 * owner() is the one method everything else (Documents, Payments, Invoices,
 * Admin Visa Management's display) should call to resolve "which User does
 * this visa application actually belong to" — never read study_application/
 * job_application/user_id directly for that purpose, so this stays the
 * single place that logic lives.
 */
class VisaApplication extends Model implements HasStatusWorkflow
{
    use LogsActivity, HasExclusiveApplicationLink;

    protected $fillable = [
        'study_application_id', 'job_application_id', 'user_id', 'destination_country', 'status_id',
        'embassy_appointment_at', 'decision', 'decided_at',

        'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'nationality',
        'country_of_residence', 'passport_number', 'passport_expiry',

        'purpose_of_travel', 'expected_travel_date', 'duration_of_stay',
        'has_admission_letter', 'has_employment_contract', 'has_invitation_letter',

        'visa_type', 'previously_applied', 'previously_refused', 'refusal_explanation',
        'travelled_internationally', 'countries_visited', 'additional_info',
    ];

    protected function casts(): array
    {
        return [
            'embassy_appointment_at' => 'datetime',
            'decided_at' => 'datetime',
            'date_of_birth' => 'date',
            'passport_expiry' => 'date',
            'expected_travel_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status_id', 'decision'])->logOnlyDirty();
    }

    public function studyApplication(): BelongsTo
    {
        return $this->belongsTo(StudyApplication::class);
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * Only meaningfully set for the standalone/guest path — see class
     * docblock. Null for Student- or Job-Seeker-linked rows; use owner()
     * below to resolve the owning User regardless of which path a given
     * row is on.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'historyable')->latest();
    }

    public function statusType(): string
    {
        return 'visa';
    }

    /**
     * 'student' | 'job_seeker' | 'guest' — computed from which link is
     * actually set, never stored redundantly. This is what Admin Visa
     * Management's "applicant type" filter reads.
     */
    public function applicantType(): string
    {
        return match (true) {
            ! is_null($this->study_application_id) => 'student',
            ! is_null($this->job_application_id) => 'job_seeker',
            default => 'guest',
        };
    }

    /**
     * The single method everything else should call to find out who this
     * visa application actually belongs to. Loads the relevant relationship
     * chain on demand — callers doing this for many rows at once (an index
     * listing, say) should eager-load studyApplication.student /
     * jobApplication.jobSeeker / user themselves first to avoid N+1s; this
     * method doesn't do that loading itself, only the resolution logic.
     */
    public function owner(): ?User
    {
        return match ($this->applicantType()) {
            'student' => $this->studyApplication?->student,
            'job_seeker' => $this->jobApplication?->jobSeeker,
            default => $this->user,
        };
    }

    /**
     * Display name — for a converted or already-linked applicant this is
     * just their account name; for a guest, falls back to whatever was
     * captured on the wizard itself (first/middle/last), since they may
     * not have a User row with a proper name yet at every point in the flow.
     */
    /**
     * For a Student/Job-Seeker-linked application: the SAME documents
     * already managed through their own Workspace Documents tab (scoped
     * to this specific application) — Visa Management reads that existing
     * set, it doesn't maintain a separate one. For a standalone/guest
     * applicant: vault-level documents on their own account (no
     * application to scope by), which is where the public wizard's
     * uploads land (Public\VisaApplicationController).
     */
    public function documentsQuery()
    {
        return match ($this->applicantType()) {
            'student' => \App\Models\Document::where('study_application_id', $this->study_application_id),
            'job_seeker' => \App\Models\Document::where('job_application_id', $this->job_application_id),
            default => \App\Models\Document::where('student_id', $this->user_id)->whereNull('study_application_id')->whereNull('job_application_id'),
        };
    }

    /**
     * Genuinely visa-related invoices only — see invoices.visa_application_id's
     * own migration for why owner-alone or application-alone scoping isn't
     * enough (a Student could have both Tuition and Visa Processing fee
     * invoices under the same study_application_id). Every invoice created
     * THROUGH Visa Management carries this tag; that's what makes this
     * query meaningful rather than arbitrary.
     */
    public function invoicesQuery()
    {
        return \App\Models\Invoice::where('visa_application_id', $this->id);
    }

    public function displayName(): string
    {
        if ($owner = $this->owner()) {
            return $owner->name;
        }

        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}") ?: 'Unnamed Applicant';
    }
}
