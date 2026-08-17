<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `statuses.type` was originally defined as ENUM('application', 'visa') —
     * a genuine Stage 1 design mistake. The whole point of the statuses/
     * status_transitions engine is that it's generic and reusable for ANY
     * workflow (ApplicationStatusService is built against the
     * HasStatusWorkflow interface, not specific type strings) — but an ENUM
     * column is a closed, fixed list that can't hold new type values like
     * 'job_application' without a schema change every time. Widening to a
     * plain string is what this column should have been from the start.
     *
     * Non-destructive: existing 'application'/'visa' rows are unaffected —
     * they're valid strings either way, just no longer constrained to a
     * fixed enum list.
     */
    public function up(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });

        Schema::table('status_transitions', function (Blueprint $table) {
            $table->string('status_type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->enum('type', ['application', 'visa'])->change();
        });

        Schema::table('status_transitions', function (Blueprint $table) {
            $table->enum('status_type', ['application', 'visa'])->change();
        });
    }
};
