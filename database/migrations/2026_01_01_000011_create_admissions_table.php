<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_application_id')->unique()->constrained()->cascadeOnDelete();

            $table->enum('decision', ['pending', 'offered', 'accepted', 'rejected'])->default('pending');
            $table->foreignId('offer_letter_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->text('conditions')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
