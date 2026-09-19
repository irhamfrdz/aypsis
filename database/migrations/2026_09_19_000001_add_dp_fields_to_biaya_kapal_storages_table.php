<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_storages', function (Blueprint $table) {
            $table->string('payment_mode', 20)->default('lunas')->after('notes_adjustment');
            $table->foreignId('dp_storage_id')->nullable()->constrained('biaya_kapal_storages')->nullOnDelete()->after('payment_mode');
            $table->decimal('nilai_tagihan', 15, 2)->default(0)->after('dp_storage_id');
            $table->decimal('nominal_dibayar', 15, 2)->default(0)->after('nilai_tagihan');
            $table->decimal('sisa_pembayaran', 15, 2)->default(0)->after('nominal_dibayar');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_storages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dp_storage_id');
            $table->dropColumn(['payment_mode', 'nilai_tagihan', 'nominal_dibayar', 'sisa_pembayaran']);
        });
    }
};
