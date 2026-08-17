<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive only — mirrors study_application_id's existing nullable/cascade
     * pattern exactly. NULL for every existing Student document row; no
     * impact on current data. Mutual exclusivity with study_application_id is
     * enforced by the Document model's HasExclusiveApplicationLink trait, not
     * a DB-level CHECK constraint (see that trait's docblock for why).
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('job_application_id')->nullable()->after('study_application_id')
                ->constrained()->cascadeOnDelete();
            $table->index(['job_application_id']);
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_application_id');
        });
    }
};
