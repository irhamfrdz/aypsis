<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeederComprehensive extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder komprehensif untuk semua permission yang digunakan dalam sistem AYPSIS
     * Berdasarkan analisis dari routes/web.php dan UserController.php
     *
     * @return void
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Truncate permissions table to start fresh
        DB::table('permissions')->truncate();

        // Re-enable foreign key checks
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Define all permissions organized by module
        $permissions = [
            // ================================================================
            // 🏠 SYSTEM & DASHBOARD PERMISSIONS
            // ================================================================
            [
                'name' => 'dashboard',
                'description' => 'Akses ke dashboard utama sistem'
            ],

            // ================================================================
            // 👥 MASTER USER PERMISSIONS
            // ================================================================
            [
                'name' => 'master-user-view',
                'description' => 'Lihat daftar dan detail user'
            ],
            [
                'name' => 'master-user-create',
                'description' => 'Tambah user baru'
            ],
            [
                'name' => 'master-user-update',
                'description' => 'Edit dan update user'
            ],
            [
                'name' => 'master-user-delete',
                'description' => 'Hapus user'
            ],
            [
                'name' => 'master-user-bulk-manage',
                'description' => 'Operasi bulk untuk user (bulk delete, bulk update)'
            ],

            // ================================================================
            // 👤 MASTER KARYAWAN PERMISSIONS
            // ================================================================
            [
                'name' => 'master-karyawan-view',
                'description' => 'Lihat daftar dan detail karyawan'
            ],
            [
                'name' => 'master-karyawan-create',
                'description' => 'Tambah karyawan baru'
            ],
            [
                'name' => 'master-karyawan-update',
                'description' => 'Edit dan update karyawan'
            ],
            [
                'name' => 'master-karyawan-delete',
                'description' => 'Hapus karyawan'
            ],
            [
                'name' => 'master-karyawan-print',
                'description' => 'Cetak data karyawan'
            ],
            [
                'name' => 'master-karyawan-export',
                'description' => 'Export data karyawan ke Excel/CSV'
            ],
            [
                'name' => 'master-karyawan-template',
                'description' => 'Download template import karyawan'
            ],
            [
                'name' => 'master-karyawan-crew-checklist',
                'description' => 'Akses crew checklist untuk karyawan'
            ],

            // ================================================================
            // 📦 MASTER KONTAINER PERMISSIONS
            // ================================================================
            [
                'name' => 'master-kontainer-view',
                'description' => 'Lihat daftar dan detail kontainer'
            ],
            [
                'name' => 'master-kontainer-create',
                'description' => 'Tambah kontainer baru'
            ],
            [
                'name' => 'master-kontainer-update',
                'description' => 'Edit dan update kontainer'
            ],
            [
                'name' => 'master-kontainer-delete',
                'description' => 'Hapus kontainer'
            ],

            // ================================================================
            // 📊 MASTER STOCK KONTAINER PERMISSIONS
            // ================================================================
            [
                'name' => 'master-stock-kontainer-view',
                'description' => 'Lihat stock kontainer'
            ],
            [
                'name' => 'master-stock-kontainer-create',
                'description' => 'Tambah stock kontainer'
            ],
            [
                'name' => 'master-stock-kontainer-update',
                'description' => 'Update stock kontainer'
            ],
            [
                'name' => 'master-stock-kontainer-delete',
                'description' => 'Hapus stock kontainer'
            ],

            // ================================================================
            // 🏢 MASTER DIVISI PERMISSIONS
            // ================================================================
            [
                'name' => 'master-divisi-view',
                'description' => 'Lihat daftar divisi'
            ],
            [
                'name' => 'master-divisi-create',
                'description' => 'Tambah divisi baru'
            ],
            [
                'name' => 'master-divisi-update',
                'description' => 'Edit divisi'
            ],
            [
                'name' => 'master-divisi-delete',
                'description' => 'Hapus divisi'
            ],

            // ================================================================
            // 💼 MASTER PEKERJAAN PERMISSIONS
            // ================================================================
            [
                'name' => 'master-pekerjaan-view',
                'description' => 'Lihat daftar pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan-create',
                'description' => 'Tambah pekerjaan baru'
            ],
            [
                'name' => 'master-pekerjaan-update',
                'description' => 'Edit pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan-delete',
                'description' => 'Hapus pekerjaan'
            ],

            // ================================================================
            // 🏦 MASTER BANK PERMISSIONS
            // ================================================================
            [
                'name' => 'master-bank-view',
                'description' => 'Lihat daftar bank'
            ],
            [
                'name' => 'master-bank-create',
                'description' => 'Tambah bank baru'
            ],
            [
                'name' => 'master-bank-update',
                'description' => 'Edit bank'
            ],
            [
                'name' => 'master-bank-delete',
                'description' => 'Hapus bank'
            ],

            // ================================================================
            // 💰 MASTER PAJAK PERMISSIONS
            // ================================================================
            [
                'name' => 'master-pajak-view',
                'description' => 'Lihat daftar pajak'
            ],
            [
                'name' => 'master-pajak-create',
                'description' => 'Tambah pajak baru'
            ],
            [
                'name' => 'master-pajak-update',
                'description' => 'Edit pajak'
            ],
            [
                'name' => 'master-pajak-delete',
                'description' => 'Hapus pajak'
            ],

            // ================================================================
            // 🏪 MASTER CABANG PERMISSIONS
            // ================================================================
            [
                'name' => 'master-cabang-view',
                'description' => 'Lihat daftar cabang'
            ],

            // ================================================================
            // 📑 MASTER COA PERMISSIONS
            // ================================================================
            [
                'name' => 'master-coa-view',
                'description' => 'Lihat Chart of Accounts'
            ],
            [
                'name' => 'master-coa-create',
                'description' => 'Tambah COA baru'
            ],

            // ================================================================
            // 🔧 MASTER VENDOR BENGKEL PERMISSIONS
            // ================================================================
            [
                'name' => 'master-vendor-bengkel-view',
                'description' => 'Lihat daftar vendor bengkel'
            ],
            [
                'name' => 'master-vendor-bengkel-create',
                'description' => 'Tambah vendor bengkel baru'
            ],
            [
                'name' => 'master-vendor-bengkel-update',
                'description' => 'Edit vendor bengkel'
            ],
            [
                'name' => 'master-vendor-bengkel-delete',
                'description' => 'Hapus vendor bengkel'
            ],

            // ================================================================
            // 🔢 MASTER KODE NOMOR PERMISSIONS
            // ================================================================
            [
                'name' => 'master-kode-nomor-view',
                'description' => 'Lihat kode nomor'
            ],

            // ================================================================
            // 📋 MASTER NOMOR TERAKHIR PERMISSIONS
            // ================================================================
            [
                'name' => 'master-nomor-terakhir-view',
                'description' => 'Lihat nomor terakhir'
            ],

            // ================================================================
            // 🏦 MASTER TIPE AKUN PERMISSIONS
            // ================================================================
            [
                'name' => 'master-tipe-akun-view',
                'description' => 'Lihat tipe akun'
            ],

            // ================================================================
            // 📍 MASTER TUJUAN PERMISSIONS
            // ================================================================
            [
                'name' => 'master-tujuan-view',
                'description' => 'Lihat daftar tujuan'
            ],

            // ================================================================
            // 🎯 MASTER KEGIATAN PERMISSIONS
            // ================================================================
            [
                'name' => 'master-kegiatan-view',
                'description' => 'Lihat daftar kegiatan'
            ],
            [
                'name' => 'master-kegiatan-create',
                'description' => 'Tambah kegiatan baru'
            ],
            [
                'name' => 'master-kegiatan-update',
                'description' => 'Edit kegiatan'
            ],
            [
                'name' => 'master-kegiatan-delete',
                'description' => 'Hapus kegiatan'
            ],

            // ================================================================
            // 🔐 MASTER PERMISSION PERMISSIONS
            // ================================================================
            [
                'name' => 'master-permission-view',
                'description' => 'Lihat daftar permission'
            ],
            [
                'name' => 'master-permission-create',
                'description' => 'Tambah permission baru'
            ],
            [
                'name' => 'master-permission-update',
                'description' => 'Edit permission'
            ],
            [
                'name' => 'master-permission-delete',
                'description' => 'Hapus permission'
            ],

            // ================================================================
            // 🚗 MASTER MOBIL PERMISSIONS
            // ================================================================
            [
                'name' => 'master-mobil-view',
                'description' => 'Lihat daftar mobil'
            ],
            [
                'name' => 'master-mobil-create',
                'description' => 'Tambah mobil baru'
            ],
            [
                'name' => 'master-mobil-update',
                'description' => 'Edit mobil'
            ],
            [
                'name' => 'master-mobil-delete',
                'description' => 'Hapus mobil'
            ],

            // ================================================================
            // 💵 MASTER PRICELIST SEWA KONTAINER PERMISSIONS
            // ================================================================
            [
                'name' => 'master-pricelist-sewa-kontainer-view',
                'description' => 'Lihat pricelist sewa kontainer'
            ],
            [
                'name' => 'master-pricelist-sewa-kontainer-create',
                'description' => 'Tambah pricelist sewa kontainer'
            ],
            [
                'name' => 'master-pricelist-sewa-kontainer-update',
                'description' => 'Edit pricelist sewa kontainer'
            ],
            [
                'name' => 'master-pricelist-sewa-kontainer-delete',
                'description' => 'Hapus pricelist sewa kontainer'
            ],

            // ================================================================
            // 🎨 MASTER PRICELIST CAT PERMISSIONS
            // ================================================================
            [
                'name' => 'master-pricelist-cat-view',
                'description' => 'Lihat pricelist cat'
            ],
            [
                'name' => 'master-pricelist-cat-create',
                'description' => 'Tambah pricelist cat'
            ],
            [
                'name' => 'master-pricelist-cat-update',
                'description' => 'Edit pricelist cat'
            ],
            [
                'name' => 'master-pricelist-cat-delete',
                'description' => 'Hapus pricelist cat'
            ],

            // ================================================================
            // 📝 PERMOHONAN MEMO PERMISSIONS
            // ================================================================
            [
                'name' => 'permohonan',
                'description' => 'Akses modul permohonan'
            ],
            [
                'name' => 'permohonan-memo-view',
                'description' => 'Lihat permohonan memo'
            ],
            [
                'name' => 'permohonan-memo-create',
                'description' => 'Tambah permohonan memo'
            ],
            [
                'name' => 'permohonan-memo-update',
                'description' => 'Edit permohonan memo'
            ],
            [
                'name' => 'permohonan-memo-delete',
                'description' => 'Hapus permohonan memo'
            ],
            [
                'name' => 'permohonan-memo-print',
                'description' => 'Cetak permohonan memo'
            ],

            // ================================================================
            // 🚚 PRANOTA SUPIR PERMISSIONS
            // ================================================================
            [
                'name' => 'pranota-supir-view',
                'description' => 'Lihat pranota supir'
            ],
            [
                'name' => 'pranota-supir-create',
                'description' => 'Tambah pranota supir'
            ],
            [
                'name' => 'pranota-supir-update',
                'description' => 'Edit pranota supir'
            ],
            [
                'name' => 'pranota-supir-delete',
                'description' => 'Hapus pranota supir'
            ],
            [
                'name' => 'pranota-supir-print',
                'description' => 'Cetak pranota supir'
            ],

            // ================================================================
            // 💳 PEMBAYARAN PRANOTA SUPIR PERMISSIONS
            // ================================================================
            [
                'name' => 'pembayaran-pranota-supir-view',
                'description' => 'Lihat pembayaran pranota supir'
            ],
            [
                'name' => 'pembayaran-pranota-supir-create',
                'description' => 'Tambah pembayaran pranota supir'
            ],
            [
                'name' => 'pembayaran-pranota-supir-update',
                'description' => 'Edit pembayaran pranota supir'
            ],
            [
                'name' => 'pembayaran-pranota-supir-delete',
                'description' => 'Hapus pembayaran pranota supir'
            ],
            [
                'name' => 'pembayaran-pranota-supir-print',
                'description' => 'Cetak pembayaran pranota supir'
            ],

            // ================================================================
            // 🔧 TAGIHAN PERBAIKAN KONTAINER PERMISSIONS
            // ================================================================
            [
                'name' => 'tagihan-perbaikan-kontainer-view',
                'description' => 'Lihat tagihan perbaikan kontainer'
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-create',
                'description' => 'Tambah tagihan perbaikan kontainer'
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-update',
                'description' => 'Edit tagihan perbaikan kontainer'
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-delete',
                'description' => 'Hapus tagihan perbaikan kontainer'
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-print',
                'description' => 'Cetak tagihan perbaikan kontainer'
            ],

            // ================================================================
            // 🔧 PERBAIKAN KONTAINER PERMISSIONS (Additional)
            // ================================================================
            [
                'name' => 'perbaikan-kontainer-view',
                'description' => 'Lihat perbaikan kontainer'
            ],
            [
                'name' => 'perbaikan-kontainer-update',
                'description' => 'Edit perbaikan kontainer'
            ],
            [
                'name' => 'perbaikan-kontainer-delete',
                'description' => 'Hapus perbaikan kontainer'
            ],

            // ================================================================
            // 📄 PRANOTA PERBAIKAN KONTAINER PERMISSIONS
            // ================================================================
            [
                'name' => 'pranota-perbaikan-kontainer-view',
                'description' => 'Lihat pranota perbaikan kontainer'
            ],
            [
                'name' => 'pranota-perbaikan-kontainer-create',
                'description' => 'Tambah pranota perbaikan kontainer'
            ],
            [
                'name' => 'pranota-perbaikan-kontainer-update',
                'description' => 'Edit pranota perbaikan kontainer'
            ],
            [
                'name' => 'pranota-perbaikan-kontainer-delete',
                'description' => 'Hapus pranota perbaikan kontainer'
            ],
            [
                'name' => 'pranota-perbaikan-kontainer-print',
                'description' => 'Cetak pranota perbaikan kontainer'
            ],

            // ================================================================
            // 💳 PEMBAYARAN PRANOTA PERBAIKAN KONTAINER PERMISSIONS
            // ================================================================
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer-view',
                'description' => 'Lihat pembayaran pranota perbaikan kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer-create',
                'description' => 'Tambah pembayaran pranota perbaikan kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer-update',
                'description' => 'Edit pembayaran pranota perbaikan kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer-delete',
                'description' => 'Hapus pembayaran pranota perbaikan kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer-print',
                'description' => 'Cetak pembayaran pranota perbaikan kontainer'
            ],

            // ================================================================
            // 🎨 TAGIHAN CAT PERMISSIONS
            // ================================================================
            [
                'name' => 'tagihan-cat-view',
                'description' => 'Lihat tagihan cat'
            ],
            [
                'name' => 'tagihan-cat-create',
                'description' => 'Tambah tagihan cat'
            ],
            [
                'name' => 'tagihan-cat-update',
                'description' => 'Edit tagihan cat'
            ],
            [
                'name' => 'tagihan-cat-delete',
                'description' => 'Hapus tagihan cat'
            ],

            // ================================================================
            // 📄 PRANOTA CAT PERMISSIONS
            // ================================================================
            [
                'name' => 'pranota-cat-view',
                'description' => 'Lihat pranota cat'
            ],
            [
                'name' => 'pranota-cat-create',
                'description' => 'Tambah pranota cat'
            ],
            [
                'name' => 'pranota-cat-update',
                'description' => 'Edit pranota cat'
            ],
            [
                'name' => 'pranota-cat-delete',
                'description' => 'Hapus pranota cat'
            ],
            [
                'name' => 'pranota-cat-print',
                'description' => 'Cetak pranota cat'
            ],

            // ================================================================
            // 💳 PEMBAYARAN PRANOTA CAT PERMISSIONS
            // ================================================================
            [
                'name' => 'pembayaran-pranota-cat-view',
                'description' => 'Lihat pembayaran pranota cat'
            ],
            [
                'name' => 'pembayaran-pranota-cat-create',
                'description' => 'Tambah pembayaran pranota cat'
            ],
            [
                'name' => 'pembayaran-pranota-cat-update',
                'description' => 'Edit pembayaran pranota cat'
            ],
            [
                'name' => 'pembayaran-pranota-cat-delete',
                'description' => 'Hapus pembayaran pranota cat'
            ],
            [
                'name' => 'pembayaran-pranota-cat-print',
                'description' => 'Cetak pembayaran pranota cat'
            ],

            // ================================================================
            // 📦 TAGIHAN KONTAINER SEWA PERMISSIONS
            // ================================================================
            [
                'name' => 'tagihan-kontainer-sewa-index',
                'description' => 'Lihat daftar tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-create',
                'description' => 'Tambah tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-update',
                'description' => 'Edit tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-destroy',
                'description' => 'Hapus tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-update',
                'description' => 'Update tagihan kontainer'
            ],
            [
                'name' => 'tagihan-kontainer-delete',
                'description' => 'Hapus tagihan kontainer'
            ],

            // ================================================================
            // 📄 PRANOTA KONTAINER SEWA PERMISSIONS
            // ================================================================
            [
                'name' => 'pranota-kontainer-sewa-view',
                'description' => 'Lihat pranota kontainer sewa'
            ],
            [
                'name' => 'pranota-kontainer-sewa-create',
                'description' => 'Tambah pranota kontainer sewa'
            ],
            [
                'name' => 'pranota-kontainer-sewa-update',
                'description' => 'Edit pranota kontainer sewa'
            ],
            [
                'name' => 'pranota-kontainer-sewa-delete',
                'description' => 'Hapus pranota kontainer sewa'
            ],
            [
                'name' => 'pranota-kontainer-sewa-print',
                'description' => 'Cetak pranota kontainer sewa'
            ],

            // ================================================================
            // 💳 PEMBAYARAN PRANOTA KONTAINER PERMISSIONS
            // ================================================================
            [
                'name' => 'pembayaran-pranota-kontainer-view',
                'description' => 'Lihat pembayaran pranota kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-kontainer-create',
                'description' => 'Tambah pembayaran pranota kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-kontainer-update',
                'description' => 'Edit pembayaran pranota kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-kontainer-delete',
                'description' => 'Hapus pembayaran pranota kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-kontainer-print',
                'description' => 'Cetak pembayaran pranota kontainer'
            ],

            // ================================================================
            // 📋 GENERAL PRANOTA PERMISSIONS
            // ================================================================
            [
                'name' => 'pranota-view',
                'description' => 'Lihat pranota umum'
            ],
            [
                'name' => 'pranota-create',
                'description' => 'Tambah pranota umum'
            ],
            [
                'name' => 'pranota-update',
                'description' => 'Edit pranota umum'
            ],
            [
                'name' => 'pranota-delete',
                'description' => 'Hapus pranota umum'
            ],
            [
                'name' => 'pranota-print',
                'description' => 'Cetak pranota umum'
            ],

            // ================================================================
            // 📊 AKTIVITAS LAINNYA PERMISSIONS
            // ================================================================
            [
                'name' => 'aktivitas-lainnya-view',
                'description' => 'Lihat aktivitas lainnya'
            ],
            [
                'name' => 'aktivitas-lainnya-create',
                'description' => 'Tambah aktivitas lainnya'
            ],
            [
                'name' => 'aktivitas-lainnya-update',
                'description' => 'Edit aktivitas lainnya'
            ],
            [
                'name' => 'aktivitas-lainnya-delete',
                'description' => 'Hapus aktivitas lainnya'
            ],
            [
                'name' => 'aktivitas-lainnya-approve',
                'description' => 'Approve aktivitas lainnya'
            ],

            // ================================================================
            // 💳 PEMBAYARAN AKTIVITAS LAINNYA PERMISSIONS
            // ================================================================
            [
                'name' => 'pembayaran-aktivitas-lainnya-view',
                'description' => 'Lihat pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-create',
                'description' => 'Tambah pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-update',
                'description' => 'Edit pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-delete',
                'description' => 'Hapus pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-print',
                'description' => 'Cetak pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-export',
                'description' => 'Export pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-approve',
                'description' => 'Approve pembayaran aktivitas lainnya'
            ],

            // ================================================================
            // ✅ APPROVAL PERMISSIONS
            // ================================================================
            [
                'name' => 'approval-tugas-1.view',
                'description' => 'Akses approval tugas level 1'
            ],
            [
                'name' => 'approval-dashboard',
                'description' => 'Akses approval dashboard level 2'
            ],

            // ================================================================
            // 👤 PROFILE PERMISSIONS
            // ================================================================
            [
                'name' => 'profile-view',
                'description' => 'Lihat profil sendiri'
            ],
            [
                'name' => 'profile-update',
                'description' => 'Edit profil sendiri'
            ],
            [
                'name' => 'profile-delete',
                'description' => 'Hapus akun sendiri'
            ],
        ];

        // Insert all permissions with timestamps
        $timestamp = now();
        $permissionsWithTimestamps = array_map(function ($permission) use ($timestamp) {
            return array_merge($permission, [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }, $permissions);

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($permissionsWithTimestamps, 50);
        foreach ($chunks as $chunk) {
            Permission::insert($chunk);
        }

        $this->command->info('✅ Total ' . count($permissions) . ' permissions telah berhasil di-seed!');
        $this->command->info('📊 Breakdown:');
        $this->command->info('   - System & Dashboard: 1');
        $this->command->info('   - Master Data: ~100');
        $this->command->info('   - Business Process: ~100');
        $this->command->info('   - Approval & Profile: 6');
    }
}
