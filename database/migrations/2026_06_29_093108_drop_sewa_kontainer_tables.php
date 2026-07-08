<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('sewa_tagihans');
        Schema::dropIfExists('sewa_invoices');
        Schema::dropIfExists('sewa_transaksis');
        Schema::dropIfExists('sewa_tarifs');
        Schema::dropIfExists('sewa_kontainers');
        Schema::dropIfExists('sewa_ukurans');
        Schema::dropIfExists('sewa_tipes');
        Schema::dropIfExists('sewa_customers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
