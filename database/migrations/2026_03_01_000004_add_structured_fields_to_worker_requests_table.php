<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two fixes in one migration:
     *
     * 1. additional_requirements was a single catch-all textarea covering
     *    salary, responsibilities, skills, benefits, and genuine requirements
     *    all at once — splits it into the same structured fields job_postings
     *    already has (salary_min/max, currency, responsibilities, skills,
     *    benefits, requirements). This also means Admin's convertToJobPosting()
     *    can now carry these straight over instead of Admin re-typing them
     *    on the resulting posting. additional_requirements stays as a genuine
     *    free-text "anything else" field, not the dumping ground it was.
     *
     * 2. employer_response/employer_responded_at — Admin could set a request
     *    to "clarification_required" with review_notes, but the Employer had
     *    no way to actually answer. This is a real, single-round-trip
     *    response, not a full message thread (that's the SupportTicket
     *    system's job if this ever needs to become a back-and-forth).
     */
    public function up(): void
    {
        Schema::table('worker_requests', function (Blueprint $table) {
            $table->decimal('salary_min', 12, 2)->nullable()->after('work_location');
            $table->decimal('salary_max', 12, 2)->nullable()->after('salary_min');
            $table->string('currency', 3)->nullable()->after('salary_max');
            $table->text('responsibilities')->nullable()->after('currency');
            $table->text('skills')->nullable()->after('responsibilities');
            $table->text('benefits')->nullable()->after('skills');
            $table->text('requirements')->nullable()->after('benefits');

            $table->text('employer_response')->nullable()->after('review_notes');
            $table->timestamp('employer_responded_at')->nullable()->after('employer_response');
        });
    }

    public function down(): void
    {
        Schema::table('worker_requests', function (Blueprint $table) {
            $table->dropColumn([
                'salary_min', 'salary_max', 'currency', 'responsibilities', 'skills', 'benefits', 'requirements',
                'employer_response', 'employer_responded_at',
            ]);
        });
    }
};
