<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['application', 'visa']);
            $table->string('slug'); // e.g. 'admission_pending'
            $table->string('label'); // e.g. 'Admission Pending'
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_terminal')->default(false); // true for 'Completed', 'Rejected', etc.
            $table->timestamps();

            $table->unique(['type', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
