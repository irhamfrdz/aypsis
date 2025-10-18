<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder komprehensif untuk semua permission yang ada di sistem
     * Berdasarkan analisis routes/web.php dan controller permissions
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Define all permissions used in the system
        $permissions = [
            // ═══════════════════════════════════════════════════════════════════════
            // 👤 USER MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-user-view',
            'master-user-create',
            'master-user-update',
            'master-user-delete',
            'master-user-bulk-manage',

            // ═══════════════════════════════════════════════════════════════════════
            // 👥 KARYAWAN (EMPLOYEE) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-karyawan-view',
            'master-karyawan-create',
            'master-karyawan-update',
            'master-karyawan-delete',
            'master-karyawan-print',
            'master-karyawan-export',
            'master-karyawan-crew-checklist',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 KONTAINER (CONTAINER) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-kontainer-view',
            'master-kontainer-create',
            'master-kontainer-update',
            'master-kontainer-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎯 TUJUAN (DESTINATION) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-tujuan-view',
            'master-tujuan-create',
            'master-tujuan-update',
            'master-tujuan-delete',
            'master-tujuan-export',
            'master-tujuan-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎯 TUJUAN KIRIM (SHIPPING DESTINATION) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-tujuan-kirim-view',
            'master-tujuan-kirim-create',
            'master-tujuan-kirim-update',
            'master-tujuan-kirim-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏗️ KEGIATAN (ACTIVITY) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-kegiatan-view',
            'master-kegiatan-create',
            'master-kegiatan-update',
            'master-kegiatan-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔐 PERMISSION MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-permission-view',
            'master-permission-create',
            'master-permission-update',
            'master-permission-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚗 MOBIL (VEHICLE) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-mobil-view',
            'master-mobil-create',
            'master-mobil-update',
            'master-mobil-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 PRICELIST SEWA KONTAINER PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-pricelist-sewa-kontainer-view',
            'master-pricelist-sewa-kontainer-create',
            'master-pricelist-sewa-kontainer-update',
            'master-pricelist-sewa-kontainer-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎨 PRICELIST CAT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-pricelist-cat-view',
            'master-pricelist-cat-create',
            'master-pricelist-cat-update',
            'master-pricelist-cat-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚪 PRICELIST GATE IN PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-pricelist-gate-in-view',
            'master-pricelist-gate-in-create',
            'master-pricelist-gate-in-update',
            'master-pricelist-gate-in-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏢 DIVISI (DIVISION) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-divisi-view',
            'master-divisi-create',
            'master-divisi-update',
            'master-divisi-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 💸 PAJAK (TAX) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-pajak-view',
            'master-pajak-create',
            'master-pajak-update',
            'master-pajak-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏦 BANK MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-bank-view',
            'master-bank-create',
            'master-bank-update',
            'master-bank-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏢 CABANG (BRANCH) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-cabang-view',
            'master-cabang-create',
            'master-cabang-update',
            'master-cabang-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 📊 COA (CHART OF ACCOUNTS) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-coa-view',
            'master-coa-create',
            'master-coa-update',
            'master-coa-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 PEKERJAAN (JOB) MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-pekerjaan-view',
            'master-pekerjaan-create',
            'master-pekerjaan-update',
            'master-pekerjaan-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 VENDOR BENGKEL (WORKSHOP VENDOR) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-vendor-bengkel-view',
            'master-vendor-bengkel-create',
            'master-vendor-bengkel-update',
            'master-vendor-bengkel-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔢 KODE NOMOR (NUMBER CODE) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-kode-nomor-view',
            'master-kode-nomor-create',
            'master-kode-nomor-update',
            'master-kode-nomor-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 STOCK KONTAINER PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-stock-kontainer-view',
            'master-stock-kontainer-create',
            'master-stock-kontainer-update',
            'master-stock-kontainer-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚢 MASTER KAPAL (SHIP MASTER) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-kapal.view',
            'master-kapal.create',
            'master-kapal.edit',
            'master-kapal.delete',
            'master-kapal.print',
            'master-kapal.export',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏦 TIPE AKUN (ACCOUNT TYPE) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-tipe-akun-view',
            'master-tipe-akun-create',
            'master-tipe-akun-update',
            'master-tipe-akun-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 📋 NOMOR TERAKHIR (LAST NUMBER) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-nomor-terakhir-view',
            'master-nomor-terakhir-create',
            'master-nomor-terakhir-update',
            'master-nomor-terakhir-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 PENGIRIM (SENDER) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-pengirim-view',
            'master-pengirim-create',
            'master-pengirim-update',
            'master-pengirim-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 JENIS BARANG (ITEM TYPE) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-jenis-barang-view',
            'master-jenis-barang-create',
            'master-jenis-barang-update',
            'master-jenis-barang-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 TERM PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'master-term-view',
            'master-term-create',
            'master-term-update',
            'master-term-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏢 VENDOR KONTAINER SEWA PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'vendor-kontainer-sewa-view',
            'vendor-kontainer-sewa-create',
            'vendor-kontainer-sewa-update',
            'vendor-kontainer-sewa-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 📋 ORDER MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'order-view',
            'order-create',
            'order-update',
            'order-delete',
            'order-print',
            'order-export',

            // ═══════════════════════════════════════════════════════════════════════
            // 📄 SURAT JALAN PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'surat-jalan-view',
            'surat-jalan-create',
            'surat-jalan-update',
            'surat-jalan-delete',
            'surat-jalan-print',
            'surat-jalan-export',

            // ═══════════════════════════════════════════════════════════════════════
            // 📋 TANDA TERIMA PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'tanda-terima-view',
            'tanda-terima-create',
            'tanda-terima-update',
            'tanda-terima-delete',
            'tanda-terima-print',
            'tanda-terima-export',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚪 GATE IN PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'gate-in-view',
            'gate-in-create',
            'gate-in-update',
            'gate-in-delete',
            'gate-in-print',
            'gate-in-export',

            // ═══════════════════════════════════════════════════════════════════════
            // 📝 PERMOHONAN (REQUEST) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'permohonan',
            'permohonan-memo-view',
            'permohonan-memo-create',
            'permohonan-memo-update',
            'permohonan-memo-delete',
            'permohonan-memo-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚚 PRANOTA SUPIR (DRIVER INVOICE) PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-supir-view',
            'pranota-supir-create',
            'pranota-supir-update',
            'pranota-supir-delete',
            'pranota-supir-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN PRANOTA SUPIR PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-pranota-supir-view',
            'pembayaran-pranota-supir-create',
            'pembayaran-pranota-supir-update',
            'pembayaran-pranota-supir-delete',
            'pembayaran-pranota-supir-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 📄 PRANOTA SURAT JALAN PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-surat-jalan-view',
            'pranota-surat-jalan-create',
            'pranota-surat-jalan-update',
            'pranota-surat-jalan-delete',
            'pranota-surat-jalan-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 PERBAIKAN KONTAINER PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'tagihan-perbaikan-kontainer-view',
            'tagihan-perbaikan-kontainer-create',
            'tagihan-perbaikan-kontainer-update',
            'tagihan-perbaikan-kontainer-delete',
            'tagihan-perbaikan-kontainer-print',
            'perbaikan-kontainer-update',
            'perbaikan-kontainer-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 PRANOTA PERBAIKAN KONTAINER PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-perbaikan-kontainer-view',
            'pranota-perbaikan-kontainer-create',
            'pranota-perbaikan-kontainer-update',
            'pranota-perbaikan-kontainer-delete',
            'pranota-perbaikan-kontainer-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎨 TAGIHAN CAT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'tagihan-cat-view',
            'tagihan-cat-create',
            'tagihan-cat-update',
            'tagihan-cat-delete',
            'tagihan-cat-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎨 PRANOTA CAT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-cat-view',
            'pranota-cat-create',
            'pranota-cat-update',
            'pranota-cat-delete',
            'pranota-cat-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN PRANOTA CAT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-pranota-cat-view',
            'pembayaran-pranota-cat-create',
            'pembayaran-pranota-cat-update',
            'pembayaran-pranota-cat-delete',
            'pembayaran-pranota-cat-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 PRANOTA KONTAINER SEWA PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-kontainer-sewa-view',
            'pranota-kontainer-sewa-create',
            'pranota-kontainer-sewa-edit',
            'pranota-kontainer-sewa-update',
            'pranota-kontainer-sewa-delete',
            'pranota-kontainer-sewa-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎯 AKTIVITAS LAINNYA PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'aktivitas-lainnya-view',
            'aktivitas-lainnya-create',
            'aktivitas-lainnya-update',
            'aktivitas-lainnya-delete',
            'aktivitas-lainnya-approve',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN AKTIVITAS LAINNYA PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-aktivitas-lainnya-view',
            'pembayaran-aktivitas-lainnya-create',
            'pembayaran-aktivitas-lainnya-update',
            'pembayaran-aktivitas-lainnya-delete',
            'pembayaran-aktivitas-lainnya-print',
            'pembayaran-aktivitas-lainnya-export',
            'pembayaran-aktivitas-lainnya-approve',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 PEMBAYARAN UANG MUKA PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-uang-muka-view',
            'pembayaran-uang-muka-create',
            'pembayaran-uang-muka-edit',
            'pembayaran-uang-muka-update',
            'pembayaran-uang-muka-delete',
            'pembayaran-uang-muka-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 PEMBAYARAN OB PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-ob-view',
            'pembayaran-ob-create',
            'pembayaran-ob-edit',
            'pembayaran-ob-update',
            'pembayaran-ob-delete',
            'pembayaran-ob-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 REALISASI UANG MUKA PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'realisasi-uang-muka-view',
            'realisasi-uang-muka-create',
            'realisasi-uang-muka-edit',
            'realisasi-uang-muka-update',
            'realisasi-uang-muka-delete',
            'realisasi-uang-muka-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 👤 PROFILE MANAGEMENT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'profile-view',
            'profile-edit',
            'profile-delete',

            // ═══════════════════════════════════════════════════════════════════════
            // 📊 AUDIT LOG PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'audit-logs-view',
            'audit-logs-export',

            // ═══════════════════════════════════════════════════════════════════════
            // 📊 DASHBOARD PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'dashboard-view',
            'dashboard-admin',
            'dashboard-operational',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚚 SUPIR DASHBOARD PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'supir-dashboard-view',
            'checkpoint-create',
            'checkpoint-update',

            // ═══════════════════════════════════════════════════════════════════════
            // 📊 REPORT PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'report-tagihan-view',
            'report-tagihan-export',
            'report-pembayaran-view',
            'report-pembayaran-export',
            'report-pembayaran-print',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔐 SPECIAL/ADDITIONAL PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'access-admin-panel',
            'manage-system-settings',
            'bulk-operations',
            'import-export-data',
        ];

        $created = 0;
        $existing = 0;

        // Insert permissions only if they don't exist
        foreach ($permissions as $permission) {
            $existingPermission = Permission::where('name', $permission)->first();

            if (!$existingPermission) {
                Permission::create([
                    'name' => $permission,
                    'description' => ucwords(str_replace('-', ' ', $permission)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("✅ Created permission: {$permission}");
                $created++;
            } else {
                $this->command->comment("⚠️  Permission already exists: {$permission}");
                $existing++;
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("🎉 Permission seeding completed!");
        $this->command->info("📊 Summary:");
        $this->command->info("   - Total permissions processed: " . count($permissions));
        $this->command->info("   - New permissions created: {$created}");
        $this->command->info("   - Existing permissions skipped: {$existing}");
    }
}
