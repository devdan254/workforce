<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Resource was previously a single global catalog shown to every
     * portal regardless of relevance — flagged as a known gap when
     * building Employer's Resources page. This is the fix: which
     * audiences (student/job_seeker/employer) a resource is actually
     * meant for. Nullable/empty means "visible to everyone" — the exact
     * behavior every existing row already had, so nothing already seeded
     * silently disappears once this ships.
     */
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->json('audience')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('audience');
        });
    }
};
