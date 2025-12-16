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
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            // Tambah kolom untuk tracking DP dan kriteria
            $table->unsignedBigInteger('pembayaran_ob_id')->nullable()->after('pranota_ob_ids');
            $table->string('kapal')->nullable()->after('pembayaran_ob_id');
            $table->string('voyage')->nullable()->after('kapal');
            
            // Tambah kolom untuk menyimpan nilai
            $table->decimal('dp_amount', 15, 2)->default(0)->after('voyage');
            $table->decimal('total_biaya_pranota', 15, 2)->default(0)->after('dp_amount');
            
            // Tambah kolom untuk breakdown supir (JSON)
            $table->json('breakdown_supir')->nullable()->after('total_biaya_pranota');
            
            // Foreign key ke pembayaran_obs
            $table->foreign('pembayaran_ob_id')->references('id')->on('pembayaran_obs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['pembayaran_ob_id']);
            
            // Drop columns
            $table->dropColumn([
                'pembayaran_ob_id',
                'kapal',
                'voyage',
                'dp_amount',
                'total_biaya_pranota',
                'breakdown_supir'
            ]);
        });
    }
};
