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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('pembayaran_aktivitas_lainnya_supirs');
        Schema::dropIfExists('pembayaran_aktivitas_lainnya_items');
        Schema::dropIfExists('pembayaran_aktivitas_lainnya');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to recreate - backup exists
    }
};
