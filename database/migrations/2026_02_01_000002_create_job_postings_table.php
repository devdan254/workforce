<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_category_id')->constrained()->restrictOnDelete();

            // Deliberately UNCONSTRAINED (no ->constrained()) — there is no
            // `employers` table yet (Stage 3). This lets Admin optionally tag
            // a posting with an employer reference now, without requiring the
            // employers table to exist, and without a job posting ever
            // depending on one existing. The FK constraint itself gets added
            // in a small follow-up migration once Stage 3 creates `employers`.
            $table->unsignedBigInteger('employer_id')->nullable();

            $table->string('title');
            $table->string('country');
            $table->string('city')->nullable();

            $table->string('currency', 3)->default('USD');
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();

            $table->unsignedInteger('vacancies')->default(1);
            $table->enum('employment_type', ['permanent', 'contract', 'temporary', 'seasonal'])->default('contract');
            $table->string('experience_required')->nullable();
            $table->string('education_requirement')->nullable();

            $table->text('description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->text('skills')->nullable();
            $table->text('languages')->nullable();
            $table->text('benefits')->nullable();

            $table->boolean('accommodation_provided')->default(false);
            $table->boolean('meals_provided')->default(false);
            $table->boolean('visa_support_provided')->default(false);
            $table->boolean('air_ticket_provided')->default(false);
            $table->string('working_hours')->nullable();

            $table->date('application_deadline')->nullable();

            $table->enum('status', ['draft', 'open', 'closed', 'filled', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);

            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->string('slug')->unique();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'job_category_id']);
            $table->index(['country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
