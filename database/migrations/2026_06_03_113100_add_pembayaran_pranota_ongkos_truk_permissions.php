<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'pembayaran-pranota-ongkos-truk-view',
            'pembayaran-pranota-ongkos-truk-create',
            'pembayaran-pranota-ongkos-truk-edit',
            'pembayaran-pranota-ongkos-truk-delete',
        ];

        foreach ($permissions as $name) {
            \App\Models\Permission::firstOrCreate(['name' => $name]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'pembayaran-pranota-ongkos-truk-view',
            'pembayaran-pranota-ongkos-truk-create',
            'pembayaran-pranota-ongkos-truk-edit',
            'pembayaran-pranota-ongkos-truk-delete',
        ];

        \App\Models\Permission::whereIn('name', $permissions)->delete();
    }
};
