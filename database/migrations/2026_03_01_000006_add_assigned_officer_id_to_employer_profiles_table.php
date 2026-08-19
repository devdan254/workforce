<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Job Seeker/Student's "assigned officer" lives on each application
     * (per-application relationship). Employer has no equivalent
     * application — their relationship with Altura is account-level, not
     * per-submission — so this lives on employer_profiles instead: one
     * assigned Account Manager per company, matching the spec's own
     * "Assigned Account Manager" framing (Section 38).
     */
    public function up(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->foreignId('assigned_officer_id')->nullable()->after('user_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_officer_id');
        });
    }
};
