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
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            // Drop foreign key constraint jika ada
            if (Schema::hasColumn('pembayaran_obs', 'pembayaran_dp_ob_id')) {
                $table->dropForeign(['pembayaran_dp_ob_id']);
                $table->dropColumn('pembayaran_dp_ob_id');
            }

            // Tambah kolom baru untuk referensi ke pembayaran_uang_muka
            $table->foreignId('pembayaran_uang_muka_id')->nullable()->constrained('pembayaran_uang_muka')->onDelete('set null');

            // Update kolom untuk konsistensi penamaan
            $table->decimal('uang_muka_amount', 15, 2)->default(0)->after('subtotal_pembayaran');
            $table->dropColumn('dp_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            // Kembalikan ke struktur lama
            $table->dropForeign(['pembayaran_uang_muka_id']);
            $table->dropColumn(['pembayaran_uang_muka_id', 'uang_muka_amount']);

            // Restore old columns
            $table->foreignId('pembayaran_dp_ob_id')->nullable()->constrained('pembayaran_dp_obs')->onDelete('set null');
            $table->decimal('dp_amount', 15, 2)->default(0);
        });
    }
};
