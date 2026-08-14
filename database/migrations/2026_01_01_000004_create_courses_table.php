<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('study_level', ['certificate', 'diploma', 'bachelor', 'master', 'phd']);
            $table->unsignedSmallInteger('duration_months')->nullable();
            $table->decimal('tuition_fee', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->json('intake_months')->nullable(); // e.g. [1,9] for Jan & Sep intakes
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['university_id', 'study_level', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
