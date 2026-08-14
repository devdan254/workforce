<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->morphs('noteable'); // noteable_type, noteable_id

            $table->text('body');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->boolean('is_internal')->default(true); // internal staff note vs. student-visible

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
