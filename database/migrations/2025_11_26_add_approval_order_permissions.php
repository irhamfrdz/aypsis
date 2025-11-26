<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Schema;
use App\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define approval-order permissions
        $permissions = [
            [
                'name' => 'approval-order-view',
                'description' => 'Melihat halaman approval order'
            ],
            [
                'name' => 'approval-order-create', 
                'description' => 'Menambah term pembayaran order'
            ],
            [
                'name' => 'approval-order-update',
                'description' => 'Mengedit term pembayaran order'
            ],
            [
                'name' => 'approval-order-delete',
                'description' => 'Menghapus term pembayaran order'
            ],
            [
                'name' => 'approval-order-approve',
                'description' => 'Menyetujui approval order'
            ],
            [
                'name' => 'approval-order-reject',
                'description' => 'Menolak approval order'
            ],
            [
                'name' => 'approval-order-print',
                'description' => 'Mencetak dokumen approval order'
            ],
            [
                'name' => 'approval-order-export',
                'description' => 'Export data approval order'
            ]
        ];

        foreach ($permissions as $permissionData) {
            $existing = Permission::where('name', $permissionData['name'])->first();
            
            if (!$existing) {
                Permission::create($permissionData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove approval-order permissions
        $permissionNames = [
            'approval-order-view',
            'approval-order-create',
            'approval-order-update',
            'approval-order-delete',
            'approval-order-approve',
            'approval-order-reject',
            'approval-order-print',
            'approval-order-export'
        ];

        foreach ($permissionNames as $name) {
            Permission::where('name', $name)->delete();
        }
    }
};