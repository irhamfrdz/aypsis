<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pricelist_ob_antar_gudang', function (Blueprint $table) {
            $table->dropUnique('ob_antar_gudang_pricelist_unique');
            $table->foreignId('gudang_tujuan_id')
                ->nullable()
                ->after('status_service')
                ->constrained('gudangs')
                ->nullOnDelete();

            $table->unique(
                ['size_kontainer', 'status_kontainer', 'status_service', 'gudang_tujuan_id'],
                'ob_antar_gudang_pricelist_destination_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('master_pricelist_ob_antar_gudang', function (Blueprint $table) {
            $table->dropUnique('ob_antar_gudang_pricelist_destination_unique');
            $table->dropConstrainedForeignId('gudang_tujuan_id');
            $table->unique(
                ['size_kontainer', 'status_kontainer', 'status_service'],
                'ob_antar_gudang_pricelist_unique'
            );
        });
    }
};
