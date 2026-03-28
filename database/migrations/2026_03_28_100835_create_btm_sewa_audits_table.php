<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('btm_sewa_audits', function (Blueprint $table) {
            $table->id();
            $table->string('unit_number');
            $table->foreignId('transaction_id')->constrained('btm_sewa_transactions')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('period_name'); // e.g. "Januari 2025"
            $table->decimal('aypsis_nominal', 15, 2)->default(0);
            $table->decimal('vendor_nominal', 15, 2)->default(0);
            $table->boolean('is_approved')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['unit_number', 'period_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btm_sewa_audits');
    }
};
