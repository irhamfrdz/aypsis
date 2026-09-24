<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add the permissions used by the Permintaan Amprahan menu.
     */
    public function up(): void
    {
        $permissions = [
            'permohonan-amprahan-view' => 'Melihat permohonan amprahan',
            'permohonan-amprahan-create' => 'Membuat permohonan amprahan',
            'permohonan-amprahan-update' => 'Mengubah permohonan amprahan',
            'permohonan-amprahan-delete' => 'Menghapus permohonan amprahan',
            'permohonan-amprahan-approve' => 'Menyetujui permohonan amprahan',
            'permohonan-amprahan-print' => 'Mencetak permohonan amprahan',
            'permohonan-amprahan-export' => 'Mengekspor permohonan amprahan',
        ];

        foreach ($permissions as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Remove the permissions added for Permintaan Amprahan.
     */
    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('name', [
                'permohonan-amprahan-view',
                'permohonan-amprahan-create',
                'permohonan-amprahan-update',
                'permohonan-amprahan-delete',
                'permohonan-amprahan-approve',
                'permohonan-amprahan-print',
                'permohonan-amprahan-export',
            ])
            ->delete();
    }
};
