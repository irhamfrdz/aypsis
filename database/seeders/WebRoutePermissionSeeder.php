<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebRoutePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            echo "🚀 Creating all permissions from web.php routes...\n";

            // Array lengkap permissions dari web.php
            $permissions = [
                // Basic permissions
                'login', 'logout', 'dashboard', 'dashboard-view', 'dashboard-admin', 
                'dashboard-operational', 'master',
                
                // Master User permissions
                'master-user-view', 'master-user-create', 'master-user-update', 
                'master-user-delete', 'master-user-bulk-manage', 'master-user-print', 
                'master-user-export', 'master-user-import',
                
                // Master Karyawan permissions
                'master-karyawan-view', 'master-karyawan-create', 'master-karyawan-update', 
                'master-karyawan-delete', 'master-karyawan-print', 'master-karyawan-export', 
                'master-karyawan-import', 'master-karyawan-crew-checklist',
                
                // Master Kontainer permissions
                'master-kontainer-view', 'master-kontainer-create', 'master-kontainer-update', 
                'master-kontainer-delete', 'master-kontainer-print', 'master-kontainer-export', 
                'master-kontainer-import',
                
                // Master Tujuan permissions
                'master-tujuan-view', 'master-tujuan-create', 'master-tujuan-update', 
                'master-tujuan-delete', 'master-tujuan-print', 'master-tujuan-export',
                
                // Master Kegiatan permissions
                'master-kegiatan-view', 'master-kegiatan-create', 'master-kegiatan-update', 
                'master-kegiatan-delete', 'master-kegiatan-print', 'master-kegiatan-export',
                
                // Master Permission permissions
                'master-permission-view', 'master-permission-create', 'master-permission-update', 
                'master-permission-delete', 'master-permission-print', 'master-permission-export',
                
                // Master Mobil permissions
                'master-mobil-view', 'master-mobil-create', 'master-mobil-update', 
                'master-mobil-delete', 'master-mobil-print', 'master-mobil-export',
                
                // Master Bank permissions
                'master-bank-view', 'master-bank-create', 'master-bank-update', 
                'master-bank-delete', 'master-bank-destroy', 'master-bank-print', 
                'master-bank-export',
                
                // Master Divisi permissions
                'master-divisi-view', 'master-divisi-create', 'master-divisi-update', 
                'master-divisi-delete', 'master-divisi-print', 'master-divisi-export',
                
                // Master Pajak permissions
                'master-pajak-view', 'master-pajak-create', 'master-pajak-update', 
                'master-pajak-delete', 'master-pajak-destroy', 'master-pajak-print', 
                'master-pajak-export',
                
                // Master Cabang permissions
                'master-cabang-view', 'master-cabang-create', 'master-cabang-update', 
                'master-cabang-delete', 'master-cabang-print', 'master-cabang-export',
                
                // Master COA permissions
                'master-coa-view', 'master-coa-create', 'master-coa-update', 
                'master-coa-delete', 'master-coa-print', 'master-coa-export',
                
                // Master Pekerjaan permissions
                'master-pekerjaan-view', 'master-pekerjaan-create', 'master-pekerjaan-update', 
                'master-pekerjaan-delete', 'master-pekerjaan-destroy', 'master-pekerjaan-print', 
                'master-pekerjaan-export',
                
                // Master Vendor Bengkel permissions
                'master-vendor-bengkel-view', 'master-vendor-bengkel-create', 
                'master-vendor-bengkel-update', 'master-vendor-bengkel-delete', 
                'master-vendor-bengkel-print', 'master-vendor-bengkel-export', 
                'master-vendor-bengkel', 'master-vendor-bengkel.view', 
                'master-vendor-bengkel.create', 'master-vendor-bengkel.update', 
                'master-vendor-bengkel.delete',
                
                // Master Kode Nomor permissions
                'master-kode-nomor-view', 'master-kode-nomor-create', 'master-kode-nomor-update', 
                'master-kode-nomor-delete', 'master-kode-nomor-print', 'master-kode-nomor-export', 
                'master-kode-nomor',
                
                // Master Stock Kontainer permissions
                'master-stock-kontainer-view', 'master-stock-kontainer-create', 
                'master-stock-kontainer-update', 'master-stock-kontainer-delete', 
                'master-stock-kontainer-print', 'master-stock-kontainer-export', 
                'master-stock-kontainer',
                
                // Master Kapal permissions
                'master-kapal-view', 'master-kapal-create', 'master-kapal-edit', 
                'master-kapal-delete', 'master-kapal-print', 'master-kapal-export', 
                'master-kapal', 'master-kapal.view', 'master-kapal.create', 
                'master-kapal.edit', 'master-kapal.delete',
                
                // Master Pelabuhan permissions
                'master-pelabuhan-view', 'master-pelabuhan-create', 'master-pelabuhan-edit', 
                'master-pelabuhan-update', 'master-pelabuhan-delete',
                
                // Master Tipe Akun permissions
                'master-tipe-akun-view', 'master-tipe-akun-create', 'master-tipe-akun-update', 
                'master-tipe-akun-delete', 'master-tipe-akun-destroy', 'master-tipe-akun-print', 
                'master-tipe-akun-export', 'master-tipe-akun',
                
                // Master Nomor Terakhir permissions
                'master-nomor-terakhir-view', 'master-nomor-terakhir-create', 
                'master-nomor-terakhir-update', 'master-nomor-terakhir-delete', 
                'master-nomor-terakhir-print', 'master-nomor-terakhir-export', 
                'master-nomor-terakhir',
                
                // Master Pengirim permissions
                'master-pengirim-view', 'master-pengirim-create', 'master-pengirim-update', 
                'master-pengirim-delete',
                
                // Master Jenis Barang permissions
                'master-jenis-barang-view', 'master-jenis-barang-create', 
                'master-jenis-barang-update', 'master-jenis-barang-delete',
                
                // Master Term permissions
                'master-term-view', 'master-term-create', 'master-term-update', 
                'master-term-delete',
                
                // Master Tujuan Kirim permissions
                'master-tujuan-kirim-view', 'master-tujuan-kirim-create', 
                'master-tujuan-kirim-update', 'master-tujuan-kirim-delete',
                
                // Vendor Kontainer Sewa permissions
                'vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 
                'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-update', 
                'vendor-kontainer-sewa-delete', 'vendor-kontainer-sewa-export', 
                'vendor-kontainer-sewa-print',
                
                // Pergerakan Kapal permissions
                'pergerakan-kapal-view', 'pergerakan-kapal-create', 'pergerakan-kapal-update', 
                'pergerakan-kapal-delete',
                
                // Master Pricelist permissions
                'master-pricelist-sewa-kontainer-view', 'master-pricelist-sewa-kontainer-create', 
                'master-pricelist-sewa-kontainer-update', 'master-pricelist-sewa-kontainer-delete', 
                'master-pricelist-sewa-kontainer-print', 'master-pricelist-sewa-kontainer-export',
                
                // Master Pricelist Cat permissions
                'master-pricelist-cat-view', 'master-pricelist-cat-create', 
                'master-pricelist-cat-update', 'master-pricelist-cat-delete', 
                'master-pricelist-cat-print', 'master-pricelist-cat-export', 
                'master-pricelist-cat',
                
                // Master Pricelist Gate In permissions
                'master-pricelist-gate-in-view', 'master-pricelist-gate-in-create', 
                'master-pricelist-gate-in-update', 'master-pricelist-gate-in-delete',
                
                // Uang Jalan permissions
                'uang-jalan-view', 'uang-jalan-create', 'uang-jalan-update', 
                'uang-jalan-delete', 'uang-jalan-approve', 'uang-jalan-print', 
                'uang-jalan-export', 'uang-jalan-batam.view', 'uang-jalan-batam.create', 
                'uang-jalan-batam.edit', 'uang-jalan-batam.delete',
                // Uang Jalan Bongkaran permissions
                'uang-jalan-bongkaran-view', 'uang-jalan-bongkaran-create', 'uang-jalan-bongkaran-update', 'uang-jalan-bongkaran-delete',
                
                // Order permissions
                'order-view', 'order-create', 'order-update', 'order-delete', 
                'order-print', 'order-export',
                
                // Surat Jalan permissions
                'surat-jalan-view', 'surat-jalan-create', 'surat-jalan-update', 
                'surat-jalan-delete', 'surat-jalan-print', 'surat-jalan-export',
                
                // Surat Jalan Bongkaran permissions
                'surat-jalan-bongkaran-view', 'surat-jalan-bongkaran-create', 
                'surat-jalan-bongkaran-update', 'surat-jalan-bongkaran-delete',
                
                // Pranota permissions
                'pranota-view', 'pranota-create', 'pranota-update', 'pranota-delete', 
                'pranota-print', 'pranota-export', 'pranota-approve', 'pranota',
                
                // Pranota Supir permissions
                'pranota-supir-view', 'pranota-supir-create', 'pranota-supir-update', 
                'pranota-supir-delete', 'pranota-supir-print',
                
                // Pranota Uang Jalan permissions
                'pranota-uang-jalan-view', 'pranota-uang-jalan-create', 
                'pranota-uang-jalan-update', 'pranota-uang-jalan-delete', 
                'pranota-uang-jalan-approve', 'pranota-uang-jalan-print', 
                'pranota-uang-jalan-export',
                // Pranota Uang Jalan Bongkaran permissions
                'pranota-uang-jalan-bongkaran-view', 'pranota-uang-jalan-bongkaran-create', 'pranota-uang-jalan-bongkaran-update', 'pranota-uang-jalan-bongkaran-delete', 'pranota-uang-jalan-bongkaran-approve', 'pranota-uang-jalan-bongkaran-print', 'pranota-uang-jalan-bongkaran-export',
                
                // Pranota Uang Rit permissions
                'pranota-uang-rit-view', 'pranota-uang-rit-create', 'pranota-uang-rit-update', 
                'pranota-uang-rit-delete', 'pranota-uang-rit-approve', 'pranota-uang-rit-mark-paid',
                
                // Pranota Uang Kenek permissions
                'pranota-uang-kenek-view', 'pranota-uang-kenek-create', 
                'pranota-uang-kenek-update', 'pranota-uang-kenek-delete', 
                'pranota-uang-kenek-approve', 'pranota-uang-kenek-mark-paid',
                
                // Pranota Cat permissions
                'pranota-cat-view', 'pranota-cat-create', 'pranota-cat-update', 
                'pranota-cat-delete', 'pranota-cat-print', 'pranota-cat-export', 'pranota-cat',
                
                // Pranota Kontainer Sewa permissions
                'pranota-kontainer-sewa-view', 'pranota-kontainer-sewa-create', 
                'pranota-kontainer-sewa-edit', 'pranota-kontainer-sewa-update', 
                'pranota-kontainer-sewa-delete', 'pranota-kontainer-sewa-print', 
                'pranota-kontainer-sewa-export', 'pranota-kontainer-sewa',
                
                // Pranota Perbaikan Kontainer permissions
                'pranota-perbaikan-kontainer-view', 'pranota-perbaikan-kontainer-create', 
                'pranota-perbaikan-kontainer-update', 'pranota-perbaikan-kontainer-delete', 
                'pranota-perbaikan-kontainer-print', 'pranota-perbaikan-kontainer-export', 
                'pranota-perbaikan-kontainer',
                
                // Pembayaran Pranota Supir permissions
                'pembayaran-pranota-supir-view', 'pembayaran-pranota-supir-create', 
                'pembayaran-pranota-supir-update', 'pembayaran-pranota-supir-delete', 
                'pembayaran-pranota-supir-print',
                
                // Pembayaran Pranota Kontainer permissions
                'pembayaran-pranota-kontainer-view', 'pembayaran-pranota-kontainer-create', 
                'pembayaran-pranota-kontainer-update', 'pembayaran-pranota-kontainer-delete', 
                'pembayaran-pranota-kontainer-print', 'pembayaran-pranota-kontainer-export',
                
                // Pembayaran Pranota Cat permissions
                'pembayaran-pranota-cat-view', 'pembayaran-pranota-cat-create', 
                'pembayaran-pranota-cat-update', 'pembayaran-pranota-cat-delete', 
                'pembayaran-pranota-cat-print', 'pembayaran-pranota-cat-export',
                
                // Pembayaran Pranota Perbaikan Kontainer permissions
                'pembayaran-pranota-perbaikan-kontainer-view', 
                'pembayaran-pranota-perbaikan-kontainer-create', 
                'pembayaran-pranota-perbaikan-kontainer-update', 
                'pembayaran-pranota-perbaikan-kontainer-delete', 
                'pembayaran-pranota-perbaikan-kontainer-print', 
                'pembayaran-pranota-perbaikan-kontainer-export',
                
                // Pembayaran Pranota Surat Jalan permissions
                'pembayaran-pranota-surat-jalan-view', 'pembayaran-pranota-surat-jalan-create', 
                'pembayaran-pranota-surat-jalan-edit', 'pembayaran-pranota-surat-jalan-delete', 
                'pembayaran-pranota-surat-jalan-approve', 'pembayaran-pranota-surat-jalan-print', 
                'pembayaran-pranota-surat-jalan-export',
                
                // Pembayaran Pranota Uang Jalan permissions
                'pembayaran-pranota-uang-jalan-view', 'pembayaran-pranota-uang-jalan-create', 
                'pembayaran-pranota-uang-jalan-edit', 'pembayaran-pranota-uang-jalan-delete',
                
                // Pembayaran Aktivitas Lainnya permissions
                'pembayaran-aktivitas-lainnya-view', 'pembayaran-aktivitas-lainnya-create', 
                'pembayaran-aktivitas-lainnya-update', 'pembayaran-aktivitas-lainnya-delete', 
                'pembayaran-aktivitas-lainnya-export', 'pembayaran-aktivitas-lainnya-print', 
                'pembayaran-aktivitas-lainnya-approve', 'pembayaran-aktivitas-lainnya-reject', 
                'pembayaran-aktivitas-lainnya-generate-nomor', 
                'pembayaran-aktivitas-lainnya-payment-form',
                
                // Pembayaran Uang Muka permissions
                'pembayaran-uang-muka-view', 'pembayaran-uang-muka-create', 
                'pembayaran-uang-muka-edit', 'pembayaran-uang-muka-update', 
                'pembayaran-uang-muka-delete', 'pembayaran-uang-muka-print',
                
                // Pembayaran OB permissions
                'pembayaran-ob-view', 'pembayaran-ob-create', 'pembayaran-ob-edit', 
                'pembayaran-ob-update', 'pembayaran-ob-delete', 'pembayaran-ob-print',
                
                // Realisasi Uang Muka permissions
                'realisasi-uang-muka-view', 'realisasi-uang-muka-create', 
                'realisasi-uang-muka-edit', 'realisasi-uang-muka-update', 
                'realisasi-uang-muka-delete', 'realisasi-uang-muka-print',
                
                // Tanda Terima permissions
                'tanda-terima-view', 'tanda-terima-create', 'tanda-terima-update', 
                'tanda-terima-edit', 'tanda-terima-delete', 'tanda-terima-print', 
                'tanda-terima-export',
                
                // Tanda Terima Tanpa Surat Jalan permissions
                'tanda-terima-tanpa-surat-jalan-view', 'tanda-terima-tanpa-surat-jalan-create', 
                'tanda-terima-tanpa-surat-jalan-update', 'tanda-terima-tanpa-surat-jalan-delete',
                
                // Gate In permissions
                'gate-in-view', 'gate-in-create', 'gate-in-update', 'gate-in-delete', 
                'gate-in-print', 'gate-in-export',
                
                // Tagihan Cat permissions
                'tagihan-cat-view', 'tagihan-cat-create', 'tagihan-cat-update', 
                'tagihan-cat-delete', 'tagihan-cat-print', 'tagihan-cat-export', 
                'tagihan-cat-approve', 'tagihan-cat',
                
                // Tagihan Kontainer permissions
                'tagihan-kontainer-view', 'tagihan-kontainer-print', 'tagihan-kontainer-export', 
                'tagihan-kontainer-sewa', 'tagihan-kontainer-sewa-view', 
                'tagihan-kontainer-sewa-create', 'tagihan-kontainer-sewa-update', 
                'tagihan-kontainer-sewa-delete', 'tagihan-kontainer-sewa-print', 
                'tagihan-kontainer-sewa-index', 'tagihan-kontainer-sewa-destroy',
                
                // Tagihan Perbaikan Kontainer permissions
                'tagihan-perbaikan-kontainer-view', 'tagihan-perbaikan-kontainer-create', 
                'tagihan-perbaikan-kontainer-update', 'tagihan-perbaikan-kontainer-delete', 
                'tagihan-perbaikan-kontainer-print', 'tagihan-perbaikan-kontainer-export', 
                'tagihan-perbaikan-kontainer-approve',
                
                // Perbaikan Kontainer permissions
                'perbaikan-kontainer-view', 'perbaikan-kontainer-create', 
                'perbaikan-kontainer-update', 'perbaikan-kontainer-delete', 
                'perbaikan-kontainer-print', 'perbaikan-kontainer-export', 
                'perbaikan-kontainer.view', 'perbaikan-kontainer.create', 
                'perbaikan-kontainer.update', 'perbaikan-kontainer.delete',
                
                // Aktivitas Lainnya permissions
                'aktivitas-lainnya-view', 'aktivitas-lainnya-create', 
                'aktivitas-lainnya-update', 'aktivitas-lainnya-delete', 
                'aktivitas-lainnya-approve',
                
                // Supir permissions
                'supir', 'supir-view', 'supir-create', 'supir-update', 'supir-delete', 
                'supir-checkpoint', 'supir-dashboard-view',
                
                // Checkpoint permissions
                'checkpoint-create', 'checkpoint-update',
                
                // Approval permissions
                'approval', 'approval-view', 'approval-update', 'approval-delete', 
                'approval-approve', 'approval-print', 'approval-export', 'approval-dashboard',
                
                // Approval Surat Jalan permissions
                'approval-surat-jalan-view', 'approval-surat-jalan-approve',
                
                // Approval Tugas permissions
                'approval-tugas-1', 'approval-tugas-1.view', 'approval-tugas-1.approve', 
                'approval-tugas-2', 'approval-tugas-2.view', 'approval-tugas-2.approve', 
                'surat-jalan-approval-dashboard',
                
                // User Approval permissions
                'user-approval-view', 'user-approval-create', 'user-approval-update', 
                'user-approval-delete', 'user-approval-print', 'user-approval-export', 
                'user-approval-approve', 'user-approval-reject',
                
                // Profile permissions
                'profile-view', 'profile-update', 'profile-delete', 'profile.show', 
                'profile.edit', 'profile.update', 'profile.destroy',
                
                // Admin permissions
                'admin-view', 'admin-create', 'admin-update', 'admin-delete', 'admin-debug', 
                'admin.debug', 'admin.features', 'admin.user-approval', 
                'admin.user-approval.create', 'admin.user-approval.update', 
                'admin.user-approval.delete',
                
                // Permohonan permissions
                'permohonan', 'permohonan-memo-view', 'permohonan-memo-create', 
                'permohonan-memo-update', 'permohonan-memo-delete', 'permohonan-memo-print', 
                'permohonan-memo-export', 'permohonan-memo',
                
                // BL permissions
                'bl-view', 'bl-create', 'bl-edit', 'bl-update', 'bl-delete',
                
                // Prospek permissions
                'prospek-view', 'prospek-edit',
                
                // Prospek Kapal permissions
                'prospek-kapal-view', 'prospek-kapal-create', 'prospek-kapal-update', 
                'prospek-kapal-delete',
                
                // Audit Log permissions
                'audit-logs-view', 'audit-logs-export', 'audit-log-view', 'audit-log-export',
                
                // Report permissions
                'report-tagihan-view', 'report-tagihan-export', 'report-pembayaran-view', 
                'report-pembayaran-export', 'report-pembayaran-print',
                
                // System permissions
                'access-admin-panel', 'manage-system-settings', 'bulk-operations', 
                'import-export-data'
            ];

            // Counter untuk tracking
            $createdCount = 0;
            $existingCount = 0;

            // Loop membuat permissions dengan format custom system - check satu per satu untuk menghindari duplikasi
            foreach ($permissions as $permission) {
                $existing = DB::table('permissions')->where('name', $permission)->first();
                if (!$existing) {
                    DB::table('permissions')->insert([
                        'name' => $permission,
                        'description' => ucwords(str_replace('-', ' ', $permission)) . ' permission',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $createdCount++;
                    echo "✅ Created permission: {$permission}\n";
                } else {
                    $existingCount++;
                    echo "⚠️  Permission already exists: {$permission}\n";
                }
            }

            // Assign semua permissions ke admin user
            $adminUsers = DB::table('users')
                ->where('username', 'admin')
                ->orWhere('id', 1)
                ->get();

            if ($adminUsers->isEmpty()) {
                echo "❌ No admin users found!\n";
                DB::rollBack();
                return;
            }

            foreach ($adminUsers as $admin) {
                echo "👤 Processing admin user: {$admin->username} (ID: {$admin->id})\n";
                
                // Get all permission IDs
                $allPermissions = DB::table('permissions')->pluck('id', 'name');
                $assignedCount = 0;
                
                foreach ($allPermissions as $permissionName => $permissionId) {
                    $existing = DB::table('user_permissions')
                        ->where('user_id', $admin->id)
                        ->where('permission_id', $permissionId)
                        ->first();

                    if (!$existing) {
                        DB::table('user_permissions')->insert([
                            'user_id' => $admin->id,
                            'permission_id' => $permissionId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $assignedCount++;
                    }
                }
                
                echo "✅ Assigned {$assignedCount} new permissions to {$admin->username}\n";
            }

            DB::commit();

            echo "\n🎉 SEEDING COMPLETED SUCCESSFULLY!\n";
            echo "📊 Permissions created: {$createdCount}\n";
            echo "📋 Permissions already existed: {$existingCount}\n";
            echo "👥 Admin users updated: " . $adminUsers->count() . "\n";

        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ ERROR: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}