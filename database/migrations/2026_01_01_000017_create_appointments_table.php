<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('study_application_id')->nullable()->constrained()->nullOnDelete();

            $table->string('type'); // e.g. "Student Consultation", "Visa Interview"
            $table->enum('mode', ['video', 'physical', 'phone']);
            $table->timestamp('scheduled_at');
            $table->enum('status', ['requested', 'confirmed', 'completed', 'cancelled'])->default('requested');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['student_id', 'scheduled_at']);
            $table->index(['staff_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
