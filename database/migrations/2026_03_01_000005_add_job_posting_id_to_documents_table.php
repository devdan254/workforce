<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nullable, independent of study_application_id/job_application_id
     * (not part of HasExclusiveApplicationLink) — this tags an EMPLOYER's
     * own document as relating to a specific job posting (e.g. "Employment
     * Contract for the Nurse role" vs a general company document like a
     * Business License). Conceptually separate from job_application_id,
     * which is the CANDIDATE's application-specific document link — the
     * two never apply to the same row, since they describe different
     * people's documents, but there's no need to enforce that formally
     * since only Employer documents will ever have job_posting_id set.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('job_posting_id')->nullable()->after('job_application_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_posting_id');
        });
    }
};
