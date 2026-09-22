<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_biaya_kapal_items', function (Blueprint $table) {
            // Existing payments retain their full-payment meaning.
            $table->string('payment_mode', 20)->default('lunas');
            $table->unsignedBigInteger('dp_item_id')->nullable();
            $table->decimal('nilai_tagihan', 15, 2)->nullable();
            $table->decimal('sisa_setelah_bayar', 15, 2)->nullable();
            $table->foreign('dp_item_id', 'pbk_item_dp_fk')
                ->references('id')->on('pembayaran_biaya_kapal_items')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_biaya_kapal_items', function (Blueprint $table) {
            $table->dropForeign('pbk_item_dp_fk');
            $table->dropColumn(['payment_mode', 'dp_item_id', 'nilai_tagihan', 'sisa_setelah_bayar']);
        });
    }
};
