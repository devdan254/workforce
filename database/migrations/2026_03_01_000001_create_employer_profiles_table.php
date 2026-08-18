<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One employer account = one company (per Stage 3's explicit
     * simplification — no separate employer_users/company distinction).
     * Mirrors job_seeker_profiles exactly: a 1:1 profile table on users,
     * same pattern, same reuse of the shared architecture.
     */
    public function up(): void
    {
        Schema::create('employer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('company_name');
            $table->string('industry')->nullable();
            $table->string('country');
            $table->string('company_website')->nullable();
            $table->enum('company_size', ['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'])->nullable();

            $table->string('city')->nullable();
            $table->text('company_description')->nullable();
            $table->string('logo_path')->nullable();

            // Contact person — separate from the account's own name/email/phone
            // (the account IS the contact, but their job title is
            // company-specific info, not a generic User field).
            $table->string('contact_job_title')->nullable();

            $table->unsignedTinyInteger('profile_completion_percent')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employer_profiles');
    }
};
