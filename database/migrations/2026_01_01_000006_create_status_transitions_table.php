<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_transitions', function (Blueprint $table) {
            $table->id();
            // nullable from_status_id = this is a valid *initial* status for the type
            $table->foreignId('from_status_id')->nullable()->constrained('statuses')->cascadeOnDelete();
            $table->foreignId('to_status_id')->constrained('statuses')->cascadeOnDelete();
            $table->enum('status_type', ['application', 'visa']);
            $table->timestamps();

            $table->unique(['from_status_id', 'to_status_id', 'status_type'], 'unique_transition');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_transitions');
    }
};
