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
        Schema::table('master_item_kwitansis', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'harga_satuan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_item_kwitansis', function (Blueprint $table) {
            $table->string('satuan')->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
        });
    }
};
