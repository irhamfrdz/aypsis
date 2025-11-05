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
        Schema::table('orders', function (Blueprint $table) {
            // Add recipient information fields
            $table->string('penerima')->nullable()->after('tujuan_ambil');
            $table->text('alamat_penerima')->nullable()->after('penerima');
            $table->string('kontak_penerima')->nullable()->after('alamat_penerima');
            
            // Add unit field for goods measurement
            $table->string('satuan')->nullable()->after('tanggal_pickup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove the added fields
            $table->dropColumn(['penerima', 'alamat_penerima', 'kontak_penerima', 'satuan']);
        });
    }
};
