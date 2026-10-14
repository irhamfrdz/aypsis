<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'pembayaran_pranota_supir_pranota_supir',
            'pembayaran_pranota_supir',
            'pranota_permohonan',
            'pranota_supirs',
            'permohonan_kontainers',
            'checkpoints',
            'permohonans',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->where(function ($query) {
                $query->whereIn('name', [
                    'permohonan', 'permohonan.approve', 'permohonan-approve',
                    'permohonan-create', 'permohonan-delete', 'permohonan-destroy',
                    'permohonan-edit', 'permohonan-export', 'permohonan-import',
                    'permohonan-index', 'permohonan-print', 'permohonan-show',
                    'permohonan-store', 'permohonan-update', 'permohonan-view',
                    'permohonan-memo',
                    'approval', 'approval-view', 'approval-approve',
                    'approval-create', 'approval-delete', 'approval-export',
                    'approval-mass_process', 'approval-print', 'approval-riwayat',
                    'approval-store', 'approval-update', 'approval-dashboard',
                ])
                    ->orWhere('name', 'like', 'permohonan-memo-%')
                    ->orWhere('name', 'pranota-supir')
                    ->orWhere('name', 'like', 'pranota-supir-%')
                    ->orWhere('name', 'pembayaran-pranota-supir')
                    ->orWhere('name', 'like', 'pembayaran-pranota-supir-%')
                    ->orWhere('name', 'like', 'approval-tugas-1%')
                    ->orWhere('name', 'like', 'approval-tugas-2%');
            })
            ->pluck('id');

        foreach (['user_permissions', 'permission_role'] as $pivot) {
            if (Schema::hasTable($pivot)) {
                DB::table($pivot)->whereIn('permission_id', $permissionIds)->delete();
            }
        }
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }

    public function down(): void
    {
        // Data in this retired workflow cannot be reconstructed.
    }
};
