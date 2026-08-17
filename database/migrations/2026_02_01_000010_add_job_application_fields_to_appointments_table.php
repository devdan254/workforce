<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive only. job_application_id mirrors study_application_id's
     * existing nullable/null-on-delete pattern; mutual exclusivity enforced
     * by the Appointment model's HasExclusiveApplicationLink trait.
     * meeting_link is the one genuinely new field interviews need that
     * consultations never did (spec's "Join Interview →" action needs
     * somewhere to point) — nullable, unused by existing Student appointments.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('job_application_id')->nullable()->after('study_application_id')
                ->constrained()->nullOnDelete();
            $table->string('meeting_link')->nullable()->after('mode');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_application_id');
            $table->dropColumn('meeting_link');
        });
    }
};
