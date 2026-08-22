<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the third applicant path HasExclusiveApplicationLink's own
     * docblock already anticipated ("User-level / vault-level (both
     * null)") — a visa applicant with NEITHER a Study nor Job Application
     * yet. user_id is the anchor for that case ONLY; when study_application_id
     * or job_application_id is set, the owning person is already reachable
     * through that relationship, so user_id stays null there — one path to
     * the owning person per row, never two competing ones.
     *
     * The personal/travel fields mirror exactly what the public Visa
     * Application wizard already collects (Public\VisaApplicationController)
     * — previously this data was only ever emailed, never persisted. Kept
     * nullable throughout since Admin-created rows (the existing Student/
     * Job Seeker path) won't populate most of these, and stay as free-form
     * Yes/No strings where the wizard itself used a Yes/No select rather
     * than a checkbox — no silent boolean reinterpretation of what was
     * actually submitted.
     */
    public function up(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')
                ->constrained('users')->nullOnDelete();

            $table->string('first_name')->nullable()->after('user_id');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('country_of_residence')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();

            $table->string('purpose_of_travel')->nullable();
            $table->date('expected_travel_date')->nullable();
            $table->string('duration_of_stay')->nullable();
            $table->string('has_admission_letter')->nullable();
            $table->string('has_employment_contract')->nullable();
            $table->string('has_invitation_letter')->nullable();

            $table->string('visa_type')->nullable();
            $table->string('previously_applied')->nullable();
            $table->string('previously_refused')->nullable();
            $table->text('refusal_explanation')->nullable();
            $table->string('travelled_internationally')->nullable();
            $table->string('countries_visited')->nullable();

            $table->text('additional_info')->nullable();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');

            $table->dropColumn([
                'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender',
                'nationality', 'country_of_residence', 'passport_number', 'passport_expiry',
                'purpose_of_travel', 'expected_travel_date', 'duration_of_stay',
                'has_admission_letter', 'has_employment_contract', 'has_invitation_letter',
                'visa_type', 'previously_applied', 'previously_refused', 'refusal_explanation',
                'travelled_internationally', 'countries_visited', 'additional_info',
            ]);
        });
    }
};
