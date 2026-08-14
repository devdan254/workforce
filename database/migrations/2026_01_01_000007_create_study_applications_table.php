<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();

            $table->string('reference_number')->unique(); // e.g. ALT-2026-000123

            $table->foreignId('status_id')->constrained('statuses')->restrictOnDelete();

            $table->string('intake')->nullable(); // e.g. "September 2026"
            $table->date('application_deadline')->nullable();

            $table->foreignId('assigned_officer_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('application_fee', 12, 2)->default(0);
            $table->decimal('tuition_fee', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->string('currency', 3)->default('KES');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status_id']);
            $table->index(['assigned_officer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_applications');
    }
};
