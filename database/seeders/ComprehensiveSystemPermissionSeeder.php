<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class ComprehensiveSystemPermissionSeeder extends Seeder
{
    /**
     * Comprehensive Permission Seeder untuk Sistem AYPSIS
     * 
     * Seeder ini dibuat berdasarkan analisis mendalam dari:
     * - UserController.php (convertMatrixPermissionsToIds method)
     * - routes/web.php (semua middleware 'can:' permissions)
     * - Struktur permission matrix di sistem
     * 
     * JANGAN DIJALANKAN - untuk review saja
     */
    public function run()
    {
        DB::beginTransaction();
        
        try {
            // Clear existing permissions (optional - uncomment if needed)
            // Permission::truncate();
            
            // ========================================================================
            // 1. SYSTEM & AUTHENTICATION PERMISSIONS
            // ========================================================================
            $systemPermissions = [
                // Core system permissions
                ['name' => 'dashboard', 'description' => 'Akses Dashboard Utama'],
                ['name' => 'login', 'description' => 'Login ke Sistem'],
                ['name' => 'logout', 'description' => 'Logout dari Sistem'],
                
                // Storage permissions
                ['name' => 'storage-local', 'description' => 'Akses Storage Lokal'],
            ];
            
            // ========================================================================
            // 2. MASTER DATA PERMISSIONS - USER MANAGEMENT
            // ========================================================================
            $masterUserPermissions = [
                // Basic CRUD
                ['name' => 'master-user-view', 'description' => 'Melihat Data User'],
                ['name' => 'master-user-create', 'description' => 'Membuat User Baru'],
                ['name' => 'master-user-update', 'description' => 'Mengupdate Data User'],
                ['name' => 'master-user-delete', 'description' => 'Menghapus Data User'],
                ['name' => 'master-user-print', 'description' => 'Print Data User'],
                ['name' => 'master-user-export', 'description' => 'Export Data User'],
                ['name' => 'master-user-import', 'description' => 'Import Data User'],
                
                // Advanced user management
                ['name' => 'master-user-bulk-manage', 'description' => 'Manajemen Bulk User'],
                
                // Dot notation variations (for backward compatibility)
                ['name' => 'master.user.index', 'description' => 'Index Master User'],
                ['name' => 'master.user.create', 'description' => 'Create Master User'],
                ['name' => 'master.user.store', 'description' => 'Store Master User'],
                ['name' => 'master.user.show', 'description' => 'Show Master User'],
                ['name' => 'master.user.edit', 'description' => 'Edit Master User'],
                ['name' => 'master.user.update', 'description' => 'Update Master User'],
                ['name' => 'master.user.destroy', 'description' => 'Destroy Master User'],
            ];
            
            // ========================================================================
            // 3. MASTER DATA PERMISSIONS - KARYAWAN MANAGEMENT
            // ========================================================================
            $masterKaryawanPermissions = [
                // Basic CRUD
                ['name' => 'master-karyawan-view', 'description' => 'Melihat Data Karyawan'],
                ['name' => 'master-karyawan-create', 'description' => 'Membuat Data Karyawan'],
                ['name' => 'master-karyawan-update', 'description' => 'Mengupdate Data Karyawan'],
                ['name' => 'master-karyawan-delete', 'description' => 'Menghapus Data Karyawan'],
                ['name' => 'master-karyawan-print', 'description' => 'Print Data Karyawan'],
                ['name' => 'master-karyawan-export', 'description' => 'Export Data Karyawan'],
                ['name' => 'master-karyawan-import', 'description' => 'Import Data Karyawan'],
                ['name' => 'master-karyawan-template', 'description' => 'Template Data Karyawan'],
                ['name' => 'master-karyawan-crew-checklist', 'description' => 'Crew Checklist Karyawan'],
                
                // Module-level permission
                ['name' => 'master-karyawan', 'description' => 'Akses Master Karyawan'],
                
                // Dot notation variations
                ['name' => 'master.karyawan.index', 'description' => 'Index Master Karyawan'],
                ['name' => 'master.karyawan.create', 'description' => 'Create Master Karyawan'],
                ['name' => 'master.karyawan.store', 'description' => 'Store Master Karyawan'],
                ['name' => 'master.karyawan.show', 'description' => 'Show Master Karyawan'],
                ['name' => 'master.karyawan.edit', 'description' => 'Edit Master Karyawan'],
                ['name' => 'master.karyawan.update', 'description' => 'Update Master Karyawan'],
                ['name' => 'master.karyawan.destroy', 'description' => 'Destroy Master Karyawan'],
                ['name' => 'master.karyawan.print', 'description' => 'Print Master Karyawan'],
                ['name' => 'master.karyawan.export', 'description' => 'Export Master Karyawan'],
                ['name' => 'master.karyawan.import', 'description' => 'Import Master Karyawan'],
                ['name' => 'master.karyawan.import.store', 'description' => 'Import Store Master Karyawan'],
                ['name' => 'master.karyawan.print.single', 'description' => 'Print Single Master Karyawan'],
                ['name' => 'master.karyawan.template', 'description' => 'Template Master Karyawan'],
            ];
            
            // ========================================================================
            // 4. MASTER DATA PERMISSIONS - KONTAINER MANAGEMENT
            // ========================================================================
            $masterKontainerPermissions = [
                // Basic CRUD
                ['name' => 'master-kontainer-view', 'description' => 'Melihat Data Kontainer'],
                ['name' => 'master-kontainer-create', 'description' => 'Membuat Data Kontainer'],
                ['name' => 'master-kontainer-update', 'description' => 'Mengupdate Data Kontainer'],
                ['name' => 'master-kontainer-delete', 'description' => 'Menghapus Data Kontainer'],
                ['name' => 'master-kontainer-print', 'description' => 'Print Data Kontainer'],
                ['name' => 'master-kontainer-export', 'description' => 'Export Data Kontainer'],
                ['name' => 'master-kontainer-import', 'description' => 'Import Data Kontainer'],
                
                // Module-level permission
                ['name' => 'master-kontainer', 'description' => 'Akses Master Kontainer'],
                
                // Dot notation variations
                ['name' => 'master.kontainer.index', 'description' => 'Index Master Kontainer'],
                ['name' => 'master.kontainer.create', 'description' => 'Create Master Kontainer'],
                ['name' => 'master.kontainer.store', 'description' => 'Store Master Kontainer'],
                ['name' => 'master.kontainer.show', 'description' => 'Show Master Kontainer'],
                ['name' => 'master.kontainer.edit', 'description' => 'Edit Master Kontainer'],
                ['name' => 'master.kontainer.update', 'description' => 'Update Master Kontainer'],
                ['name' => 'master.kontainer.destroy', 'description' => 'Destroy Master Kontainer'],
            ];
            
            // ========================================================================
            // 5. MASTER DATA PERMISSIONS - OTHER MASTERS
            // ========================================================================
            
            // Master Tujuan
            $masterTujuanPermissions = [
                ['name' => 'master-tujuan-view', 'description' => 'Melihat Data Tujuan'],
                ['name' => 'master-tujuan-create', 'description' => 'Membuat Data Tujuan'],
                ['name' => 'master-tujuan-update', 'description' => 'Mengupdate Data Tujuan'],
                ['name' => 'master-tujuan-delete', 'description' => 'Menghapus Data Tujuan'],
                ['name' => 'master-tujuan-print', 'description' => 'Print Data Tujuan'],
                ['name' => 'master-tujuan-export', 'description' => 'Export Data Tujuan'],
                ['name' => 'master-tujuan', 'description' => 'Akses Master Tujuan'],
                
                ['name' => 'master.tujuan.index', 'description' => 'Index Master Tujuan'],
                ['name' => 'master.tujuan.create', 'description' => 'Create Master Tujuan'],
                ['name' => 'master.tujuan.store', 'description' => 'Store Master Tujuan'],
                ['name' => 'master.tujuan.show', 'description' => 'Show Master Tujuan'],
                ['name' => 'master.tujuan.edit', 'description' => 'Edit Master Tujuan'],
                ['name' => 'master.tujuan.update', 'description' => 'Update Master Tujuan'],
                ['name' => 'master.tujuan.destroy', 'description' => 'Destroy Master Tujuan'],
            ];
            
            // Master Kegiatan
            $masterKegiatanPermissions = [
                ['name' => 'master-kegiatan-view', 'description' => 'Melihat Data Kegiatan'],
                ['name' => 'master-kegiatan-create', 'description' => 'Membuat Data Kegiatan'],
                ['name' => 'master-kegiatan-update', 'description' => 'Mengupdate Data Kegiatan'],
                ['name' => 'master-kegiatan-delete', 'description' => 'Menghapus Data Kegiatan'],
                ['name' => 'master-kegiatan-print', 'description' => 'Print Data Kegiatan'],
                ['name' => 'master-kegiatan-export', 'description' => 'Export Data Kegiatan'],
                ['name' => 'master-kegiatan', 'description' => 'Akses Master Kegiatan'],
                
                ['name' => 'master.kegiatan.index', 'description' => 'Index Master Kegiatan'],
                ['name' => 'master.kegiatan.create', 'description' => 'Create Master Kegiatan'],
                ['name' => 'master.kegiatan.store', 'description' => 'Store Master Kegiatan'],
                ['name' => 'master.kegiatan.show', 'description' => 'Show Master Kegiatan'],
                ['name' => 'master.kegiatan.edit', 'description' => 'Edit Master Kegiatan'],
                ['name' => 'master.kegiatan.update', 'description' => 'Update Master Kegiatan'],
                ['name' => 'master.kegiatan.destroy', 'description' => 'Destroy Master Kegiatan'],
                ['name' => 'master.kegiatan.template', 'description' => 'Template Master Kegiatan'],
                ['name' => 'master.kegiatan.import', 'description' => 'Import Master Kegiatan'],
            ];
            
            // Master Permission
            $masterPermissionPermissions = [
                ['name' => 'master-permission-view', 'description' => 'Melihat Data Permission'],
                ['name' => 'master-permission-create', 'description' => 'Membuat Data Permission'],
                ['name' => 'master-permission-update', 'description' => 'Mengupdate Data Permission'],
                ['name' => 'master-permission-delete', 'description' => 'Menghapus Data Permission'],
                ['name' => 'master-permission-print', 'description' => 'Print Data Permission'],
                ['name' => 'master-permission-export', 'description' => 'Export Data Permission'],
                ['name' => 'master-permission', 'description' => 'Akses Master Permission'],
                
                ['name' => 'master.permission.index', 'description' => 'Index Master Permission'],
                ['name' => 'master.permission.create', 'description' => 'Create Master Permission'],
                ['name' => 'master.permission.store', 'description' => 'Store Master Permission'],
                ['name' => 'master.permission.show', 'description' => 'Show Master Permission'],
                ['name' => 'master.permission.edit', 'description' => 'Edit Master Permission'],
                ['name' => 'master.permission.update', 'description' => 'Update Master Permission'],
                ['name' => 'master.permission.destroy', 'description' => 'Destroy Master Permission'],
            ];
            
            // Master Mobil
            $masterMobilPermissions = [
                ['name' => 'master-mobil-view', 'description' => 'Melihat Data Mobil'],
                ['name' => 'master-mobil-create', 'description' => 'Membuat Data Mobil'],
                ['name' => 'master-mobil-update', 'description' => 'Mengupdate Data Mobil'],
                ['name' => 'master-mobil-delete', 'description' => 'Menghapus Data Mobil'],
                ['name' => 'master-mobil-print', 'description' => 'Print Data Mobil'],
                ['name' => 'master-mobil-export', 'description' => 'Export Data Mobil'],
                ['name' => 'master-mobil', 'description' => 'Akses Master Mobil'],
                
                ['name' => 'master.mobil.index', 'description' => 'Index Master Mobil'],
                ['name' => 'master.mobil.create', 'description' => 'Create Master Mobil'],
                ['name' => 'master.mobil.store', 'description' => 'Store Master Mobil'],
                ['name' => 'master.mobil.show', 'description' => 'Show Master Mobil'],
                ['name' => 'master.mobil.edit', 'description' => 'Edit Master Mobil'],
                ['name' => 'master.mobil.update', 'description' => 'Update Master Mobil'],
                ['name' => 'master.mobil.destroy', 'description' => 'Destroy Master Mobil'],
            ];
            
            // Master Bank
            $masterBankPermissions = [
                ['name' => 'master-bank-view', 'description' => 'Melihat Data Bank'],
                ['name' => 'master-bank-create', 'description' => 'Membuat Data Bank'],
                ['name' => 'master-bank-update', 'description' => 'Mengupdate Data Bank'],
                ['name' => 'master-bank-delete', 'description' => 'Menghapus Data Bank'],
                ['name' => 'master-bank-print', 'description' => 'Print Data Bank'],
                ['name' => 'master-bank-export', 'description' => 'Export Data Bank'],
                ['name' => 'master-bank', 'description' => 'Akses Master Bank'],
            ];
            
            // Master Divisi
            $masterDivisiPermissions = [
                ['name' => 'master-divisi-view', 'description' => 'Melihat Data Divisi'],
                ['name' => 'master-divisi-create', 'description' => 'Membuat Data Divisi'],
                ['name' => 'master-divisi-update', 'description' => 'Mengupdate Data Divisi'],
                ['name' => 'master-divisi-delete', 'description' => 'Menghapus Data Divisi'],
                ['name' => 'master-divisi-print', 'description' => 'Print Data Divisi'],
                ['name' => 'master-divisi-export', 'description' => 'Export Data Divisi'],
                ['name' => 'master-divisi', 'description' => 'Akses Master Divisi'],
            ];
            
            // Master Pajak
            $masterPajakPermissions = [
                ['name' => 'master-pajak-view', 'description' => 'Melihat Data Pajak'],
                ['name' => 'master-pajak-create', 'description' => 'Membuat Data Pajak'],
                ['name' => 'master-pajak-update', 'description' => 'Mengupdate Data Pajak'],
                ['name' => 'master-pajak-delete', 'description' => 'Menghapus Data Pajak'],
                ['name' => 'master-pajak-print', 'description' => 'Print Data Pajak'],
                ['name' => 'master-pajak-export', 'description' => 'Export Data Pajak'],
                ['name' => 'master-pajak', 'description' => 'Akses Master Pajak'],
            ];
            
            // Master Cabang
            $masterCabangPermissions = [
                ['name' => 'master-cabang-view', 'description' => 'Melihat Data Cabang'],
                ['name' => 'master-cabang-create', 'description' => 'Membuat Data Cabang'],
                ['name' => 'master-cabang-update', 'description' => 'Mengupdate Data Cabang'],
                ['name' => 'master-cabang-delete', 'description' => 'Menghapus Data Cabang'],
                ['name' => 'master-cabang-print', 'description' => 'Print Data Cabang'],
                ['name' => 'master-cabang-export', 'description' => 'Export Data Cabang'],
                ['name' => 'master-cabang', 'description' => 'Akses Master Cabang'],
            ];
            
            // Master COA
            $masterCoaPermissions = [
                ['name' => 'master-coa-view', 'description' => 'Melihat Data COA'],
                ['name' => 'master-coa-create', 'description' => 'Membuat Data COA'],
                ['name' => 'master-coa-update', 'description' => 'Mengupdate Data COA'],
                ['name' => 'master-coa-delete', 'description' => 'Menghapus Data COA'],
                ['name' => 'master-coa-print', 'description' => 'Print Data COA'],
                ['name' => 'master-coa-export', 'description' => 'Export Data COA'],
                ['name' => 'master-coa', 'description' => 'Akses Master COA'],
            ];
            
            // Master Pekerjaan
            $masterPekerjaanPermissions = [
                ['name' => 'master-pekerjaan-view', 'description' => 'Melihat Data Pekerjaan'],
                ['name' => 'master-pekerjaan-create', 'description' => 'Membuat Data Pekerjaan'],
                ['name' => 'master-pekerjaan-update', 'description' => 'Mengupdate Data Pekerjaan'],
                ['name' => 'master-pekerjaan-delete', 'description' => 'Menghapus Data Pekerjaan'],
                ['name' => 'master-pekerjaan-destroy', 'description' => 'Destroy Data Pekerjaan'], // Alternative delete
                ['name' => 'master-pekerjaan-print', 'description' => 'Print Data Pekerjaan'],
                ['name' => 'master-pekerjaan-export', 'description' => 'Export Data Pekerjaan'],
                ['name' => 'master-pekerjaan', 'description' => 'Akses Master Pekerjaan'],
            ];
            
            // Master Vendor Bengkel
            $masterVendorBengkelPermissions = [
                ['name' => 'master-vendor-bengkel-view', 'description' => 'Melihat Data Vendor Bengkel'],
                ['name' => 'master-vendor-bengkel-create', 'description' => 'Membuat Data Vendor Bengkel'],
                ['name' => 'master-vendor-bengkel-update', 'description' => 'Mengupdate Data Vendor Bengkel'],
                ['name' => 'master-vendor-bengkel-delete', 'description' => 'Menghapus Data Vendor Bengkel'],
                ['name' => 'master-vendor-bengkel-print', 'description' => 'Print Data Vendor Bengkel'],
                ['name' => 'master-vendor-bengkel-export', 'description' => 'Export Data Vendor Bengkel'],
                ['name' => 'master-vendor-bengkel', 'description' => 'Akses Master Vendor Bengkel'],
                
                // Dot notation for special handling in UserController
                ['name' => 'master-vendor-bengkel.view', 'description' => 'View Vendor Bengkel (Dot)'],
                ['name' => 'master-vendor-bengkel.create', 'description' => 'Create Vendor Bengkel (Dot)'],
                ['name' => 'master-vendor-bengkel.update', 'description' => 'Update Vendor Bengkel (Dot)'],
                ['name' => 'master-vendor-bengkel.delete', 'description' => 'Delete Vendor Bengkel (Dot)'],
            ];
            
            // Master Kode Nomor
            $masterKodeNomorPermissions = [
                ['name' => 'master-kode-nomor-view', 'description' => 'Melihat Data Kode Nomor'],
                ['name' => 'master-kode-nomor-create', 'description' => 'Membuat Data Kode Nomor'],
                ['name' => 'master-kode-nomor-update', 'description' => 'Mengupdate Data Kode Nomor'],
                ['name' => 'master-kode-nomor-delete', 'description' => 'Menghapus Data Kode Nomor'],
                ['name' => 'master-kode-nomor-print', 'description' => 'Print Data Kode Nomor'],
                ['name' => 'master-kode-nomor-export', 'description' => 'Export Data Kode Nomor'],
                ['name' => 'master-kode-nomor', 'description' => 'Akses Master Kode Nomor'],
            ];
            
            // Master Stock Kontainer
            $masterStockKontainerPermissions = [
                ['name' => 'master-stock-kontainer-view', 'description' => 'Melihat Data Stock Kontainer'],
                ['name' => 'master-stock-kontainer-create', 'description' => 'Membuat Data Stock Kontainer'],
                ['name' => 'master-stock-kontainer-update', 'description' => 'Mengupdate Data Stock Kontainer'],
                ['name' => 'master-stock-kontainer-delete', 'description' => 'Menghapus Data Stock Kontainer'],
                ['name' => 'master-stock-kontainer-print', 'description' => 'Print Data Stock Kontainer'],
                ['name' => 'master-stock-kontainer-export', 'description' => 'Export Data Stock Kontainer'],
                ['name' => 'master-stock-kontainer', 'description' => 'Akses Master Stock Kontainer'],
            ];
            
            // Master Tipe Akun
            $masterTipeAkunPermissions = [
                ['name' => 'master-tipe-akun-view', 'description' => 'Melihat Data Tipe Akun'],
                ['name' => 'master-tipe-akun-create', 'description' => 'Membuat Data Tipe Akun'],
                ['name' => 'master-tipe-akun-update', 'description' => 'Mengupdate Data Tipe Akun'],
                ['name' => 'master-tipe-akun-delete', 'description' => 'Menghapus Data Tipe Akun'],
                ['name' => 'master-tipe-akun-destroy', 'description' => 'Destroy Data Tipe Akun'], // Alternative delete
                ['name' => 'master-tipe-akun-print', 'description' => 'Print Data Tipe Akun'],
                ['name' => 'master-tipe-akun-export', 'description' => 'Export Data Tipe Akun'],
                ['name' => 'master-tipe-akun', 'description' => 'Akses Master Tipe Akun'],
            ];
            
            // Master Nomor Terakhir
            $masterNomorTerakhirPermissions = [
                ['name' => 'master-nomor-terakhir-view', 'description' => 'Melihat Data Nomor Terakhir'],
                ['name' => 'master-nomor-terakhir-create', 'description' => 'Membuat Data Nomor Terakhir'],
                ['name' => 'master-nomor-terakhir-update', 'description' => 'Mengupdate Data Nomor Terakhir'],
                ['name' => 'master-nomor-terakhir-delete', 'description' => 'Menghapus Data Nomor Terakhir'],
                ['name' => 'master-nomor-terakhir-print', 'description' => 'Print Data Nomor Terakhir'],
                ['name' => 'master-nomor-terakhir-export', 'description' => 'Export Data Nomor Terakhir'],
                ['name' => 'master-nomor-terakhir', 'description' => 'Akses Master Nomor Terakhir'],
            ];
            
            // Master Pricelist Sewa Kontainer
            $masterPricelistSewaKontainerPermissions = [
                ['name' => 'master-pricelist-sewa-kontainer-view', 'description' => 'Melihat Data Pricelist Sewa Kontainer'],
                ['name' => 'master-pricelist-sewa-kontainer-create', 'description' => 'Membuat Data Pricelist Sewa Kontainer'],
                ['name' => 'master-pricelist-sewa-kontainer-update', 'description' => 'Mengupdate Data Pricelist Sewa Kontainer'],
                ['name' => 'master-pricelist-sewa-kontainer-delete', 'description' => 'Menghapus Data Pricelist Sewa Kontainer'],
                ['name' => 'master-pricelist-sewa-kontainer-print', 'description' => 'Print Data Pricelist Sewa Kontainer'],
                ['name' => 'master-pricelist-sewa-kontainer-export', 'description' => 'Export Data Pricelist Sewa Kontainer'],
                ['name' => 'master-pricelist-sewa-kontainer', 'description' => 'Akses Master Pricelist Sewa Kontainer'],
            ];
            
            // Master Pricelist Cat
            $masterPricelistCatPermissions = [
                ['name' => 'master-pricelist-cat-view', 'description' => 'Melihat Data Pricelist Cat'],
                ['name' => 'master-pricelist-cat-create', 'description' => 'Membuat Data Pricelist Cat'],
                ['name' => 'master-pricelist-cat-update', 'description' => 'Mengupdate Data Pricelist Cat'],
                ['name' => 'master-pricelist-cat-delete', 'description' => 'Menghapus Data Pricelist Cat'],
                ['name' => 'master-pricelist-cat-print', 'description' => 'Print Data Pricelist Cat'],
                ['name' => 'master-pricelist-cat-export', 'description' => 'Export Data Pricelist Cat'],
                ['name' => 'master-pricelist-cat', 'description' => 'Akses Master Pricelist Cat'],
            ];
            
            // ========================================================================
            // 6. BUSINESS PROCESS PERMISSIONS - PERMOHONAN
            // ========================================================================
            $permohonanPermissions = [
                // Simple permission (legacy)
                ['name' => 'permohonan', 'description' => 'Akses Permohonan'],
                
                // Memo-specific permissions
                ['name' => 'permohonan-memo-view', 'description' => 'Melihat Permohonan Memo'],
                ['name' => 'permohonan-memo-create', 'description' => 'Membuat Permohonan Memo'],
                ['name' => 'permohonan-memo-update', 'description' => 'Mengupdate Permohonan Memo'],
                ['name' => 'permohonan-memo-delete', 'description' => 'Menghapus Permohonan Memo'],
                ['name' => 'permohonan-memo-print', 'description' => 'Print Permohonan Memo'],
                ['name' => 'permohonan-memo-export', 'description' => 'Export Permohonan Memo'],
                
                // General permohonan actions
                ['name' => 'permohonan-create', 'description' => 'Membuat Permohonan'],
                ['name' => 'permohonan-view', 'description' => 'Melihat Permohonan'],
                ['name' => 'permohonan-edit', 'description' => 'Edit Permohonan'],
                ['name' => 'permohonan-delete', 'description' => 'Hapus Permohonan'],
            ];
            
            // ========================================================================
            // 7. BUSINESS PROCESS PERMISSIONS - PRANOTA & TAGIHAN
            // ========================================================================
            
            // Pranota Supir
            $pranotaSupirPermissions = [
                ['name' => 'pranota-supir-view', 'description' => 'Melihat Pranota Supir'],
                ['name' => 'pranota-supir-create', 'description' => 'Membuat Pranota Supir'],
                ['name' => 'pranota-supir-update', 'description' => 'Mengupdate Pranota Supir'],
                ['name' => 'pranota-supir-delete', 'description' => 'Menghapus Pranota Supir'],
                ['name' => 'pranota-supir-print', 'description' => 'Print Pranota Supir'],
                ['name' => 'pranota-supir-export', 'description' => 'Export Pranota Supir'],
            ];
            
            // Tagihan Kontainer
            $tagihanKontainerPermissions = [
                ['name' => 'tagihan-kontainer-view', 'description' => 'Melihat Tagihan Kontainer'],
                ['name' => 'tagihan-kontainer-create', 'description' => 'Membuat Tagihan Kontainer'],
                ['name' => 'tagihan-kontainer-update', 'description' => 'Mengupdate Tagihan Kontainer'],
                ['name' => 'tagihan-kontainer-delete', 'description' => 'Menghapus Tagihan Kontainer'],
                ['name' => 'tagihan-kontainer-print', 'description' => 'Print Tagihan Kontainer'],
                ['name' => 'tagihan-kontainer-export', 'description' => 'Export Tagihan Kontainer'],
            ];
            
            // Tagihan Kontainer Sewa (from routes analysis)
            $tagihanKontainerSewaPermissions = [
                ['name' => 'tagihan-kontainer-sewa-index', 'description' => 'Index Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa-view', 'description' => 'Melihat Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa-create', 'description' => 'Membuat Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa-update', 'description' => 'Mengupdate Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa-destroy', 'description' => 'Destroy Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa-delete', 'description' => 'Menghapus Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa-print', 'description' => 'Print Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa-export', 'description' => 'Export Tagihan Kontainer Sewa'],
                
                // Alternative naming patterns
                ['name' => 'tagihan-kontainer-sewa-edit', 'description' => 'Edit Tagihan Kontainer Sewa'],
            ];
            
            // Tagihan CAT
            $tagihanCatPermissions = [
                ['name' => 'tagihan-cat-view', 'description' => 'Melihat Tagihan CAT'],
                ['name' => 'tagihan-cat-create', 'description' => 'Membuat Tagihan CAT'],
                ['name' => 'tagihan-cat-update', 'description' => 'Mengupdate Tagihan CAT'],
                ['name' => 'tagihan-cat-delete', 'description' => 'Menghapus Tagihan CAT'],
                ['name' => 'tagihan-cat-print', 'description' => 'Print Tagihan CAT'],
                ['name' => 'tagihan-cat-export', 'description' => 'Export Tagihan CAT'],
            ];
            
            // Tagihan Perbaikan Kontainer
            $tagihanPerbaikanKontainerPermissions = [
                ['name' => 'tagihan-perbaikan-kontainer-view', 'description' => 'Melihat Tagihan Perbaikan Kontainer'],
                ['name' => 'tagihan-perbaikan-kontainer-create', 'description' => 'Membuat Tagihan Perbaikan Kontainer'],
                ['name' => 'tagihan-perbaikan-kontainer-update', 'description' => 'Mengupdate Tagihan Perbaikan Kontainer'],
                ['name' => 'tagihan-perbaikan-kontainer-delete', 'description' => 'Menghapus Tagihan Perbaikan Kontainer'],
                ['name' => 'tagihan-perbaikan-kontainer-approve', 'description' => 'Approve Tagihan Perbaikan Kontainer'],
                ['name' => 'tagihan-perbaikan-kontainer-print', 'description' => 'Print Tagihan Perbaikan Kontainer'],
                ['name' => 'tagihan-perbaikan-kontainer-export', 'description' => 'Export Tagihan Perbaikan Kontainer'],
            ];
            
            // Pranota CAT
            $pranotaCatPermissions = [
                ['name' => 'pranota-cat-view', 'description' => 'Melihat Pranota CAT'],
                ['name' => 'pranota-cat-create', 'description' => 'Membuat Pranota CAT'],
                ['name' => 'pranota-cat-update', 'description' => 'Mengupdate Pranota CAT'],
                ['name' => 'pranota-cat-delete', 'description' => 'Menghapus Pranota CAT'],
                ['name' => 'pranota-cat-print', 'description' => 'Print Pranota CAT'],
                ['name' => 'pranota-cat-export', 'description' => 'Export Pranota CAT'],
            ];
            
            // Pranota Kontainer Sewa
            $pranotaKontainerSewaPermissions = [
                ['name' => 'pranota-kontainer-sewa-view', 'description' => 'Melihat Pranota Kontainer Sewa'],
                ['name' => 'pranota-kontainer-sewa-create', 'description' => 'Membuat Pranota Kontainer Sewa'],
                ['name' => 'pranota-kontainer-sewa-update', 'description' => 'Mengupdate Pranota Kontainer Sewa'],
                ['name' => 'pranota-kontainer-sewa-delete', 'description' => 'Menghapus Pranota Kontainer Sewa'],
                ['name' => 'pranota-kontainer-sewa-print', 'description' => 'Print Pranota Kontainer Sewa'],
                ['name' => 'pranota-kontainer-sewa-export', 'description' => 'Export Pranota Kontainer Sewa'],
            ];
            
            // Pranota Perbaikan Kontainer
            $pranotaPerbaikanKontainerPermissions = [
                ['name' => 'pranota-perbaikan-kontainer-view', 'description' => 'Melihat Pranota Perbaikan Kontainer'],
                ['name' => 'pranota-perbaikan-kontainer-create', 'description' => 'Membuat Pranota Perbaikan Kontainer'],
                ['name' => 'pranota-perbaikan-kontainer-update', 'description' => 'Mengupdate Pranota Perbaikan Kontainer'],
                ['name' => 'pranota-perbaikan-kontainer-delete', 'description' => 'Menghapus Pranota Perbaikan Kontainer'],
                ['name' => 'pranota-perbaikan-kontainer-print', 'description' => 'Print Pranota Perbaikan Kontainer'],
                ['name' => 'pranota-perbaikan-kontainer-export', 'description' => 'Export Pranota Perbaikan Kontainer'],
            ];
            
            // General Pranota permissions
            $pranotaGeneralPermissions = [
                ['name' => 'pranota-view', 'description' => 'Melihat Pranota'],
                ['name' => 'pranota-create', 'description' => 'Membuat Pranota'],
                ['name' => 'pranota-update', 'description' => 'Mengupdate Pranota'],
                ['name' => 'pranota-delete', 'description' => 'Menghapus Pranota'],
                ['name' => 'pranota-print', 'description' => 'Print Pranota'],
                ['name' => 'pranota-export', 'description' => 'Export Pranota'],
            ];
            
            // ========================================================================
            // 8. PAYMENT PERMISSIONS - PEMBAYARAN
            // ========================================================================
            
            // Pembayaran Pranota Supir
            $pembayaranPranotaSupirPermissions = [
                ['name' => 'pembayaran-pranota-supir-view', 'description' => 'Melihat Pembayaran Pranota Supir'],
                ['name' => 'pembayaran-pranota-supir-create', 'description' => 'Membuat Pembayaran Pranota Supir'],
                ['name' => 'pembayaran-pranota-supir-update', 'description' => 'Mengupdate Pembayaran Pranota Supir'],
                ['name' => 'pembayaran-pranota-supir-delete', 'description' => 'Menghapus Pembayaran Pranota Supir'],
                ['name' => 'pembayaran-pranota-supir-print', 'description' => 'Print Pembayaran Pranota Supir'],
                ['name' => 'pembayaran-pranota-supir-export', 'description' => 'Export Pembayaran Pranota Supir'],
            ];
            
            // Pembayaran Pranota Kontainer
            $pembayaranPranotaKontainerPermissions = [
                ['name' => 'pembayaran-pranota-kontainer-view', 'description' => 'Melihat Pembayaran Pranota Kontainer'],
                ['name' => 'pembayaran-pranota-kontainer-create', 'description' => 'Membuat Pembayaran Pranota Kontainer'],
                ['name' => 'pembayaran-pranota-kontainer-update', 'description' => 'Mengupdate Pembayaran Pranota Kontainer'],
                ['name' => 'pembayaran-pranota-kontainer-delete', 'description' => 'Menghapus Pembayaran Pranota Kontainer'],
                ['name' => 'pembayaran-pranota-kontainer-print', 'description' => 'Print Pembayaran Pranota Kontainer'],
                ['name' => 'pembayaran-pranota-kontainer-export', 'description' => 'Export Pembayaran Pranota Kontainer'],
            ];
            
            // Pembayaran Pranota CAT
            $pembayaranPranotaCatPermissions = [
                ['name' => 'pembayaran-pranota-cat-view', 'description' => 'Melihat Pembayaran Pranota CAT'],
                ['name' => 'pembayaran-pranota-cat-create', 'description' => 'Membuat Pembayaran Pranota CAT'],
                ['name' => 'pembayaran-pranota-cat-update', 'description' => 'Mengupdate Pembayaran Pranota CAT'],
                ['name' => 'pembayaran-pranota-cat-delete', 'description' => 'Menghapus Pembayaran Pranota CAT'],
                ['name' => 'pembayaran-pranota-cat-print', 'description' => 'Print Pembayaran Pranota CAT'],
                ['name' => 'pembayaran-pranota-cat-export', 'description' => 'Export Pembayaran Pranota CAT'],
            ];
            
            // Pembayaran Pranota Perbaikan Kontainer
            $pembayaranPranotaPerbaikanKontainerPermissions = [
                ['name' => 'pembayaran-pranota-perbaikan-kontainer-view', 'description' => 'Melihat Pembayaran Pranota Perbaikan Kontainer'],
                ['name' => 'pembayaran-pranota-perbaikan-kontainer-create', 'description' => 'Membuat Pembayaran Pranota Perbaikan Kontainer'],
                ['name' => 'pembayaran-pranota-perbaikan-kontainer-update', 'description' => 'Mengupdate Pembayaran Pranota Perbaikan Kontainer'],
                ['name' => 'pembayaran-pranota-perbaikan-kontainer-delete', 'description' => 'Menghapus Pembayaran Pranota Perbaikan Kontainer'],
                ['name' => 'pembayaran-pranota-perbaikan-kontainer-print', 'description' => 'Print Pembayaran Pranota Perbaikan Kontainer'],
                ['name' => 'pembayaran-pranota-perbaikan-kontainer-export', 'description' => 'Export Pembayaran Pranota Perbaikan Kontainer'],
            ];
            
            // ========================================================================
            // 9. REPAIR & MAINTENANCE PERMISSIONS - PERBAIKAN
            // ========================================================================
            $perbaikanKontainerPermissions = [
                ['name' => 'perbaikan-kontainer-view', 'description' => 'Melihat Perbaikan Kontainer'],
                ['name' => 'perbaikan-kontainer-create', 'description' => 'Membuat Perbaikan Kontainer'],
                ['name' => 'perbaikan-kontainer-update', 'description' => 'Mengupdate Perbaikan Kontainer'],
                ['name' => 'perbaikan-kontainer-delete', 'description' => 'Menghapus Perbaikan Kontainer'],
                ['name' => 'perbaikan-kontainer-print', 'description' => 'Print Perbaikan Kontainer'],
                ['name' => 'perbaikan-kontainer-export', 'description' => 'Export Perbaikan Kontainer'],
            ];
            
            // ========================================================================
            // 10. APPROVAL SYSTEM PERMISSIONS
            // ========================================================================
            $approvalPermissions = [
                // Core approval system
                ['name' => 'approval-view', 'description' => 'Melihat Sistem Approval'],
                ['name' => 'approval-dashboard', 'description' => 'Dashboard Approval'],
                ['name' => 'approval-create', 'description' => 'Membuat Approval'],
                ['name' => 'approval-approve', 'description' => 'Melakukan Approval'],
                ['name' => 'approval-mass_process', 'description' => 'Proses Masal Approval'],
                ['name' => 'approval-riwayat', 'description' => 'Riwayat Approval'],
                ['name' => 'approval-print', 'description' => 'Print Approval'],
                ['name' => 'approval-export', 'description' => 'Export Approval'],
                
                // Approval Tugas Level 1
                ['name' => 'approval-tugas-1.view', 'description' => 'View Approval Tugas Level 1'],
                ['name' => 'approval-tugas-1.approve', 'description' => 'Approve Tugas Level 1'],
                
                // Approval Tugas Level 2
                ['name' => 'approval-tugas-2.view', 'description' => 'View Approval Tugas Level 2'],
                ['name' => 'approval-tugas-2.approve', 'description' => 'Approve Tugas Level 2'],
                
                // User Approval System
                ['name' => 'user-approval', 'description' => 'Sistem Persetujuan User'],
                ['name' => 'user-approval-view', 'description' => 'Melihat User Approval'],
                ['name' => 'user-approval-approve', 'description' => 'Approve User'],
                ['name' => 'user-approval-reject', 'description' => 'Reject User'],
            ];
            
            // ========================================================================
            // 11. PROFILE & USER MANAGEMENT PERMISSIONS
            // ========================================================================
            $profilePermissions = [
                ['name' => 'profile-view', 'description' => 'Melihat Profil'],
                ['name' => 'profile-show', 'description' => 'Show Profil'],
                ['name' => 'profile-update', 'description' => 'Update Profil'],
                ['name' => 'profile-edit', 'description' => 'Edit Profil'],
                ['name' => 'profile-delete', 'description' => 'Hapus Profil'],
                ['name' => 'profile-destroy', 'description' => 'Destroy Profil'],
            ];
            
            // ========================================================================
            // 12. DRIVER SPECIFIC PERMISSIONS - SUPIR
            // ========================================================================
            $supirPermissions = [
                ['name' => 'supir-dashboard', 'description' => 'Dashboard Supir'],
                ['name' => 'supir-checkpoint', 'description' => 'Checkpoint Supir'],
                ['name' => 'supir-view', 'description' => 'Melihat Data Supir'],
                ['name' => 'supir-create', 'description' => 'Membuat Data Supir'],
                ['name' => 'supir-update', 'description' => 'Update Data Supir'],
                ['name' => 'supir-delete', 'description' => 'Hapus Data Supir'],
            ];
            
            // ========================================================================
            // 13. ADMIN SYSTEM PERMISSIONS
            // ========================================================================
            $adminPermissions = [
                ['name' => 'admin-debug', 'description' => 'Debug Admin'],
                ['name' => 'admin-features', 'description' => 'Features Admin'],
                ['name' => 'admin-debug-perms', 'description' => 'Debug Permissions'],
                ['name' => 'admin-view', 'description' => 'View Admin Panel'],
                ['name' => 'admin-create', 'description' => 'Create Admin Data'],
                ['name' => 'admin-update', 'description' => 'Update Admin Data'],
                ['name' => 'admin-delete', 'description' => 'Delete Admin Data'],
            ];
            
            // ========================================================================
            // 14. SPECIAL SYSTEM PERMISSIONS (from UserController analysis)
            // ========================================================================
            $specialSystemPermissions = [
                // Pembayaran Pranota Tagihan Kontainer (from UserController)
                ['name' => 'pembayaran-pranota-tagihan-kontainer.view', 'description' => 'View Pembayaran Pranota Tagihan Kontainer'],
                ['name' => 'pembayaran-pranota-tagihan-kontainer.create', 'description' => 'Create Pembayaran Pranota Tagihan Kontainer'],
                ['name' => 'pembayaran-pranota-tagihan-kontainer.update', 'description' => 'Update Pembayaran Pranota Tagihan Kontainer'],
                ['name' => 'pembayaran-pranota-tagihan-kontainer.delete', 'description' => 'Delete Pembayaran Pranota Tagihan Kontainer'],
                
                // Perbaikan Kontainer (dot notation from UserController)
                ['name' => 'perbaikan-kontainer.view', 'description' => 'View Perbaikan Kontainer (Dot)'],
                ['name' => 'perbaikan-kontainer.create', 'description' => 'Create Perbaikan Kontainer (Dot)'],
                ['name' => 'perbaikan-kontainer.update', 'description' => 'Update Perbaikan Kontainer (Dot)'],
                ['name' => 'perbaikan-kontainer.delete', 'description' => 'Delete Perbaikan Kontainer (Dot)'],
                
                // Tagihan Kontainer Sewa (dot notation)
                ['name' => 'tagihan-kontainer-sewa.view', 'description' => 'View Tagihan Kontainer Sewa (Dot)'],
                ['name' => 'tagihan-kontainer-sewa.create', 'description' => 'Create Tagihan Kontainer Sewa (Dot)'],
                ['name' => 'tagihan-kontainer-sewa.update', 'description' => 'Update Tagihan Kontainer Sewa (Dot)'],
                ['name' => 'tagihan-kontainer-sewa.delete', 'description' => 'Delete Tagihan Kontainer Sewa (Dot)'],
                ['name' => 'tagihan-kontainer-sewa.group_create', 'description' => 'Group Create Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa.group_edit', 'description' => 'Group Edit Tagihan Kontainer Sewa'],
                ['name' => 'tagihan-kontainer-sewa.group_delete', 'description' => 'Group Delete Tagihan Kontainer Sewa'],
                
                // Admin (dot notation from UserController)
                ['name' => 'admin.debug', 'description' => 'Admin Debug (Dot)'],
                ['name' => 'admin.features', 'description' => 'Admin Features (Dot)'],
                ['name' => 'admin.user-approval', 'description' => 'Admin User Approval (Dot)'],
                ['name' => 'admin.user-approval.create', 'description' => 'Admin User Approval Create'],
                ['name' => 'admin.user-approval.update', 'description' => 'Admin User Approval Update'],
                ['name' => 'admin.user-approval.delete', 'description' => 'Admin User Approval Delete'],
                
                // Profile (dot notation from UserController)
                ['name' => 'profile.show', 'description' => 'Profile Show (Dot)'],
                ['name' => 'profile.edit', 'description' => 'Profile Edit (Dot)'],
                ['name' => 'profile.update', 'description' => 'Profile Update (Dot)'],
                ['name' => 'profile.destroy', 'description' => 'Profile Destroy (Dot)'],
                
                // Supir (dot notation from UserController)
                ['name' => 'supir.dashboard', 'description' => 'Supir Dashboard (Dot)'],
                ['name' => 'supir.checkpoint', 'description' => 'Supir Checkpoint (Dot)'],
                
                // Approval (dot notation from UserController)
                ['name' => 'approval.dashboard', 'description' => 'Approval Dashboard (Dot)'],
                ['name' => 'approval.mass_process', 'description' => 'Approval Mass Process (Dot)'],
                ['name' => 'approval.create', 'description' => 'Approval Create (Dot)'],
                ['name' => 'approval.riwayat', 'description' => 'Approval Riwayat (Dot)'],
                ['name' => 'approval.view', 'description' => 'Approval View (Dot)'],
                ['name' => 'approval.approve', 'description' => 'Approval Approve (Dot)'],
                ['name' => 'approval.print', 'description' => 'Approval Print (Dot)'],
                ['name' => 'approval.export', 'description' => 'Approval Export (Dot)'],
            ];
            
            // ========================================================================
            // COMBINE ALL PERMISSIONS
            // ========================================================================
            $allPermissions = array_merge(
                $systemPermissions,
                $masterUserPermissions,
                $masterKaryawanPermissions,
                $masterKontainerPermissions,
                $masterTujuanPermissions,
                $masterKegiatanPermissions,
                $masterPermissionPermissions,
                $masterMobilPermissions,
                $masterBankPermissions,
                $masterDivisiPermissions,
                $masterPajakPermissions,
                $masterCabangPermissions,
                $masterCoaPermissions,
                $masterPekerjaanPermissions,
                $masterVendorBengkelPermissions,
                $masterKodeNomorPermissions,
                $masterStockKontainerPermissions,
                $masterTipeAkunPermissions,
                $masterNomorTerakhirPermissions,
                $masterPricelistSewaKontainerPermissions,
                $masterPricelistCatPermissions,
                $permohonanPermissions,
                $pranotaSupirPermissions,
                $tagihanKontainerPermissions,
                $tagihanKontainerSewaPermissions,
                $tagihanCatPermissions,
                $tagihanPerbaikanKontainerPermissions,
                $pranotaCatPermissions,
                $pranotaKontainerSewaPermissions,
                $pranotaPerbaikanKontainerPermissions,
                $pranotaGeneralPermissions,
                $pembayaranPranotaSupirPermissions,
                $pembayaranPranotaKontainerPermissions,
                $pembayaranPranotaCatPermissions,
                $pembayaranPranotaPerbaikanKontainerPermissions,
                $perbaikanKontainerPermissions,
                $approvalPermissions,
                $profilePermissions,
                $supirPermissions,
                $adminPermissions,
                $specialSystemPermissions
            );
            
            // ========================================================================
            // INSERT PERMISSIONS
            // ========================================================================
            $this->command->info('Creating ' . count($allPermissions) . ' permissions...');
            
            $insertedCount = 0;
            foreach ($allPermissions as $permission) {
                // Check if permission already exists
                $existingPermission = Permission::where('name', $permission['name'])->first();
                
                if (!$existingPermission) {
                    Permission::create([
                        'name' => $permission['name'],
                        'description' => $permission['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $insertedCount++;
                } else {
                    // Update description if it's different
                    if ($existingPermission->description !== $permission['description']) {
                        $existingPermission->update(['description' => $permission['description']]);
                    }
                }
            }
            
            DB::commit();
            
            $this->command->info("✅ Successfully processed {$insertedCount} new permissions");
            $this->command->info("📊 Total permissions in system: " . Permission::count());
            
            // Show summary by category
            $this->showPermissionSummary();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating permissions: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Show permission summary by category
     */
    private function showPermissionSummary()
    {
        $this->command->info("\n📋 Permission Summary by Category:");
        $this->command->info("==========================================");
        
        $categories = [
            'System' => ['dashboard', 'login', 'logout', 'storage'],
            'Master User' => ['master-user', 'master.user'],
            'Master Karyawan' => ['master-karyawan', 'master.karyawan'],
            'Master Kontainer' => ['master-kontainer', 'master.kontainer'],
            'Master Other' => ['master-tujuan', 'master-kegiatan', 'master-permission', 'master-mobil', 'master-bank', 'master-divisi', 'master-pajak', 'master-cabang', 'master-coa', 'master-pekerjaan', 'master-vendor', 'master-kode', 'master-stock', 'master-tipe', 'master-nomor', 'master-pricelist'],
            'Permohonan' => ['permohonan'],
            'Pranota' => ['pranota'],
            'Tagihan' => ['tagihan'],
            'Pembayaran' => ['pembayaran'],
            'Perbaikan' => ['perbaikan'],
            'Approval' => ['approval', 'user-approval'],
            'Profile' => ['profile'],
            'Supir' => ['supir'],
            'Admin' => ['admin'],
        ];
        
        foreach ($categories as $category => $patterns) {
            $count = 0;
            foreach ($patterns as $pattern) {
                $count += Permission::where('name', 'LIKE', "%{$pattern}%")->count();
            }
            $this->command->info("   {$category}: {$count} permissions");
        }
    }
}