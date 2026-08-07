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
        Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
            // Drop old foreign key if it exists
            $table->dropForeign('pranota_lembur_karyawans_pranota_lembur_id_foreign');
            
            // Add the new one pointing to the headers table
            $table->foreign('pranota_lembur_karyawan_header_id', 'fk_pranota_lembur_karyawan_header')
                  ->references('id')
                  ->on('pranota_lembur_karyawan_headers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
            $table->dropForeign('fk_pranota_lembur_karyawan_header');
            
            $table->foreign('pranota_lembur_karyawan_header_id', 'pranota_lembur_karyawans_pranota_lembur_id_foreign')
                  ->references('id')
                  ->on('pranota_lemburs')
                  ->onDelete('cascade');
        });
    }
};
