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
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('pembayaran_aktivitas_lains', 'sub_jenis_kendaraan')) {
                $table->string('sub_jenis_kendaraan')->nullable()->after('jenis_aktivitas');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lains', 'nomor_polisi')) {
                $table->string('nomor_polisi')->nullable()->after('sub_jenis_kendaraan');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lains', 'nomor_voyage')) {
                $table->string('nomor_voyage')->nullable()->after('nomor_polisi');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lains', 'penerima')) {
                $table->string('penerima')->nullable()->after('nomor_voyage');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lains', 'akun_bank_id')) {
                $table->unsignedBigInteger('akun_bank_id')->nullable()->after('akun_coa_id');
                
                // Add foreign key constraint for akun_bank_id
                $table->foreign('akun_bank_id')->references('id')->on('akun_coa')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->dropForeign(['akun_bank_id']);
            $table->dropColumn([
                'sub_jenis_kendaraan',
                'nomor_polisi', 
                'nomor_voyage',
                'penerima',
                'akun_bank_id'
            ]);
        });
    }
};
