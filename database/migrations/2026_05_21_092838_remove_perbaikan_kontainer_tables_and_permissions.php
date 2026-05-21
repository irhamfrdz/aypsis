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
        Schema::dropIfExists('pembayaran_pranota_perbaikan_kontainer_items');
        Schema::dropIfExists('pembayaran_pranota_perbaikan_kontainers');
        Schema::dropIfExists('pranota_perbaikan_kontainer_items');
        Schema::dropIfExists('pranota_perbaikan_kontainers');
        Schema::dropIfExists('perbaikan_kontainers');

        $permissions = [
            'pranota-perbaikan-kontainer-view',
            'pranota-perbaikan-kontainer-create',
            'pranota-perbaikan-kontainer-update',
            'pranota-perbaikan-kontainer-delete',
            'pranota-perbaikan-kontainer-print',
            'tagihan-perbaikan-kontainer-view',
            'tagihan-perbaikan-kontainer-create',
            'tagihan-perbaikan-kontainer-update',
            'tagihan-perbaikan-kontainer-delete',
            'tagihan-perbaikan-kontainer-print',
            'perbaikan-kontainer-view',
            'perbaikan-kontainer-update',
            'perbaikan-kontainer-delete',
            'pembayaran-pranota-perbaikan-kontainer-view',
            'pembayaran-pranota-perbaikan-kontainer-create',
            'pembayaran-pranota-perbaikan-kontainer-update',
            'pembayaran-pranota-perbaikan-kontainer-delete',
            'pembayaran-pranota-perbaikan-kontainer-print',
        ];
        \Illuminate\Support\Facades\DB::table('permissions')->whereIn('name', $permissions)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback is possible as tables are dropped
    }
};
