<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Same reasoning as documents.job_posting_id (Stage 3 of the Employer
     * build) — study_application_id/job_application_id identify WHICH
     * student/candidate an invoice belongs to, not WHETHER it's a visa
     * fee specifically (a student could plausibly have both a Tuition Fee
     * invoice and a Visa Processing Fee invoice under the same
     * study_application_id). This tag is what lets Visa Management show
     * genuinely visa-related invoices only, not everything that person
     * has ever been billed for. Independent, nullable, not part of any
     * exclusivity constraint — an invoice can be both study-application-
     * scoped AND visa-tagged at the same time.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('visa_application_id')->nullable()->after('job_application_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visa_application_id');
        });
    }
};
