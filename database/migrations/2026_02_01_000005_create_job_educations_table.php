<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_seeker_profile_id')->constrained()->cascadeOnDelete();

            $table->enum('level', ['primary', 'secondary', 'diploma', 'bachelor', 'master', 'not_applicable']);
            $table->string('institution')->nullable();
            $table->string('course')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_educations');
    }
};
