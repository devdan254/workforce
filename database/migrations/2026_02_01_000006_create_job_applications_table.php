<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            // Reuses the SAME `users` reference pattern as study_applications.student_id —
            // a job seeker is a User with the job_seeker role, no separate identity table.
            $table->foreignId('job_seeker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained()->restrictOnDelete();

            $table->string('reference_number')->unique(); // e.g. ALT-JA-2026-000123

            // Reuses the SAME statuses/status_transitions engine as study_applications —
            // just a different `type` row ('job_application'). ApplicationStatusService
            // works against this unmodified, since JobApplication implements
            // HasStatusWorkflow exactly like StudyApplication does.
            $table->foreignId('status_id')->constrained('statuses')->restrictOnDelete();

            $table->foreignId('assigned_officer_id')->nullable()->constrained('users')->nullOnDelete();

            // Deliberately NO fee columns here (unlike study_applications' application_fee/
            // tuition_fee/service_fee) — per spec, job seeker fees are ad-hoc/optional,
            // never a mandatory model. Invoices get created only when a fee genuinely applies.

            $table->timestamp('applied_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_seeker_id', 'status_id']);
            $table->index(['assigned_officer_id']);
            $table->unique(['job_seeker_id', 'job_posting_id'], 'unique_seeker_posting_application');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
