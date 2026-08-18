<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The literal "Employer → Worker Request → Altura" step. Deliberately
     * does NOT duplicate Company Information fields (name/industry/country/
     * website/size) — those already live on employer_profiles, filled in
     * during Phase B Step 1. Re-asking them here on every request would be
     * duplicate data entry; the form reads them from the profile instead.
     */
    public function up(): void
    {
        Schema::create('worker_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();

            // Contact person for THIS request — defaults to the employer
            // account's own name/email/phone, but can differ (e.g. a
            // different department handles this particular hire).
            $table->string('contact_name')->nullable();
            $table->string('contact_job_title')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            // Hiring requirements
            $table->string('job_title');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('preferred_experience')->nullable();
            $table->enum('employment_type', ['permanent', 'contract', 'temporary', 'seasonal', 'part_time', 'full_time'])->default('contract');
            $table->string('age_range')->nullable();
            $table->date('preferred_start_date')->nullable();
            $table->string('work_location')->nullable();
            $table->text('additional_requirements')->nullable();

            $table->enum('status', ['draft', 'submitted', 'under_review', 'clarification_required', 'approved', 'rejected', 'converted'])
                ->default('submitted');

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            // Once Altura converts this into a real job posting, this links
            // back — the Worker Request and the Job Posting stay
            // distinguishable, per the spec's explicit instruction not to
            // conflate "what the employer asked for" with "what Altura published."
            $table->foreignId('job_posting_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->index(['employer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_requests');
    }
};
