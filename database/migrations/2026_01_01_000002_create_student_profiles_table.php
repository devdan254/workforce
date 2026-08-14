<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();

            $table->string('address_line')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->string('highest_qualification')->nullable();
            $table->string('institution')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('field_of_study')->nullable();
            $table->string('grade')->nullable();

            $table->string('passport_number')->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('passport_country')->nullable();

            $table->json('preferred_countries')->nullable();
            $table->string('preferred_course')->nullable();
            $table->string('preferred_study_level')->nullable();
            $table->string('preferred_intake')->nullable();

            $table->unsignedTinyInteger('profile_completion_percent')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
