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
        Schema::table('pranota_ongkos_truks', function (Blueprint $table) {
            $table->dropForeign(['supir_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['supir_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_ongkos_truks', function (Blueprint $table) {
            $table->unsignedBigInteger('supir_id')->nullable()->after('tanggal_pranota');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('supir_id');
            
            $table->foreign('supir_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('vendor_supirs')->onDelete('set null');
        });
    }
};
