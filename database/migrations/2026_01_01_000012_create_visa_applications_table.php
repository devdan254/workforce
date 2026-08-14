<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_application_id')->constrained()->cascadeOnDelete();
            $table->string('destination_country');

            $table->foreignId('status_id')->constrained('statuses')->restrictOnDelete();

            $table->timestamp('embassy_appointment_at')->nullable();
            $table->enum('decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            $table->index(['study_application_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_applications');
    }
};
