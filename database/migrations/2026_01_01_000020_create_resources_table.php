<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable(); // e.g. "Study Abroad Guide", "Country Guides"
            $table->enum('type', ['guide', 'faq', 'form', 'checklist']);
            $table->text('body')->nullable();       // rich text content, when type doesn't need a file
            $table->string('file_path')->nullable(); // downloadable file, when applicable
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['type', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
