<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * visa_applications was built exclusively for Study Applications
     * (study_application_id NOT NULL) — the same generalization pattern
     * already approved and applied to documents/invoices/appointments:
     * make the existing FK nullable, add the new one nullable, mutual
     * exclusivity enforced by the VisaApplication model's
     * HasExclusiveApplicationLink trait, not a DB CHECK constraint.
     *
     * Existing Student visa rows are unaffected — their study_application_id
     * stays exactly as populated; only the column's NULL-ability changes,
     * which doesn't touch existing data.
     */
    public function up(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {
            $table->foreignId('study_application_id')->nullable()->change();
            $table->foreignId('job_application_id')->nullable()->after('study_application_id')
                ->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_application_id');
            $table->foreignId('study_application_id')->nullable(false)->change();
        });
    }
};
