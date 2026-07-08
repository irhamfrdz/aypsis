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
        Schema::dropIfExists('sk_invoice_grup_tagihans');
        Schema::dropIfExists('sk_invoice_grups');
        Schema::dropIfExists('sk_tagihan_bulans');
        Schema::dropIfExists('sk_sewas');
        Schema::dropIfExists('sk_tarif_sewas');
        Schema::dropIfExists('sk_kontainers');
        Schema::dropIfExists('sk_ukuran_kontainers');
        Schema::dropIfExists('sk_tipe_kontainers');

        if (Schema::hasColumn('vendor_kontainer_sewas', 'status_aktif')) {
            Schema::table('vendor_kontainer_sewas', function (Blueprint $table) {
                $table->dropColumn('status_aktif');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-creating tables is complex, keeping this empty or minimal.
    }
};
