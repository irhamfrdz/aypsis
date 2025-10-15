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
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Add vendor invoice information columns after adjustment_note
            $table->string('invoice_vendor', 100)->nullable()->after('adjustment_note')->comment('Nomor invoice dari vendor');
            $table->date('tanggal_vendor')->nullable()->after('invoice_vendor')->comment('Tanggal invoice vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->dropColumn(['invoice_vendor', 'tanggal_vendor']);
        });
    }
};
