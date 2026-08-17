<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->unique()->constrained()->cascadeOnDelete();

            $table->decimal('salary', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->text('benefits')->nullable();
            $table->string('working_hours')->nullable();
            $table->boolean('accommodation_provided')->default(false);
            $table->boolean('meals_provided')->default(false);
            $table->boolean('air_ticket_provided')->default(false);
            $table->string('contract_duration')->nullable();
            $table->date('start_date')->nullable();
            $table->string('location')->nullable();

            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->foreignId('offer_letter_document_id')->nullable()->constrained('documents')->nullOnDelete();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
