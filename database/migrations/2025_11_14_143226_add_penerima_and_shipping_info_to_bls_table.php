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
        Schema::table('bls', function (Blueprint $table) {
            $table->string('penerima')->nullable()->after('nama_barang');
            $table->text('alamat_pengiriman')->nullable()->after('penerima');
            $table->string('contact_person')->nullable()->after('alamat_pengiriman');
            $table->string('satuan')->nullable()->after('volume');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropColumn(['penerima', 'alamat_pengiriman', 'contact_person', 'satuan']);
        });
    }
};
