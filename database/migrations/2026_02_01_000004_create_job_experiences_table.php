<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_seeker_profile_id')->constrained()->cascadeOnDelete();

            $table->string('occupation');
            $table->string('employer')->nullable();
            $table->decimal('years_of_experience', 4, 1)->nullable();
            $table->boolean('is_current')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_experiences');
    }
};
