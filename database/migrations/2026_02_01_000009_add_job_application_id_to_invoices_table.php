<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive only — mirrors study_application_id's existing nullable/
     * null-on-delete pattern exactly. Mutual exclusivity enforced by the
     * Invoice model's HasExclusiveApplicationLink trait.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('job_application_id')->nullable()->after('study_application_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_application_id');
        });
    }
};
