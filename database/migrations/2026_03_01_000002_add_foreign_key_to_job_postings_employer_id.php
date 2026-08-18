<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * employer_id has been sitting nullable/unconstrained since Stage 2,
     * deliberately, for exactly this moment. Since Employer = User with
     * role=employer (no separate company table), this constrains straight
     * to users.id — the same pattern job_seeker_id/student_id already use.
     * Purely additive: existing postings all have employer_id = null,
     * which remains perfectly valid (Admin-created postings with no
     * employer origin).
     */
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->foreign('employer_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropForeign(['employer_id']);
        });
    }
};
