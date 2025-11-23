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
        Schema::table('invoices_kontainer_sewa', function (Blueprint $table) {
            $table->foreignId('pranota_id')->nullable()->after('id')->constrained('pranota_tagihan_kontainer_sewa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices_kontainer_sewa', function (Blueprint $table) {
            $table->dropForeign(['pranota_id']);
            $table->dropColumn('pranota_id');
        });
    }
};
