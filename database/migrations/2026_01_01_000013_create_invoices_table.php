<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('study_application_id')->nullable()->constrained()->nullOnDelete();

            $table->string('invoice_number')->unique(); // e.g. INV-2026-000045
            $table->string('description')->nullable();
            $table->string('currency', 3)->default('KES');

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0); // kept in sync by PaymentService, not set directly

            $table->enum('status', ['draft', 'sent', 'pending', 'paid', 'overdue', 'cancelled'])->default('draft');

            $table->date('due_date')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
