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
            // Add missing fields for enhanced functionality
            $table->string('sub_jenis_kendaraan')->nullable()->after('jenis_aktivitas');
            $table->string('nomor_polisi')->nullable()->after('sub_jenis_kendaraan');
            $table->string('nomor_voyage')->nullable()->after('nomor_polisi');
            $table->string('penerima')->nullable()->after('nomor_voyage');
            $table->bigInteger('akun_bank_id')->unsigned()->nullable()->after('akun_coa_id');
            
            // Add foreign key for akun_bank_id
            $table->foreign('akun_bank_id')->references('id')->on('akun_coa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['akun_bank_id']);
            
            // Drop columns
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
