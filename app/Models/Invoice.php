<?php

namespace App\Models;

use App\Concerns\HasExclusiveApplicationLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasExclusiveApplicationLink;

    protected $fillable = [
        'student_id', 'study_application_id', 'job_application_id', 'visa_application_id', 'invoice_number', 'description',
        'currency', 'subtotal', 'tax', 'total', 'amount_paid', 'status', 'due_date', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'due_date' => 'date',
            'sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-'.now()->format('Y').'-'.str_pad(
                    (string) (static::count() + 1),
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /* ---------- Relationships ---------- */

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Same column as student() — readability alias for Job Seeker contexts.
     * student_id → users.id was never actually student-specific; it's the
     * generic person-reference the shared services were built around.
     */
    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
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
     * Tags an invoice as genuinely visa-related — see this column's own
     * migration for why study_application_id/job_application_id alone
     * aren't enough to identify that. Independent of both; an invoice can
     * carry this alongside either.
     */
    public function visaApplication(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /* ---------- Computed ---------- */

    public function getBalanceAttribute(): float
    {
        return (float) $this->total - (float) $this->amount_paid;
    }

    /**
     * The ONE place "Total Cost / Paid / Outstanding" gets computed — used by
     * both the Student Dashboard and (once built) the Job Seeker Dashboard.
     * Generic name since student_id holds ANY person, not just students.
     */
    public static function financialSummaryForPerson(int $personId): array
    {
        $invoices = static::where('student_id', $personId)->get();

        $total = (float) $invoices->sum('total');
        $paid = (float) $invoices->sum('amount_paid');

        return [
            'total' => $total,
            'paid' => $paid,
            'balance' => $total - $paid,
            'currency' => $invoices->first()->currency ?? 'KES',
        ];
    }

    /**
     * @deprecated in favor of financialSummaryForPerson() — kept as a thin
     * alias, not removed, so every existing Student controller/view call
     * site (DashboardController, InvoiceController, PaymentController, the
     * Admin StudentWorkspaceController, etc.) needs ZERO changes.
     */
    public static function financialSummaryForStudent(int $studentId): array
    {
        return static::financialSummaryForPerson($studentId);
    }
}

