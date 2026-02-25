<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'pembayaran-pranota-invoice-vendor-supir-view',
            'pembayaran-pranota-invoice-vendor-supir-create',
            'pembayaran-pranota-invoice-vendor-supir-delete',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'pembayaran-pranota-invoice-vendor-supir-view',
            'pembayaran-pranota-invoice-vendor-supir-create',
            'pembayaran-pranota-invoice-vendor-supir-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
