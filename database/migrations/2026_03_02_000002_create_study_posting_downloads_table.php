<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Downloads — this can be fee structure or necessary downloads" — a
     * genuine one-to-many (a posting can have several downloadable files),
     * so this is its own small child table rather than a single file_path
     * column. Deliberately NOT reusing the polymorphic Document system —
     * Document is scoped to a PERSON (student_id), not a catalog item, so
     * forcing a study posting's fee structure through that model would
     * mean a document with no real owner. A dedicated table is the honest
     * fit here, same reasoning as job_postings not reusing Document either.
     */
    public function up(): void
    {
        Schema::create('study_posting_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_posting_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_posting_downloads');
    }
};
