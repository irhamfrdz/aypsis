<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix: penerima_id should reference penerimas table, not master_pengirim_penerima
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the incorrect foreign key constraint
            $table->dropForeign(['penerima_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            // Re-add with correct reference to penerimas table
            $table->foreign('penerima_id')
                ->references('id')
                ->on('penerimas')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['penerima_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('penerima_id')
                ->references('id')
                ->on('master_pengirim_penerima')
                ->onDelete('set null');
        });
    }
};
