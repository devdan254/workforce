<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('study_application_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('document_category_id')->constrained()->restrictOnDelete();

            $table->string('name'); // e.g. "Passport", "Academic Certificate"
            $table->string('file_path')->nullable(); // null while status = required (not yet uploaded)
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->enum('status', ['required', 'uploaded', 'under_review', 'verified', 'rejected'])
                  ->default('required');

            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('rejection_reason')->nullable();
            $table->text('verification_notes')->nullable();

            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['study_application_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
