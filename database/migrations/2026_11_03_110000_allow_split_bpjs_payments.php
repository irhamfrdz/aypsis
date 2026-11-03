<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_pranota_bpjs_items', function (Blueprint $table) {
            $table->index('pranota_bpjs_header_id', 'bpjs_payment_header_index');
        });
        Schema::table('pembayaran_pranota_bpjs_items', function (Blueprint $table) {
            $table->dropUnique(['pranota_bpjs_header_id']);
            $table->unique(['pembayaran_pranota_bpjs_id', 'pranota_bpjs_header_id'], 'bpjs_payment_header_unique');
        });
        Schema::create('pembayaran_pranota_bpjs_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_bpjs_id')->constrained('pembayaran_pranota_bpjs');
            $table->foreignId('pranota_bpjs_header_id')->constrained('pranota_bpjs_headers');
            $table->foreignId('pranota_bpjs_detail_id')->constrained('pranota_bpjs_details');
            $table->string('komponen', 20);
            $table->string('tipe');
            $table->decimal('subtotal', 15, 2);
            $table->unique(['pranota_bpjs_detail_id', 'komponen'], 'bpjs_paid_component_unique');
        });
    }

    public function down(): void
    {
        $hasSplitPayments = \Illuminate\Support\Facades\DB::table('pembayaran_pranota_bpjs_items')
            ->select('pranota_bpjs_header_id')->groupBy('pranota_bpjs_header_id')->havingRaw('COUNT(*) > 1')->exists();
        if ($hasSplitPayments) {
            throw new RuntimeException('Rollback ditolak karena terdapat beberapa pembayaran pada pranota yang sama.');
        }
        Schema::table('pembayaran_pranota_bpjs_items', function (Blueprint $table) {
            $table->unique('pranota_bpjs_header_id');
            $table->dropUnique('bpjs_payment_header_unique');
            $table->dropIndex('bpjs_payment_header_index');
        });
        Schema::dropIfExists('pembayaran_pranota_bpjs_parts');
    }
};
