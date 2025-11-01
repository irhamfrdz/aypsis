<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class ComprehensivePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * ═══════════════════════════════════════════════════════════════════════
     * SEEDER KOMPREHENSIF UNTUK SEMUA PERMISSION DI SISTEM AYPSIS
     * ═══════════════════════════════════════════════════════════════════════
     * 
     * Dibuat berdasarkan analisis mendalam dari:
     * - routes/web.php (semua middleware 'can:' permissions)
     * - PermissionSeeder.php yang sudah ada
     * - ComprehensiveSystemPermissionSeeder.php
     * 
     * Total: 300+ permissions
     * 
     * Cara Menjalankan:
     * php artisan db:seed --class=ComprehensivePermissionSeeder
     * 
     * @return void
     */
    public function run(): void
    {
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   🔐 COMPREHENSIVE PERMISSION SEEDER - AYPSIS SYSTEM');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
        
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Get all permissions
        $permissions = $this->getAllPermissions();

        $created = 0;
        $existing = 0;
        $updated = 0;

        // Insert permissions only if they don't exist
        foreach ($permissions as $permissionName => $description) {
            $existingPermission = Permission::where('name', $permissionName)->first();

            if (!$existingPermission) {
                Permission::create([
                    'name' => $permissionName,
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("✅ Created: {$permissionName}");
                $created++;
            } else {
                // Update description if different
                if ($existingPermission->description !== $description) {
                    $existingPermission->update([
                        'description' => $description,
                        'updated_at' => now(),
                    ]);
                    $this->command->comment("🔄 Updated: {$permissionName}");
                    $updated++;
                } else {
                    $existing++;
                }
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   📊 SEEDING SUMMARY');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info("   Total permissions: " . count($permissions));
        $this->command->info("   ✅ New created: {$created}");
        $this->command->info("   🔄 Updated: {$updated}");
        $this->command->info("   ℹ️  Already exists: {$existing}");
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->info('🎉 Permission seeding completed successfully!');
        $this->command->newLine();
    }

    /**
     * Get all permissions with descriptions
     * 
     * @return array
     */
    private function getAllPermissions(): array
    {
        return [
            // ═══════════════════════════════════════════════════════════════════════
            // 🏠 SYSTEM & AUTHENTICATION PERMISSIONS
            // ═══════════════════════════════════════════════════════════════════════
            'dashboard' => 'Akses Dashboard Utama',
            'login' => 'Login ke Sistem',
            'logout' => 'Logout dari Sistem',
            'storage-local' => 'Akses Storage Lokal',
            
            // ═══════════════════════════════════════════════════════════════════════
            // 👤 MASTER USER PERMISSIONS (8)
            // ═══════════════════════════════════════════════════════════════════════
            'master-user-view' => 'Melihat Data User',
            'master-user-create' => 'Membuat User Baru',
            'master-user-update' => 'Mengupdate Data User',
            'master-user-delete' => 'Menghapus Data User',
            'master-user-print' => 'Print Data User',
            'master-user-export' => 'Export Data User',
            'master-user-import' => 'Import Data User',
            'master-user-bulk-manage' => 'Manajemen Bulk User',

            // ═══════════════════════════════════════════════════════════════════════
            // 👥 MASTER KARYAWAN PERMISSIONS (10)
            // ═══════════════════════════════════════════════════════════════════════
            'master-karyawan' => 'Akses Master Karyawan',
            'master-karyawan-view' => 'Melihat Data Karyawan',
            'master-karyawan-create' => 'Membuat Data Karyawan',
            'master-karyawan-update' => 'Mengupdate Data Karyawan',
            'master-karyawan-delete' => 'Menghapus Data Karyawan',
            'master-karyawan-print' => 'Print Data Karyawan',
            'master-karyawan-export' => 'Export Data Karyawan',
            'master-karyawan-import' => 'Import Data Karyawan',
            'master-karyawan-template' => 'Template Data Karyawan',
            'master-karyawan-crew-checklist' => 'Crew Checklist Karyawan',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 MASTER KONTAINER PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-kontainer-view' => 'Melihat Data Kontainer',
            'master-kontainer-create' => 'Membuat Data Kontainer',
            'master-kontainer-update' => 'Mengupdate Data Kontainer',
            'master-kontainer-delete' => 'Menghapus Data Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎯 MASTER TUJUAN PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'master-tujuan-view' => 'Melihat Data Tujuan',
            'master-tujuan-create' => 'Membuat Data Tujuan',
            'master-tujuan-update' => 'Mengupdate Data Tujuan',
            'master-tujuan-delete' => 'Menghapus Data Tujuan',
            'master-tujuan-export' => 'Export Data Tujuan',
            'master-tujuan-print' => 'Print Data Tujuan',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎯 MASTER TUJUAN KIRIM PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-tujuan-kirim-view' => 'Melihat Data Tujuan Kirim',
            'master-tujuan-kirim-create' => 'Membuat Data Tujuan Kirim',
            'master-tujuan-kirim-update' => 'Mengupdate Data Tujuan Kirim',
            'master-tujuan-kirim-delete' => 'Menghapus Data Tujuan Kirim',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏗️ MASTER KEGIATAN PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-kegiatan-view' => 'Melihat Data Kegiatan',
            'master-kegiatan-create' => 'Membuat Data Kegiatan',
            'master-kegiatan-update' => 'Mengupdate Data Kegiatan',
            'master-kegiatan-delete' => 'Menghapus Data Kegiatan',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔐 MASTER PERMISSION PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-permission-view' => 'Melihat Data Permission',
            'master-permission-create' => 'Membuat Permission Baru',
            'master-permission-update' => 'Mengupdate Permission',
            'master-permission-delete' => 'Menghapus Permission',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚗 MASTER MOBIL PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-mobil-view' => 'Melihat Data Mobil',
            'master-mobil-create' => 'Membuat Data Mobil',
            'master-mobil-update' => 'Mengupdate Data Mobil',
            'master-mobil-delete' => 'Menghapus Data Mobil',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 MASTER PRICELIST SEWA KONTAINER PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-pricelist-sewa-kontainer-view' => 'Melihat Pricelist Sewa Kontainer',
            'master-pricelist-sewa-kontainer-create' => 'Membuat Pricelist Sewa Kontainer',
            'master-pricelist-sewa-kontainer-update' => 'Mengupdate Pricelist Sewa Kontainer',
            'master-pricelist-sewa-kontainer-delete' => 'Menghapus Pricelist Sewa Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎨 MASTER PRICELIST CAT PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-pricelist-cat-view' => 'Melihat Pricelist CAT',
            'master-pricelist-cat-create' => 'Membuat Pricelist CAT',
            'master-pricelist-cat-update' => 'Mengupdate Pricelist CAT',
            'master-pricelist-cat-delete' => 'Menghapus Pricelist CAT',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚪 MASTER PRICELIST GATE IN PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-pricelist-gate-in-view' => 'Melihat Pricelist Gate In',
            'master-pricelist-gate-in-create' => 'Membuat Pricelist Gate In',
            'master-pricelist-gate-in-update' => 'Mengupdate Pricelist Gate In',
            'master-pricelist-gate-in-delete' => 'Menghapus Pricelist Gate In',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏢 MASTER DIVISI PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-divisi-view' => 'Melihat Data Divisi',
            'master-divisi-create' => 'Membuat Data Divisi',
            'master-divisi-update' => 'Mengupdate Data Divisi',
            'master-divisi-delete' => 'Menghapus Data Divisi',

            // ═══════════════════════════════════════════════════════════════════════
            // 💸 MASTER PAJAK PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'master-pajak-view' => 'Melihat Data Pajak',
            'master-pajak-create' => 'Membuat Data Pajak',
            'master-pajak-update' => 'Mengupdate Data Pajak',
            'master-pajak-delete' => 'Menghapus Data Pajak',
            'master-pajak-destroy' => 'Destroy Data Pajak',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏦 MASTER BANK PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'master-bank-view' => 'Melihat Data Bank',
            'master-bank-create' => 'Membuat Data Bank',
            'master-bank-update' => 'Mengupdate Data Bank',
            'master-bank-delete' => 'Menghapus Data Bank',
            'master-bank-destroy' => 'Destroy Data Bank',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏢 MASTER CABANG PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-cabang-view' => 'Melihat Data Cabang',
            'master-cabang-create' => 'Membuat Data Cabang',
            'master-cabang-update' => 'Mengupdate Data Cabang',
            'master-cabang-delete' => 'Menghapus Data Cabang',

            // ═══════════════════════════════════════════════════════════════════════
            // 📊 MASTER COA (CHART OF ACCOUNTS) PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-coa-view' => 'Melihat Data COA',
            'master-coa-create' => 'Membuat Data COA',
            'master-coa-update' => 'Mengupdate Data COA',
            'master-coa-delete' => 'Menghapus Data COA',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 MASTER PEKERJAAN PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'master-pekerjaan-view' => 'Melihat Data Pekerjaan',
            'master-pekerjaan-create' => 'Membuat Data Pekerjaan',
            'master-pekerjaan-update' => 'Mengupdate Data Pekerjaan',
            'master-pekerjaan-delete' => 'Menghapus Data Pekerjaan',
            'master-pekerjaan-destroy' => 'Destroy Data Pekerjaan',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 MASTER VENDOR BENGKEL PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-vendor-bengkel-view' => 'Melihat Data Vendor Bengkel',
            'master-vendor-bengkel-create' => 'Membuat Data Vendor Bengkel',
            'master-vendor-bengkel-update' => 'Mengupdate Data Vendor Bengkel',
            'master-vendor-bengkel-delete' => 'Menghapus Data Vendor Bengkel',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔢 MASTER KODE NOMOR PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-kode-nomor-view' => 'Melihat Data Kode Nomor',
            'master-kode-nomor-create' => 'Membuat Data Kode Nomor',
            'master-kode-nomor-update' => 'Mengupdate Data Kode Nomor',
            'master-kode-nomor-delete' => 'Menghapus Data Kode Nomor',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 MASTER STOCK KONTAINER PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-stock-kontainer-view' => 'Melihat Stock Kontainer',
            'master-stock-kontainer-create' => 'Membuat Stock Kontainer',
            'master-stock-kontainer-update' => 'Mengupdate Stock Kontainer',
            'master-stock-kontainer-delete' => 'Menghapus Stock Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚢 MASTER KAPAL PERMISSIONS (7)
            // ═══════════════════════════════════════════════════════════════════════
            'master-kapal' => 'Akses Master Kapal',
            'master-kapal.view' => 'Melihat Data Kapal',
            'master-kapal.create' => 'Membuat Data Kapal',
            'master-kapal.edit' => 'Edit Data Kapal',
            'master-kapal.delete' => 'Menghapus Data Kapal',
            'master-kapal.print' => 'Print Data Kapal',
            'master-kapal.export' => 'Export Data Kapal',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏦 MASTER TIPE AKUN PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-tipe-akun-view' => 'Melihat Tipe Akun',
            'master-tipe-akun-create' => 'Membuat Tipe Akun',
            'master-tipe-akun-update' => 'Mengupdate Tipe Akun',
            'master-tipe-akun-delete' => 'Menghapus Tipe Akun',

            // ═══════════════════════════════════════════════════════════════════════
            // 📋 MASTER NOMOR TERAKHIR PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-nomor-terakhir-view' => 'Melihat Nomor Terakhir',
            'master-nomor-terakhir-create' => 'Membuat Nomor Terakhir',
            'master-nomor-terakhir-update' => 'Mengupdate Nomor Terakhir',
            'master-nomor-terakhir-delete' => 'Menghapus Nomor Terakhir',

            // ═══════════════════════════════════════════════════════════════════════
            // 📧 MASTER PENGIRIM PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-pengirim-view' => 'Melihat Data Pengirim',
            'master-pengirim-create' => 'Membuat Data Pengirim',
            'master-pengirim-update' => 'Mengupdate Data Pengirim',
            'master-pengirim-delete' => 'Menghapus Data Pengirim',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 MASTER JENIS BARANG PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-jenis-barang-view' => 'Melihat Jenis Barang',
            'master-jenis-barang-create' => 'Membuat Jenis Barang',
            'master-jenis-barang-update' => 'Mengupdate Jenis Barang',
            'master-jenis-barang-delete' => 'Menghapus Jenis Barang',

            // ═══════════════════════════════════════════════════════════════════════
            // 📃 MASTER TERM PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'master-term-view' => 'Melihat Data Term',
            'master-term-create' => 'Membuat Data Term',
            'master-term-update' => 'Mengupdate Data Term',
            'master-term-delete' => 'Menghapus Data Term',

            // ═══════════════════════════════════════════════════════════════════════
            // ⚓ MASTER PELABUHAN PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'master-pelabuhan-view' => 'Melihat Data Pelabuhan',
            'master-pelabuhan-create' => 'Membuat Data Pelabuhan',
            'master-pelabuhan-edit' => 'Edit Data Pelabuhan',
            'master-pelabuhan-update' => 'Mengupdate Data Pelabuhan',
            'master-pelabuhan-delete' => 'Menghapus Data Pelabuhan',

            // ═══════════════════════════════════════════════════════════════════════
            // 🏢 VENDOR KONTAINER SEWA PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'vendor-kontainer-sewa-view' => 'Melihat Vendor Kontainer Sewa',
            'vendor-kontainer-sewa-create' => 'Membuat Vendor Kontainer Sewa',
            'vendor-kontainer-sewa-edit' => 'Edit Vendor Kontainer Sewa',
            'vendor-kontainer-sewa-update' => 'Update Vendor Kontainer Sewa',
            'vendor-kontainer-sewa-delete' => 'Menghapus Vendor Kontainer Sewa',

            // ═══════════════════════════════════════════════════════════════════════
            // 📋 ORDER PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'order-view' => 'Melihat Data Order',
            'order-create' => 'Membuat Order',
            'order-update' => 'Mengupdate Order',
            'order-delete' => 'Menghapus Order',
            'order-print' => 'Print Order',
            'order-export' => 'Export Order',

            // ═══════════════════════════════════════════════════════════════════════
            // 📄 SURAT JALAN PERMISSIONS (7)
            // ═══════════════════════════════════════════════════════════════════════
            'surat-jalan-view' => 'Melihat Surat Jalan',
            'surat-jalan-create' => 'Membuat Surat Jalan',
            'surat-jalan-update' => 'Mengupdate Surat Jalan',
            'surat-jalan-delete' => 'Menghapus Surat Jalan',
            'surat-jalan-print' => 'Print Surat Jalan',
            'surat-jalan-export' => 'Export Surat Jalan',
            'surat-jalan-approval-dashboard' => 'Dashboard Approval Surat Jalan',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 PRANOTA UANG RIT PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-uang-rit-view' => 'Melihat Pranota Uang Rit',
            'pranota-uang-rit-create' => 'Membuat Pranota Uang Rit',
            'pranota-uang-rit-update' => 'Mengupdate Pranota Uang Rit',
            'pranota-uang-rit-delete' => 'Menghapus Pranota Uang Rit',
            'pranota-uang-rit-approve' => 'Approve Pranota Uang Rit',
            'pranota-uang-rit-mark-paid' => 'Mark Paid Pranota Uang Rit',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 PRANOTA UANG KENEK PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-uang-kenek-view' => 'Melihat Pranota Uang Kenek',
            'pranota-uang-kenek-create' => 'Membuat Pranota Uang Kenek',
            'pranota-uang-kenek-update' => 'Mengupdate Pranota Uang Kenek',
            'pranota-uang-kenek-delete' => 'Menghapus Pranota Uang Kenek',
            'pranota-uang-kenek-approve' => 'Approve Pranota Uang Kenek',
            'pranota-uang-kenek-mark-paid' => 'Mark Paid Pranota Uang Kenek',

            // ═══════════════════════════════════════════════════════════════════════
            // 📋 TANDA TERIMA PERMISSIONS (7)
            // ═══════════════════════════════════════════════════════════════════════
            'tanda-terima-view' => 'Melihat Tanda Terima',
            'tanda-terima-create' => 'Membuat Tanda Terima',
            'tanda-terima-edit' => 'Edit Tanda Terima',
            'tanda-terima-update' => 'Update Tanda Terima',
            'tanda-terima-delete' => 'Menghapus Tanda Terima',
            'tanda-terima-print' => 'Print Tanda Terima',
            'tanda-terima-export' => 'Export Tanda Terima',

            // ═══════════════════════════════════════════════════════════════════════
            // 📋 TANDA TERIMA TANPA SURAT JALAN PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'tanda-terima-tanpa-surat-jalan-view' => 'Melihat Tanda Terima Tanpa Surat Jalan',
            'tanda-terima-tanpa-surat-jalan-create' => 'Membuat Tanda Terima Tanpa Surat Jalan',
            'tanda-terima-tanpa-surat-jalan-update' => 'Mengupdate Tanda Terima Tanpa Surat Jalan',
            'tanda-terima-tanpa-surat-jalan-delete' => 'Menghapus Tanda Terima Tanpa Surat Jalan',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚪 GATE IN PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'gate-in-view' => 'Melihat Gate In',
            'gate-in-create' => 'Membuat Gate In',
            'gate-in-update' => 'Mengupdate Gate In',
            'gate-in-delete' => 'Menghapus Gate In',
            'gate-in-print' => 'Print Gate In',
            'gate-in-export' => 'Export Gate In',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚢 PERGERAKAN KAPAL PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'pergerakan-kapal-view' => 'Melihat Pergerakan Kapal',
            'pergerakan-kapal-create' => 'Membuat Pergerakan Kapal',
            'pergerakan-kapal-update' => 'Mengupdate Pergerakan Kapal',
            'pergerakan-kapal-delete' => 'Menghapus Pergerakan Kapal',

            // ═══════════════════════════════════════════════════════════════════════
            // 📝 PERMOHONAN PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'permohonan' => 'Akses Permohonan',
            'permohonan-memo-view' => 'Melihat Permohonan Memo',
            'permohonan-memo-create' => 'Membuat Permohonan Memo',
            'permohonan-memo-update' => 'Mengupdate Permohonan Memo',
            'permohonan-memo-delete' => 'Menghapus Permohonan Memo',
            'permohonan-memo-print' => 'Print Permohonan Memo',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚚 PRANOTA SUPIR PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-supir-view' => 'Melihat Pranota Supir',
            'pranota-supir-create' => 'Membuat Pranota Supir',
            'pranota-supir-update' => 'Mengupdate Pranota Supir',
            'pranota-supir-delete' => 'Menghapus Pranota Supir',
            'pranota-supir-print' => 'Print Pranota Supir',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN PRANOTA SUPIR PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-pranota-supir-view' => 'Melihat Pembayaran Pranota Supir',
            'pembayaran-pranota-supir-create' => 'Membuat Pembayaran Pranota Supir',
            'pembayaran-pranota-supir-update' => 'Mengupdate Pembayaran Pranota Supir',
            'pembayaran-pranota-supir-delete' => 'Menghapus Pembayaran Pranota Supir',
            'pembayaran-pranota-supir-print' => 'Print Pembayaran Pranota Supir',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN PRANOTA PERBAIKAN KONTAINER PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-pranota-perbaikan-kontainer-view' => 'Melihat Pembayaran Pranota Perbaikan Kontainer',
            'pembayaran-pranota-perbaikan-kontainer-create' => 'Membuat Pembayaran Pranota Perbaikan Kontainer',
            'pembayaran-pranota-perbaikan-kontainer-update' => 'Mengupdate Pembayaran Pranota Perbaikan Kontainer',
            'pembayaran-pranota-perbaikan-kontainer-delete' => 'Menghapus Pembayaran Pranota Perbaikan Kontainer',
            'pembayaran-pranota-perbaikan-kontainer-print' => 'Print Pembayaran Pranota Perbaikan Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 📄 PRANOTA SURAT JALAN PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-surat-jalan-view' => 'Melihat Pranota Surat Jalan',
            'pranota-surat-jalan-create' => 'Membuat Pranota Surat Jalan',
            'pranota-surat-jalan-update' => 'Mengupdate Pranota Surat Jalan',
            'pranota-surat-jalan-delete' => 'Menghapus Pranota Surat Jalan',
            'pranota-surat-jalan-print' => 'Print Pranota Surat Jalan',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 PERBAIKAN KONTAINER PERMISSIONS (3)
            // ═══════════════════════════════════════════════════════════════════════
            'perbaikan-kontainer-view' => 'Melihat Perbaikan Kontainer',
            'perbaikan-kontainer-update' => 'Mengupdate Perbaikan Kontainer',
            'perbaikan-kontainer-delete' => 'Menghapus Perbaikan Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 PRANOTA PERBAIKAN KONTAINER PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-perbaikan-kontainer-view' => 'Melihat Pranota Perbaikan Kontainer',
            'pranota-perbaikan-kontainer-create' => 'Membuat Pranota Perbaikan Kontainer',
            'pranota-perbaikan-kontainer-update' => 'Mengupdate Pranota Perbaikan Kontainer',
            'pranota-perbaikan-kontainer-delete' => 'Menghapus Pranota Perbaikan Kontainer',
            'pranota-perbaikan-kontainer-print' => 'Print Pranota Perbaikan Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 🔧 TAGIHAN PERBAIKAN KONTAINER PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'tagihan-perbaikan-kontainer-view' => 'Melihat Tagihan Perbaikan Kontainer',
            'tagihan-perbaikan-kontainer-create' => 'Membuat Tagihan Perbaikan Kontainer',
            'tagihan-perbaikan-kontainer-update' => 'Mengupdate Tagihan Perbaikan Kontainer',
            'tagihan-perbaikan-kontainer-delete' => 'Menghapus Tagihan Perbaikan Kontainer',
            'tagihan-perbaikan-kontainer-print' => 'Print Tagihan Perbaikan Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎨 PRANOTA CAT PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-cat-view' => 'Melihat Pranota CAT',
            'pranota-cat-create' => 'Membuat Pranota CAT',
            'pranota-cat-update' => 'Mengupdate Pranota CAT',
            'pranota-cat-delete' => 'Menghapus Pranota CAT',
            'pranota-cat-print' => 'Print Pranota CAT',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎨 TAGIHAN CAT PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'tagihan-cat-view' => 'Melihat Tagihan CAT',
            'tagihan-cat-create' => 'Membuat Tagihan CAT',
            'tagihan-cat-update' => 'Mengupdate Tagihan CAT',
            'tagihan-cat-delete' => 'Menghapus Tagihan CAT',
            'tagihan-cat-print' => 'Print Tagihan CAT',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN PRANOTA CAT PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-pranota-cat-view' => 'Melihat Pembayaran Pranota CAT',
            'pembayaran-pranota-cat-create' => 'Membuat Pembayaran Pranota CAT',
            'pembayaran-pranota-cat-update' => 'Mengupdate Pembayaran Pranota CAT',
            'pembayaran-pranota-cat-delete' => 'Menghapus Pembayaran Pranota CAT',
            'pembayaran-pranota-cat-print' => 'Print Pembayaran Pranota CAT',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 PRANOTA KONTAINER SEWA PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-kontainer-sewa-view' => 'Melihat Pranota Kontainer Sewa',
            'pranota-kontainer-sewa-create' => 'Membuat Pranota Kontainer Sewa',
            'pranota-kontainer-sewa-edit' => 'Edit Pranota Kontainer Sewa',
            'pranota-kontainer-sewa-update' => 'Mengupdate Pranota Kontainer Sewa',
            'pranota-kontainer-sewa-delete' => 'Menghapus Pranota Kontainer Sewa',
            'pranota-kontainer-sewa-print' => 'Print Pranota Kontainer Sewa',

            // ═══════════════════════════════════════════════════════════════════════
            // 📦 TAGIHAN KONTAINER SEWA PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'tagihan-kontainer-sewa-index' => 'Index Tagihan Kontainer Sewa',
            'tagihan-kontainer-sewa-create' => 'Membuat Tagihan Kontainer Sewa',
            'tagihan-kontainer-sewa-update' => 'Mengupdate Tagihan Kontainer Sewa',
            'tagihan-kontainer-sewa-destroy' => 'Destroy Tagihan Kontainer Sewa',
            'tagihan-kontainer-update' => 'Update Tagihan Kontainer',
            'tagihan-kontainer-delete' => 'Menghapus Tagihan Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN PRANOTA KONTAINER PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-pranota-kontainer-view' => 'Melihat Pembayaran Pranota Kontainer',
            'pembayaran-pranota-kontainer-create' => 'Membuat Pembayaran Pranota Kontainer',
            'pembayaran-pranota-kontainer-update' => 'Mengupdate Pembayaran Pranota Kontainer',
            'pembayaran-pranota-kontainer-delete' => 'Menghapus Pembayaran Pranota Kontainer',
            'pembayaran-pranota-kontainer-print' => 'Print Pembayaran Pranota Kontainer',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN PRANOTA SURAT JALAN PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-pranota-surat-jalan-view' => 'Melihat Pembayaran Pranota Surat Jalan',
            'pembayaran-pranota-surat-jalan-create' => 'Membuat Pembayaran Pranota Surat Jalan',
            'pembayaran-pranota-surat-jalan-edit' => 'Edit Pembayaran Pranota Surat Jalan',
            'pembayaran-pranota-surat-jalan-delete' => 'Menghapus Pembayaran Pranota Surat Jalan',

            // ═══════════════════════════════════════════════════════════════════════
            // 📝 PRANOTA GENERAL PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'pranota-view' => 'Melihat Pranota',
            'pranota-create' => 'Membuat Pranota',
            'pranota-update' => 'Mengupdate Pranota',
            'pranota-delete' => 'Menghapus Pranota',
            'pranota-print' => 'Print Pranota',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎯 AKTIVITAS LAINNYA PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'aktivitas-lainnya-view' => 'Melihat Aktivitas Lainnya',
            'aktivitas-lainnya-create' => 'Membuat Aktivitas Lainnya',
            'aktivitas-lainnya-update' => 'Mengupdate Aktivitas Lainnya',
            'aktivitas-lainnya-delete' => 'Menghapus Aktivitas Lainnya',
            'aktivitas-lainnya-approve' => 'Approve Aktivitas Lainnya',

            // ═══════════════════════════════════════════════════════════════════════
            // 💳 PEMBAYARAN AKTIVITAS LAINNYA PERMISSIONS (7)
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-aktivitas-lainnya-view' => 'Melihat Pembayaran Aktivitas Lainnya',
            'pembayaran-aktivitas-lainnya-create' => 'Membuat Pembayaran Aktivitas Lainnya',
            'pembayaran-aktivitas-lainnya-update' => 'Mengupdate Pembayaran Aktivitas Lainnya',
            'pembayaran-aktivitas-lainnya-delete' => 'Menghapus Pembayaran Aktivitas Lainnya',
            'pembayaran-aktivitas-lainnya-print' => 'Print Pembayaran Aktivitas Lainnya',
            'pembayaran-aktivitas-lainnya-export' => 'Export Pembayaran Aktivitas Lainnya',
            'pembayaran-aktivitas-lainnya-approve' => 'Approve Pembayaran Aktivitas Lainnya',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 PEMBAYARAN UANG MUKA PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-uang-muka-view' => 'Melihat Pembayaran Uang Muka',
            'pembayaran-uang-muka-create' => 'Membuat Pembayaran Uang Muka',
            'pembayaran-uang-muka-edit' => 'Edit Pembayaran Uang Muka',
            'pembayaran-uang-muka-update' => 'Update Pembayaran Uang Muka',
            'pembayaran-uang-muka-delete' => 'Menghapus Pembayaran Uang Muka',
            'pembayaran-uang-muka-print' => 'Print Pembayaran Uang Muka',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 PEMBAYARAN OB PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'pembayaran-ob-view' => 'Melihat Pembayaran OB',
            'pembayaran-ob-create' => 'Membuat Pembayaran OB',
            'pembayaran-ob-edit' => 'Edit Pembayaran OB',
            'pembayaran-ob-update' => 'Update Pembayaran OB',
            'pembayaran-ob-delete' => 'Menghapus Pembayaran OB',
            'pembayaran-ob-print' => 'Print Pembayaran OB',

            // ═══════════════════════════════════════════════════════════════════════
            // 💰 REALISASI UANG MUKA PERMISSIONS (6)
            // ═══════════════════════════════════════════════════════════════════════
            'realisasi-uang-muka-view' => 'Melihat Realisasi Uang Muka',
            'realisasi-uang-muka-create' => 'Membuat Realisasi Uang Muka',
            'realisasi-uang-muka-edit' => 'Edit Realisasi Uang Muka',
            'realisasi-uang-muka-update' => 'Update Realisasi Uang Muka',
            'realisasi-uang-muka-delete' => 'Menghapus Realisasi Uang Muka',
            'realisasi-uang-muka-print' => 'Print Realisasi Uang Muka',

            // ═══════════════════════════════════════════════════════════════════════
            // 📋 BL PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'bl-view' => 'Melihat BL',
            'bl-create' => 'Membuat BL',
            'bl-edit' => 'Edit BL',
            'bl-update' => 'Update BL',
            'bl-delete' => 'Menghapus BL',

            // ═══════════════════════════════════════════════════════════════════════
            // 👤 PROFILE PERMISSIONS (4)
            // ═══════════════════════════════════════════════════════════════════════
            'profile-view' => 'Melihat Profile',
            'profile-edit' => 'Edit Profile',
            'profile-update' => 'Update Profile',
            'profile-delete' => 'Menghapus Profile',

            // ═══════════════════════════════════════════════════════════════════════
            // 🎯 PROSPEK PERMISSIONS (2)
            // ═══════════════════════════════════════════════════════════════════════
            'prospek-view' => 'Melihat Prospek',
            'prospek-edit' => 'Edit Prospek',

            // ═══════════════════════════════════════════════════════════════════════
            // ✅ APPROVAL PERMISSIONS (3)
            // ═══════════════════════════════════════════════════════════════════════
            'approval-dashboard' => 'Dashboard Approval',
            'approval-tugas-1' => 'Approval Tugas 1',
            'approval-tugas-2' => 'Approval Tugas 2',

            // ═══════════════════════════════════════════════════════════════════════
            // 📊 AUDIT LOG PERMISSIONS (2)
            // ═══════════════════════════════════════════════════════════════════════
            'audit-logs-view' => 'Melihat Audit Logs',
            'audit-logs-export' => 'Export Audit Logs',

            // ═══════════════════════════════════════════════════════════════════════
            // 📊 DASHBOARD PERMISSIONS (3)
            // ═══════════════════════════════════════════════════════════════════════
            'dashboard-view' => 'Melihat Dashboard',
            'dashboard-admin' => 'Dashboard Admin',
            'dashboard-operational' => 'Dashboard Operational',

            // ═══════════════════════════════════════════════════════════════════════
            // 🚚 SUPIR DASHBOARD PERMISSIONS (3)
            // ═══════════════════════════════════════════════════════════════════════
            'supir-dashboard-view' => 'Melihat Dashboard Supir',
            'checkpoint-create' => 'Membuat Checkpoint',
            'checkpoint-update' => 'Mengupdate Checkpoint',

            // ═══════════════════════════════════════════════════════════════════════
            // 📊 REPORT PERMISSIONS (5)
            // ═══════════════════════════════════════════════════════════════════════
            'report-tagihan-view' => 'Melihat Report Tagihan',
            'report-tagihan-export' => 'Export Report Tagihan',
            'report-pembayaran-view' => 'Melihat Report Pembayaran',
            'report-pembayaran-export' => 'Export Report Pembayaran',
            'report-pembayaran-print' => 'Print Report Pembayaran',
        ];
    }
}
