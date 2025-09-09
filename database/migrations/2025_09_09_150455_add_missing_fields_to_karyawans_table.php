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
        Schema::table('karyawans', function (Blueprint $table) {
            // Add missing fields that exist in controller but not in original migration
            $table->date('tanggal_masuk_sebelumnya')->nullable()->after('tanggal_berhenti');
            $table->date('tanggal_berhenti_sebelumnya')->nullable()->after('tanggal_masuk_sebelumnya');
            $table->text('catatan')->nullable()->after('tanggal_berhenti_sebelumnya');
            $table->string('bank_cabang')->nullable()->after('nama_bank');
            $table->string('no_ketenagakerjaan')->nullable()->after('jkn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Drop the added fields in reverse order
            $table->dropColumn([
                'no_ketenagakerjaan',
                'bank_cabang', 
                'catatan',
                'tanggal_berhenti_sebelumnya',
                'tanggal_masuk_sebelumnya'
            ]);
        });
    }
};
