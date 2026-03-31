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
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('period_name');
            $table->decimal('aypsis_nominal', 15, 2)->default(0);
            $table->decimal('vendor_nominal', 15, 2)->default(0);
            $table->boolean('is_approved')->default(false);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('pranota_id')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('btm_sewa_transactions')->onDelete('set null');
            $table->foreign('pranota_id')->references('id')->on('btm_sewa_pranotas')->onDelete('set null');
            $table->index(['unit_number', 'period_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btm_sewa_audits');
    }
};
