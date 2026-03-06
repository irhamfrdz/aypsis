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
        Schema::table('pembayaran_pranota_vendor_supirs', function (Blueprint $table) {
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0)->after('total_pembayaran');
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2)->default(0)->after('total_tagihan_penyesuaian');
            $table->text('alasan_penyesuaian')->nullable()->after('total_tagihan_setelah_penyesuaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_vendor_supirs', function (Blueprint $table) {
            $table->dropColumn(['total_tagihan_penyesuaian', 'total_tagihan_setelah_penyesuaian', 'alasan_penyesuaian']);
        });
    }
};
