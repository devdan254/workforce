<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'student_id', 'study_application_id', 'invoice_number', 'description',
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

    public function studyApplication(): BelongsTo
    {
        return $this->belongsTo(StudyApplication::class);
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
     * both the Dashboard and the Payments page. If this logic ever needs to
     * change (e.g. excluding cancelled invoices), it changes here once.
     */
    public static function financialSummaryForStudent(int $studentId): array
    {
        $invoices = static::where('student_id', $studentId)->get();

        $total = (float) $invoices->sum('total');
        $paid = (float) $invoices->sum('amount_paid');

        return [
            'total' => $total,
            'paid' => $paid,
            'balance' => $total - $paid,
            'currency' => $invoices->first()->currency ?? 'KES',
        ];
    }
}