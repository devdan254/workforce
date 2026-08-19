<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Student-side equivalent of job_postings — same shape, same
     * lifecycle (draft/open/closed/archived, Publish/Unpublish/Close/
     * Archive/Feature/Duplicate), same soft-deletes/slug pattern. Fields
     * differ because a study opportunity and a job vacancy are genuinely
     * different things (scholarship type, courses offered, intake, school
     * fees vs salary), but the administrative shell — how Admin manages
     * the catalog — is identical on purpose, matching the existing pattern
     * rather than inventing a new one.
     */
    public function up(): void
    {
        Schema::create('study_postings', function (Blueprint $table) {
            $table->id();

            $table->string('image_path')->nullable();
            $table->string('university_name');
            $table->string('country');

            $table->enum('scholarship_type', ['full', 'partial', 'none'])->default('none');

            // Free text, same pattern as job_postings.skills/benefits — a
            // course catalog table would be over-engineering for what's
            // fundamentally a list Admin types in, same reasoning used
            // throughout this app for "can be many" text fields.
            $table->text('courses_offered');

            $table->decimal('school_fees_per_semester', 12, 2)->nullable();
            $table->string('fees_currency', 3)->default('USD');

            $table->string('age_requirement')->nullable();
            $table->text('requirements')->nullable();
            $table->text('description')->nullable();
            $table->string('intake')->nullable();
            $table->text('application_eligibility')->nullable();

            $table->enum('status', ['draft', 'open', 'closed', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);

            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->string('slug')->unique();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_postings');
    }
};
