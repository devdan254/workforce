<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_seeker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();

            $table->string('address_line')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            // Professional summary — the repeatable history (multiple jobs,
            // multiple qualifications) lives in job_experiences/job_educations
            // below; these are the quick-view fields shown on cards/dashboards.
            $table->string('professional_title')->nullable();
            $table->string('industry')->nullable();
            $table->text('skills')->nullable();
            $table->text('languages')->nullable();
            $table->text('certifications')->nullable();

            $table->string('passport_number')->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('passport_country')->nullable();

            // Job preferences (spec Section 20/21 — designed for future expansion
            // without new columns: countries/industries/positions as JSON arrays).
            $table->json('preferred_countries')->nullable();
            $table->json('preferred_industries')->nullable();
            $table->json('preferred_positions')->nullable();
            $table->string('preferred_employment_type')->nullable();
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->string('expected_salary_currency', 3)->nullable();
            $table->date('earliest_availability')->nullable();
            $table->boolean('willing_to_relocate')->default(true);
            $table->boolean('worked_abroad_before')->nullable();

            $table->unsignedTinyInteger('profile_completion_percent')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_seeker_profiles');
    }
};
