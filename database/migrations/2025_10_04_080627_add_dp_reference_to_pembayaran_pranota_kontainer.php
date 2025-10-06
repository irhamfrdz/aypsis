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
        Schema::table('pembayaran_pranota_kontainer', function (Blueprint $table) {
            $table->unsignedBigInteger('dp_payment_id')->nullable()->after('keterangan');
            $table->decimal('dp_amount', 15, 2)->nullable()->after('dp_payment_id');

            // Add foreign key constraint
            $table->foreign('dp_payment_id')->references('id')->on('pembayaran_aktivitas_lainnya')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_kontainer', function (Blueprint $table) {
            $table->dropForeign(['dp_payment_id']);
            $table->dropColumn(['dp_payment_id', 'dp_amount']);
        });
    }
};
