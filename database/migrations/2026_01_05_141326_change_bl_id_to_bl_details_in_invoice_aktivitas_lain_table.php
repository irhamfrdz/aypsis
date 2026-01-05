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
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            // Drop old bl_id column and add bl_details JSON column
            $table->dropColumn('bl_id');
            $table->json('bl_details')->nullable()->after('nomor_voyage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            // Restore bl_id column
            $table->dropColumn('bl_details');
            $table->unsignedBigInteger('bl_id')->nullable()->after('nomor_voyage');
        });
    }
};
