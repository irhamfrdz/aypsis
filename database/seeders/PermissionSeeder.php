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
     * Seeder ini mencakup semua permission yang digunakan dalam UserController.php
     * dengan berbagai format naming convention (dot notation, dash notation, dll)
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing permissions (optional - uncomment if you want to start fresh)
        // Permission::truncate();
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Definisi semua permissions berdasarkan UserController.php
        $permissions = [
            // ============================================
            // SYSTEM & AUTH PERMISSIONS
            // ============================================
            [
                'name' => 'dashboard',
                'description' => 'Akses ke dashboard utama sistem'
            ],
            [
                'name' => 'login',
                'description' => 'Izin untuk login ke sistem'
            ],
            [
                'name' => 'logout',
                'description' => 'Izin untuk logout dari sistem'
            ],

            // ============================================
            // MASTER KARYAWAN PERMISSIONS
            // ============================================
            [
                'name' => 'master-karyawan',
                'description' => 'Akses modul Master Karyawan'
            ],
            [
                'name' => 'master.karyawan.index',
                'description' => 'Lihat daftar karyawan'
            ],
            [
                'name' => 'master.karyawan.create',
                'description' => 'Tambah karyawan baru'
            ],
            [
                'name' => 'master.karyawan.show',
                'description' => 'Lihat detail karyawan'
            ],
            [
                'name' => 'master.karyawan.edit',
                'description' => 'Edit karyawan'
            ],
            [
                'name' => 'master.karyawan.update',
                'description' => 'Update data karyawan'
            ],
            [
                'name' => 'master.karyawan.destroy',
                'description' => 'Hapus karyawan'
            ],
            [
                'name' => 'master.karyawan.print',
                'description' => 'Cetak data karyawan'
            ],
            [
                'name' => 'master.karyawan.export',
                'description' => 'Export data karyawan'
            ],
            [
                'name' => 'master-karyawan-view',
                'description' => 'Lihat karyawan (dash notation)'
            ],
            [
                'name' => 'master-karyawan-create',
                'description' => 'Tambah karyawan (dash notation)'
            ],
            [
                'name' => 'master-karyawan-update',
                'description' => 'Update karyawan (dash notation)'
            ],
            [
                'name' => 'master-karyawan-destroy',
                'description' => 'Hapus karyawan (dash notation)'
            ],
            [
                'name' => 'master-karyawan-print',
                'description' => 'Cetak karyawan (dash notation)'
            ],
            [
                'name' => 'master-karyawan-export',
                'description' => 'Export karyawan (dash notation)'
            ],

            // ============================================
            // MASTER KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'master-kontainer',
                'description' => 'Akses modul Master Kontainer'
            ],
            [
                'name' => 'master.kontainer.index',
                'description' => 'Lihat daftar kontainer'
            ],
            [
                'name' => 'master.kontainer.create',
                'description' => 'Tambah kontainer baru'
            ],
            [
                'name' => 'master.kontainer.show',
                'description' => 'Lihat detail kontainer'
            ],
            [
                'name' => 'master.kontainer.edit',
                'description' => 'Edit kontainer'
            ],
            [
                'name' => 'master.kontainer.update',
                'description' => 'Update data kontainer'
            ],
            [
                'name' => 'master.kontainer.destroy',
                'description' => 'Hapus kontainer'
            ],
            [
                'name' => 'master.kontainer.print',
                'description' => 'Cetak data kontainer'
            ],
            [
                'name' => 'master.kontainer.export',
                'description' => 'Export data kontainer'
            ],
            [
                'name' => 'master-kontainer-view',
                'description' => 'Lihat kontainer (dash notation)'
            ],
            [
                'name' => 'master-kontainer-create',
                'description' => 'Tambah kontainer (dash notation)'
            ],
            [
                'name' => 'master-kontainer-update',
                'description' => 'Update kontainer (dash notation)'
            ],
            [
                'name' => 'master-kontainer-destroy',
                'description' => 'Hapus kontainer (dash notation)'
            ],
            [
                'name' => 'master-kontainer-print',
                'description' => 'Cetak kontainer (dash notation)'
            ],
            [
                'name' => 'master-kontainer-export',
                'description' => 'Export kontainer (dash notation)'
            ],

            // ============================================
            // MASTER TUJUAN PERMISSIONS
            // ============================================
            [
                'name' => 'master-tujuan',
                'description' => 'Akses modul Master Tujuan'
            ],
            [
                'name' => 'master.tujuan.index',
                'description' => 'Lihat daftar tujuan'
            ],
            [
                'name' => 'master.tujuan.create',
                'description' => 'Tambah tujuan baru'
            ],
            [
                'name' => 'master.tujuan.show',
                'description' => 'Lihat detail tujuan'
            ],
            [
                'name' => 'master.tujuan.edit',
                'description' => 'Edit tujuan'
            ],
            [
                'name' => 'master.tujuan.update',
                'description' => 'Update data tujuan'
            ],
            [
                'name' => 'master.tujuan.destroy',
                'description' => 'Hapus tujuan'
            ],
            [
                'name' => 'master.tujuan.print',
                'description' => 'Cetak data tujuan'
            ],
            [
                'name' => 'master.tujuan.export',
                'description' => 'Export data tujuan'
            ],
            [
                'name' => 'master-tujuan-view',
                'description' => 'Lihat tujuan (dash notation)'
            ],
            [
                'name' => 'master-tujuan-create',
                'description' => 'Tambah tujuan (dash notation)'
            ],
            [
                'name' => 'master-tujuan-update',
                'description' => 'Update tujuan (dash notation)'
            ],
            [
                'name' => 'master-tujuan-destroy',
                'description' => 'Hapus tujuan (dash notation)'
            ],
            [
                'name' => 'master-tujuan-print',
                'description' => 'Cetak tujuan (dash notation)'
            ],
            [
                'name' => 'master-tujuan-export',
                'description' => 'Export tujuan (dash notation)'
            ],

            // ============================================
            // MASTER KEGIATAN PERMISSIONS
            // ============================================
            [
                'name' => 'master-kegiatan',
                'description' => 'Akses modul Master Kegiatan'
            ],
            [
                'name' => 'master.kegiatan.index',
                'description' => 'Lihat daftar kegiatan'
            ],
            [
                'name' => 'master.kegiatan.create',
                'description' => 'Tambah kegiatan baru'
            ],
            [
                'name' => 'master.kegiatan.show',
                'description' => 'Lihat detail kegiatan'
            ],
            [
                'name' => 'master.kegiatan.edit',
                'description' => 'Edit kegiatan'
            ],
            [
                'name' => 'master.kegiatan.update',
                'description' => 'Update data kegiatan'
            ],
            [
                'name' => 'master.kegiatan.destroy',
                'description' => 'Hapus kegiatan'
            ],
            [
                'name' => 'master.kegiatan.print',
                'description' => 'Cetak data kegiatan'
            ],
            [
                'name' => 'master.kegiatan.export',
                'description' => 'Export data kegiatan'
            ],
            [
                'name' => 'master-kegiatan-view',
                'description' => 'Lihat kegiatan (dash notation)'
            ],
            [
                'name' => 'master-kegiatan-create',
                'description' => 'Tambah kegiatan (dash notation)'
            ],
            [
                'name' => 'master-kegiatan-update',
                'description' => 'Update kegiatan (dash notation)'
            ],
            [
                'name' => 'master-kegiatan-destroy',
                'description' => 'Hapus kegiatan (dash notation)'
            ],
            [
                'name' => 'master-kegiatan-print',
                'description' => 'Cetak kegiatan (dash notation)'
            ],
            [
                'name' => 'master-kegiatan-export',
                'description' => 'Export kegiatan (dash notation)'
            ],

            // ============================================
            // MASTER PERMISSION PERMISSIONS
            // ============================================
            [
                'name' => 'master-permission',
                'description' => 'Akses modul Master Permission'
            ],
            [
                'name' => 'master.permission.index',
                'description' => 'Lihat daftar permission'
            ],
            [
                'name' => 'master.permission.create',
                'description' => 'Tambah permission baru'
            ],
            [
                'name' => 'master.permission.show',
                'description' => 'Lihat detail permission'
            ],
            [
                'name' => 'master.permission.edit',
                'description' => 'Edit permission'
            ],
            [
                'name' => 'master.permission.update',
                'description' => 'Update data permission'
            ],
            [
                'name' => 'master.permission.destroy',
                'description' => 'Hapus permission'
            ],
            [
                'name' => 'master.permission.print',
                'description' => 'Cetak data permission'
            ],
            [
                'name' => 'master.permission.export',
                'description' => 'Export data permission'
            ],
            [
                'name' => 'master-permission-view',
                'description' => 'Lihat permission (dash notation)'
            ],
            [
                'name' => 'master-permission-create',
                'description' => 'Tambah permission (dash notation)'
            ],
            [
                'name' => 'master-permission-update',
                'description' => 'Update permission (dash notation)'
            ],
            [
                'name' => 'master-permission-destroy',
                'description' => 'Hapus permission (dash notation)'
            ],
            [
                'name' => 'master-permission-print',
                'description' => 'Cetak permission (dash notation)'
            ],
            [
                'name' => 'master-permission-export',
                'description' => 'Export permission (dash notation)'
            ],

            // ============================================
            // MASTER MOBIL PERMISSIONS
            // ============================================
            [
                'name' => 'master-mobil',
                'description' => 'Akses modul Master Mobil'
            ],
            [
                'name' => 'master.mobil.index',
                'description' => 'Lihat daftar mobil'
            ],
            [
                'name' => 'master.mobil.create',
                'description' => 'Tambah mobil baru'
            ],
            [
                'name' => 'master.mobil.show',
                'description' => 'Lihat detail mobil'
            ],
            [
                'name' => 'master.mobil.edit',
                'description' => 'Edit mobil'
            ],
            [
                'name' => 'master.mobil.update',
                'description' => 'Update data mobil'
            ],
            [
                'name' => 'master.mobil.destroy',
                'description' => 'Hapus mobil'
            ],
            [
                'name' => 'master.mobil.print',
                'description' => 'Cetak data mobil'
            ],
            [
                'name' => 'master.mobil.export',
                'description' => 'Export data mobil'
            ],
            [
                'name' => 'master-mobil-view',
                'description' => 'Lihat mobil (dash notation)'
            ],
            [
                'name' => 'master-mobil-create',
                'description' => 'Tambah mobil (dash notation)'
            ],
            [
                'name' => 'master-mobil-update',
                'description' => 'Update mobil (dash notation)'
            ],
            [
                'name' => 'master-mobil-destroy',
                'description' => 'Hapus mobil (dash notation)'
            ],
            [
                'name' => 'master-mobil-print',
                'description' => 'Cetak mobil (dash notation)'
            ],
            [
                'name' => 'master-mobil-export',
                'description' => 'Export mobil (dash notation)'
            ],

            // ============================================
            // MASTER KODE NOMOR PERMISSIONS
            // ============================================
            [
                'name' => 'master-kode-nomor',
                'description' => 'Akses modul Master Kode Nomor'
            ],
            [
                'name' => 'master.kode-nomor.index',
                'description' => 'Lihat daftar kode nomor'
            ],
            [
                'name' => 'master.kode-nomor.create',
                'description' => 'Tambah kode nomor baru'
            ],
            [
                'name' => 'master.kode-nomor.show',
                'description' => 'Lihat detail kode nomor'
            ],
            [
                'name' => 'master.kode-nomor.edit',
                'description' => 'Edit kode nomor'
            ],
            [
                'name' => 'master.kode-nomor.update',
                'description' => 'Update data kode nomor'
            ],
            [
                'name' => 'master.kode-nomor.destroy',
                'description' => 'Hapus kode nomor'
            ],
            [
                'name' => 'master.kode-nomor.print',
                'description' => 'Cetak data kode nomor'
            ],
            [
                'name' => 'master.kode-nomor.export',
                'description' => 'Export data kode nomor'
            ],
            [
                'name' => 'master-kode-nomor-view',
                'description' => 'Lihat kode nomor (dash notation)'
            ],
            [
                'name' => 'master-kode-nomor-create',
                'description' => 'Tambah kode nomor (dash notation)'
            ],
            [
                'name' => 'master-kode-nomor-update',
                'description' => 'Update kode nomor (dash notation)'
            ],
            [
                'name' => 'master-kode-nomor-destroy',
                'description' => 'Hapus kode nomor (dash notation)'
            ],
            [
                'name' => 'master-kode-nomor-print',
                'description' => 'Cetak kode nomor (dash notation)'
            ],
            [
                'name' => 'master-kode-nomor-export',
                'description' => 'Export kode nomor (dash notation)'
            ],

            // ============================================
            // MASTER STOCK KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'master-stock-kontainer',
                'description' => 'Akses modul Master Stock Kontainer'
            ],
            [
                'name' => 'master.stock-kontainer.index',
                'description' => 'Lihat daftar stock kontainer'
            ],
            [
                'name' => 'master.stock-kontainer.create',
                'description' => 'Tambah stock kontainer baru'
            ],
            [
                'name' => 'master.stock-kontainer.show',
                'description' => 'Lihat detail stock kontainer'
            ],
            [
                'name' => 'master.stock-kontainer.edit',
                'description' => 'Edit stock kontainer'
            ],
            [
                'name' => 'master.stock-kontainer.update',
                'description' => 'Update data stock kontainer'
            ],
            [
                'name' => 'master.stock-kontainer.destroy',
                'description' => 'Hapus stock kontainer'
            ],
            [
                'name' => 'master-stock-kontainer-view',
                'description' => 'Lihat stock kontainer (dash notation)'
            ],
            [
                'name' => 'master-stock-kontainer-create',
                'description' => 'Tambah stock kontainer (dash notation)'
            ],
            [
                'name' => 'master-stock-kontainer-update',
                'description' => 'Update stock kontainer (dash notation)'
            ],
            [
                'name' => 'master-stock-kontainer-delete',
                'description' => 'Hapus stock kontainer (dash notation)'
            ],

            // ============================================
            // MASTER NOMOR TERAKHIR PERMISSIONS
            // ============================================
            [
                'name' => 'master-nomor-terakhir',
                'description' => 'Akses modul Master Nomor Terakhir'
            ],
            [
                'name' => 'master.nomor-terakhir.index',
                'description' => 'Lihat daftar nomor terakhir'
            ],
            [
                'name' => 'master.nomor-terakhir.create',
                'description' => 'Tambah nomor terakhir baru'
            ],
            [
                'name' => 'master.nomor-terakhir.show',
                'description' => 'Lihat detail nomor terakhir'
            ],
            [
                'name' => 'master.nomor-terakhir.edit',
                'description' => 'Edit nomor terakhir'
            ],
            [
                'name' => 'master.nomor-terakhir.update',
                'description' => 'Update data nomor terakhir'
            ],
            [
                'name' => 'master.nomor-terakhir.destroy',
                'description' => 'Hapus nomor terakhir'
            ],
            [
                'name' => 'master-nomor-terakhir-view',
                'description' => 'Lihat nomor terakhir (dash notation)'
            ],
            [
                'name' => 'master-nomor-terakhir-create',
                'description' => 'Tambah nomor terakhir (dash notation)'
            ],
            [
                'name' => 'master-nomor-terakhir-update',
                'description' => 'Update nomor terakhir (dash notation)'
            ],
            [
                'name' => 'master-nomor-terakhir-delete',
                'description' => 'Hapus nomor terakhir (dash notation)'
            ],

            // ============================================
            // MASTER DIVISI PERMISSIONS
            // ============================================
            [
                'name' => 'master-divisi',
                'description' => 'Akses modul Master Divisi'
            ],
            [
                'name' => 'master.divisi.index',
                'description' => 'Lihat daftar divisi'
            ],
            [
                'name' => 'master.divisi.create',
                'description' => 'Tambah divisi baru'
            ],
            [
                'name' => 'master.divisi.show',
                'description' => 'Lihat detail divisi'
            ],
            [
                'name' => 'master.divisi.edit',
                'description' => 'Edit divisi'
            ],
            [
                'name' => 'master.divisi.update',
                'description' => 'Update data divisi'
            ],
            [
                'name' => 'master.divisi.destroy',
                'description' => 'Hapus divisi'
            ],
            [
                'name' => 'master.divisi.print',
                'description' => 'Cetak data divisi'
            ],
            [
                'name' => 'master.divisi.export',
                'description' => 'Export data divisi'
            ],
            [
                'name' => 'master-divisi-view',
                'description' => 'Lihat divisi (dash notation)'
            ],
            [
                'name' => 'master-divisi-create',
                'description' => 'Tambah divisi (dash notation)'
            ],
            [
                'name' => 'master-divisi-update',
                'description' => 'Update divisi (dash notation)'
            ],
            [
                'name' => 'master-divisi-destroy',
                'description' => 'Hapus divisi (dash notation)'
            ],
            [
                'name' => 'master-divisi-print',
                'description' => 'Cetak divisi (dash notation)'
            ],
            [
                'name' => 'master-divisi-export',
                'description' => 'Export divisi (dash notation)'
            ],

            // ============================================
            // MASTER USER PERMISSIONS
            // ============================================
            [
                'name' => 'master-user',
                'description' => 'Akses modul Master User'
            ],
            [
                'name' => 'master.user.index',
                'description' => 'Lihat daftar user'
            ],
            [
                'name' => 'master.user.create',
                'description' => 'Tambah user baru'
            ],
            [
                'name' => 'master.user.show',
                'description' => 'Lihat detail user'
            ],
            [
                'name' => 'master.user.edit',
                'description' => 'Edit user'
            ],
            [
                'name' => 'master.user.update',
                'description' => 'Update data user'
            ],
            [
                'name' => 'master.user.destroy',
                'description' => 'Hapus user'
            ],
            [
                'name' => 'master.user.print',
                'description' => 'Cetak data user'
            ],
            [
                'name' => 'master.user.export',
                'description' => 'Export data user'
            ],
            [
                'name' => 'master-user-view',
                'description' => 'Lihat user (dash notation)'
            ],
            [
                'name' => 'master-user-create',
                'description' => 'Tambah user (dash notation)'
            ],
            [
                'name' => 'master-user-update',
                'description' => 'Update user (dash notation)'
            ],
            [
                'name' => 'master-user-destroy',
                'description' => 'Hapus user (dash notation)'
            ],
            [
                'name' => 'master-user-print',
                'description' => 'Cetak user (dash notation)'
            ],
            [
                'name' => 'master-user-export',
                'description' => 'Export user (dash notation)'
            ],

            // ============================================
            // MASTER PEKERJAAN PERMISSIONS
            // ============================================
            [
                'name' => 'master-pekerjaan',
                'description' => 'Akses modul Master Pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan-view',
                'description' => 'Lihat pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan-create',
                'description' => 'Tambah pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan-update',
                'description' => 'Update pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan-destroy',
                'description' => 'Hapus pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan-print',
                'description' => 'Cetak pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan-export',
                'description' => 'Export pekerjaan'
            ],

            // ============================================
            // MASTER PAJAK PERMISSIONS
            // ============================================
            [
                'name' => 'master-pajak',
                'description' => 'Akses modul Master Pajak'
            ],
            [
                'name' => 'master-pajak-view',
                'description' => 'Lihat pajak'
            ],
            [
                'name' => 'master-pajak-create',
                'description' => 'Tambah pajak'
            ],
            [
                'name' => 'master-pajak-update',
                'description' => 'Update pajak'
            ],
            [
                'name' => 'master-pajak-destroy',
                'description' => 'Hapus pajak'
            ],

            // ============================================
            // MASTER BANK PERMISSIONS
            // ============================================
            [
                'name' => 'master-bank',
                'description' => 'Akses modul Master Bank'
            ],
            [
                'name' => 'master-bank-view',
                'description' => 'Lihat bank'
            ],
            [
                'name' => 'master-bank-create',
                'description' => 'Tambah bank'
            ],
            [
                'name' => 'master-bank-update',
                'description' => 'Update bank'
            ],
            [
                'name' => 'master-bank-destroy',
                'description' => 'Hapus bank'
            ],

            // ============================================
            // MASTER COA PERMISSIONS
            // ============================================
            [
                'name' => 'master-coa',
                'description' => 'Akses modul Master COA (Chart of Accounts)'
            ],
            [
                'name' => 'master-coa-view',
                'description' => 'Lihat COA'
            ],
            [
                'name' => 'master-coa-create',
                'description' => 'Tambah COA'
            ],
            [
                'name' => 'master-coa-update',
                'description' => 'Update COA'
            ],
            [
                'name' => 'master-coa-delete',
                'description' => 'Hapus COA'
            ],

            // ============================================
            // MASTER TIPE AKUN PERMISSIONS
            // ============================================
            [
                'name' => 'master-tipe-akun',
                'description' => 'Akses modul Master Tipe Akun'
            ],
            [
                'name' => 'master-tipe-akun-view',
                'description' => 'Lihat tipe akun'
            ],
            [
                'name' => 'master-tipe-akun-create',
                'description' => 'Tambah tipe akun'
            ],
            [
                'name' => 'master-tipe-akun-update',
                'description' => 'Update tipe akun'
            ],
            [
                'name' => 'master-tipe-akun-delete',
                'description' => 'Hapus tipe akun'
            ],

            // ============================================
            // MASTER CABANG PERMISSIONS
            // ============================================
            [
                'name' => 'master-cabang',
                'description' => 'Akses modul Master Cabang'
            ],
            [
                'name' => 'master-cabang-view',
                'description' => 'Lihat cabang'
            ],
            [
                'name' => 'master-cabang-create',
                'description' => 'Tambah cabang'
            ],
            [
                'name' => 'master-cabang-update',
                'description' => 'Update cabang'
            ],
            [
                'name' => 'master-cabang-delete',
                'description' => 'Hapus cabang'
            ],

            // ============================================
            // MASTER VENDOR BENGKEL PERMISSIONS
            // ============================================
            [
                'name' => 'master-vendor-bengkel',
                'description' => 'Akses modul Master Vendor Bengkel'
            ],
            [
                'name' => 'master-vendor-bengkel.view',
                'description' => 'Lihat vendor bengkel (dot notation)'
            ],
            [
                'name' => 'master-vendor-bengkel.create',
                'description' => 'Tambah vendor bengkel (dot notation)'
            ],
            [
                'name' => 'master-vendor-bengkel.update',
                'description' => 'Update vendor bengkel (dot notation)'
            ],
            [
                'name' => 'master-vendor-bengkel.delete',
                'description' => 'Hapus vendor bengkel (dot notation)'
            ],
            [
                'name' => 'master-vendor-bengkel-view',
                'description' => 'Lihat vendor bengkel (dash notation)'
            ],
            [
                'name' => 'master-vendor-bengkel-create',
                'description' => 'Tambah vendor bengkel (dash notation)'
            ],
            [
                'name' => 'master-vendor-bengkel-update',
                'description' => 'Update vendor bengkel (dash notation)'
            ],
            [
                'name' => 'master-vendor-bengkel-delete',
                'description' => 'Hapus vendor bengkel (dash notation)'
            ],

            // ============================================
            // MASTER PRICELIST SEWA KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'master-pricelist-sewa-kontainer',
                'description' => 'Akses modul Master Pricelist Sewa Kontainer'
            ],
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
                'description' => 'Update pricelist sewa kontainer'
            ],
            [
                'name' => 'master-pricelist-sewa-kontainer-delete',
                'description' => 'Hapus pricelist sewa kontainer'
            ],

            // ============================================
            // MASTER PRICELIST CAT PERMISSIONS
            // ============================================
            [
                'name' => 'master-pricelist-cat',
                'description' => 'Akses modul Master Pricelist Cat'
            ],
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
                'description' => 'Update pricelist cat'
            ],
            [
                'name' => 'master-pricelist-cat-delete',
                'description' => 'Hapus pricelist cat'
            ],

            // ============================================
            // ADMIN PERMISSIONS
            // ============================================
            [
                'name' => 'admin',
                'description' => 'Akses modul Admin'
            ],
            [
                'name' => 'admin-debug',
                'description' => 'Akses debug tools admin'
            ],
            [
                'name' => 'admin-features',
                'description' => 'Akses fitur admin'
            ],
            [
                'name' => 'admin.debug',
                'description' => 'Akses debug admin (dot notation)'
            ],
            [
                'name' => 'admin.features',
                'description' => 'Akses features admin (dot notation)'
            ],

            // ============================================
            // USER APPROVAL PERMISSIONS
            // ============================================
            [
                'name' => 'user-approval',
                'description' => 'Akses modul User Approval'
            ],
            [
                'name' => 'user-approval-view',
                'description' => 'Lihat user approval'
            ],
            [
                'name' => 'user-approval-create',
                'description' => 'Tambah user approval'
            ],
            [
                'name' => 'user-approval-update',
                'description' => 'Update user approval'
            ],
            [
                'name' => 'user-approval-delete',
                'description' => 'Hapus user approval'
            ],

            // ============================================
            // PRANOTA SUPIR PERMISSIONS
            // ============================================
            [
                'name' => 'pranota-supir',
                'description' => 'Akses modul Pranota Supir'
            ],
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
                'description' => 'Update pranota supir'
            ],
            [
                'name' => 'pranota-supir-delete',
                'description' => 'Hapus pranota supir'
            ],
            [
                'name' => 'pranota-supir-print',
                'description' => 'Cetak pranota supir'
            ],
            [
                'name' => 'pranota-supir-export',
                'description' => 'Export pranota supir'
            ],

            // ============================================
            // PEMBAYARAN PRANOTA SUPIR PERMISSIONS
            // ============================================
            [
                'name' => 'pembayaran-pranota-supir',
                'description' => 'Akses modul Pembayaran Pranota Supir'
            ],
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
                'description' => 'Update pembayaran pranota supir'
            ],
            [
                'name' => 'pembayaran-pranota-supir-delete',
                'description' => 'Hapus pembayaran pranota supir'
            ],
            [
                'name' => 'pembayaran-pranota-supir-print',
                'description' => 'Cetak pembayaran pranota supir'
            ],
            [
                'name' => 'pembayaran-pranota-supir-export',
                'description' => 'Export pembayaran pranota supir'
            ],

            // ============================================
            // PEMBAYARAN PRANOTA TAGIHAN KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'pembayaran-pranota-tagihan-kontainer',
                'description' => 'Akses modul Pembayaran Pranota Tagihan Kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-tagihan-kontainer.view',
                'description' => 'Lihat pembayaran pranota tagihan kontainer (dot notation)'
            ],
            [
                'name' => 'pembayaran-pranota-tagihan-kontainer.create',
                'description' => 'Tambah pembayaran pranota tagihan kontainer (dot notation)'
            ],
            [
                'name' => 'pembayaran-pranota-tagihan-kontainer.update',
                'description' => 'Update pembayaran pranota tagihan kontainer (dot notation)'
            ],
            [
                'name' => 'pembayaran-pranota-tagihan-kontainer.delete',
                'description' => 'Hapus pembayaran pranota tagihan kontainer (dot notation)'
            ],

            // ============================================
            // PEMBAYARAN PRANOTA KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'pembayaran-pranota-kontainer',
                'description' => 'Akses modul Pembayaran Pranota Kontainer'
            ],
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
                'description' => 'Update pembayaran pranota kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-kontainer-delete',
                'description' => 'Hapus pembayaran pranota kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-kontainer-print',
                'description' => 'Cetak pembayaran pranota kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-kontainer-export',
                'description' => 'Export pembayaran pranota kontainer'
            ],

            // ============================================
            // PEMBAYARAN PRANOTA CAT PERMISSIONS
            // ============================================
            [
                'name' => 'pembayaran-pranota-cat',
                'description' => 'Akses modul Pembayaran Pranota Cat'
            ],
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
                'description' => 'Update pembayaran pranota cat'
            ],
            [
                'name' => 'pembayaran-pranota-cat-delete',
                'description' => 'Hapus pembayaran pranota cat'
            ],
            [
                'name' => 'pembayaran-pranota-cat-print',
                'description' => 'Cetak pembayaran pranota cat'
            ],
            [
                'name' => 'pembayaran-pranota-cat-export',
                'description' => 'Export pembayaran pranota cat'
            ],

            // ============================================
            // PERBAIKAN KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'perbaikan-kontainer',
                'description' => 'Akses modul Perbaikan Kontainer'
            ],
            [
                'name' => 'perbaikan-kontainer-view',
                'description' => 'Lihat perbaikan kontainer'
            ],
            [
                'name' => 'perbaikan-kontainer-create',
                'description' => 'Tambah perbaikan kontainer'
            ],
            [
                'name' => 'perbaikan-kontainer-update',
                'description' => 'Update perbaikan kontainer'
            ],
            [
                'name' => 'perbaikan-kontainer-delete',
                'description' => 'Hapus perbaikan kontainer'
            ],
            [
                'name' => 'perbaikan-kontainer.view',
                'description' => 'Lihat perbaikan kontainer (dot notation)'
            ],
            [
                'name' => 'perbaikan-kontainer.create',
                'description' => 'Tambah perbaikan kontainer (dot notation)'
            ],
            [
                'name' => 'perbaikan-kontainer.update',
                'description' => 'Update perbaikan kontainer (dot notation)'
            ],
            [
                'name' => 'perbaikan-kontainer.delete',
                'description' => 'Hapus perbaikan kontainer (dot notation)'
            ],

            // ============================================
            // PRANOTA PERBAIKAN KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'pranota-perbaikan-kontainer',
                'description' => 'Akses modul Pranota Perbaikan Kontainer'
            ],
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
                'description' => 'Update pranota perbaikan kontainer'
            ],
            [
                'name' => 'pranota-perbaikan-kontainer-delete',
                'description' => 'Hapus pranota perbaikan kontainer'
            ],
            [
                'name' => 'pranota-perbaikan-kontainer-print',
                'description' => 'Cetak pranota perbaikan kontainer'
            ],
            [
                'name' => 'pranota-perbaikan-kontainer-export',
                'description' => 'Export pranota perbaikan kontainer'
            ],

            // ============================================
            // PEMBAYARAN PRANOTA PERBAIKAN KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer',
                'description' => 'Akses modul Pembayaran Pranota Perbaikan Kontainer'
            ],
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
                'description' => 'Update pembayaran pranota perbaikan kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer-delete',
                'description' => 'Hapus pembayaran pranota perbaikan kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer-print',
                'description' => 'Cetak pembayaran pranota perbaikan kontainer'
            ],
            [
                'name' => 'pembayaran-pranota-perbaikan-kontainer-export',
                'description' => 'Export pembayaran pranota perbaikan kontainer'
            ],

            // ============================================
            // TAGIHAN PERBAIKAN KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'tagihan-perbaikan-kontainer',
                'description' => 'Akses modul Tagihan Perbaikan Kontainer'
            ],
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
                'description' => 'Update tagihan perbaikan kontainer'
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-delete',
                'description' => 'Hapus tagihan perbaikan kontainer'
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-approve',
                'description' => 'Approve tagihan perbaikan kontainer'
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-print',
                'description' => 'Cetak tagihan perbaikan kontainer'
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-export',
                'description' => 'Export tagihan perbaikan kontainer'
            ],

            // ============================================
            // PERMOHONAN MEMO PERMISSIONS
            // ============================================
            [
                'name' => 'permohonan-memo',
                'description' => 'Akses modul Permohonan Memo'
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
                'name' => 'permohonan-memo-edit',
                'description' => 'Edit permohonan memo'
            ],
            [
                'name' => 'permohonan-memo-update',
                'description' => 'Update permohonan memo'
            ],
            [
                'name' => 'permohonan-memo-delete',
                'description' => 'Hapus permohonan memo'
            ],
            [
                'name' => 'permohonan-memo-print',
                'description' => 'Cetak permohonan memo'
            ],

            // ============================================
            // PERMOHONAN PERMISSIONS
            // ============================================
            [
                'name' => 'permohonan',
                'description' => 'Akses modul Permohonan'
            ],
            [
                'name' => 'permohonan-view',
                'description' => 'Lihat permohonan'
            ],
            [
                'name' => 'permohonan-create',
                'description' => 'Tambah permohonan'
            ],
            [
                'name' => 'permohonan-edit',
                'description' => 'Edit permohonan'
            ],
            [
                'name' => 'permohonan-delete',
                'description' => 'Hapus permohonan'
            ],

            // ============================================
            // PROFILE PERMISSIONS
            // ============================================
            [
                'name' => 'profile',
                'description' => 'Akses modul Profile'
            ],
            [
                'name' => 'profile-show',
                'description' => 'Lihat profile'
            ],
            [
                'name' => 'profile-edit',
                'description' => 'Edit profile'
            ],
            [
                'name' => 'profile-update',
                'description' => 'Update profile'
            ],
            [
                'name' => 'profile-destroy',
                'description' => 'Hapus profile'
            ],
            [
                'name' => 'profile.show',
                'description' => 'Lihat profile (dot notation)'
            ],
            [
                'name' => 'profile.edit',
                'description' => 'Edit profile (dot notation)'
            ],
            [
                'name' => 'profile.update',
                'description' => 'Update profile (dot notation)'
            ],
            [
                'name' => 'profile.destroy',
                'description' => 'Hapus profile (dot notation)'
            ],

            // ============================================
            // SUPIR PERMISSIONS
            // ============================================
            [
                'name' => 'supir',
                'description' => 'Akses modul Supir'
            ],
            [
                'name' => 'supir-dashboard',
                'description' => 'Akses dashboard supir'
            ],
            [
                'name' => 'supir-checkpoint',
                'description' => 'Akses checkpoint supir'
            ],
            [
                'name' => 'supir.dashboard',
                'description' => 'Akses dashboard supir (dot notation)'
            ],
            [
                'name' => 'supir.checkpoint',
                'description' => 'Akses checkpoint supir (dot notation)'
            ],

            // ============================================
            // APPROVAL PERMISSIONS
            // ============================================
            [
                'name' => 'approval',
                'description' => 'Akses modul Approval'
            ],
            [
                'name' => 'approval-dashboard',
                'description' => 'Akses dashboard approval'
            ],
            [
                'name' => 'approval-view',
                'description' => 'Lihat approval'
            ],
            [
                'name' => 'approval-create',
                'description' => 'Tambah approval'
            ],
            [
                'name' => 'approval-approve',
                'description' => 'Approve dokumen'
            ],
            [
                'name' => 'approval-print',
                'description' => 'Cetak approval'
            ],
            [
                'name' => 'approval-mass_process',
                'description' => 'Mass process approval'
            ],
            [
                'name' => 'approval-riwayat',
                'description' => 'Lihat riwayat approval'
            ],
            [
                'name' => 'approval.dashboard',
                'description' => 'Dashboard approval (dot notation)'
            ],
            [
                'name' => 'approval.mass_process',
                'description' => 'Mass process approval (dot notation)'
            ],
            [
                'name' => 'approval.create',
                'description' => 'Tambah approval (dot notation)'
            ],
            [
                'name' => 'approval.riwayat',
                'description' => 'Riwayat approval (dot notation)'
            ],

            // ============================================
            // APPROVAL TUGAS PERMISSIONS
            // ============================================
            [
                'name' => 'approval-tugas-1',
                'description' => 'Akses approval tugas level 1'
            ],
            [
                'name' => 'approval-tugas-1.view',
                'description' => 'Lihat approval tugas level 1'
            ],
            [
                'name' => 'approval-tugas-1.approve',
                'description' => 'Approve tugas level 1'
            ],
            [
                'name' => 'approval-tugas-2',
                'description' => 'Akses approval tugas level 2'
            ],
            [
                'name' => 'approval-tugas-2.view',
                'description' => 'Lihat approval tugas level 2'
            ],
            [
                'name' => 'approval-tugas-2.approve',
                'description' => 'Approve tugas level 2'
            ],

            // ============================================
            // STORAGE PERMISSIONS
            // ============================================
            [
                'name' => 'storage',
                'description' => 'Akses modul Storage'
            ],
            [
                'name' => 'storage-local',
                'description' => 'Akses storage lokal'
            ],

            // ============================================
            // TAGIHAN KONTAINER PERMISSIONS
            // ============================================
            [
                'name' => 'tagihan-kontainer',
                'description' => 'Akses modul Tagihan Kontainer'
            ],
            [
                'name' => 'tagihan-kontainer-view',
                'description' => 'Lihat tagihan kontainer'
            ],
            [
                'name' => 'tagihan-kontainer-create',
                'description' => 'Tambah tagihan kontainer'
            ],
            [
                'name' => 'tagihan-kontainer-update',
                'description' => 'Update tagihan kontainer'
            ],
            [
                'name' => 'tagihan-kontainer-delete',
                'description' => 'Hapus tagihan kontainer'
            ],
            [
                'name' => 'tagihan-kontainer-export',
                'description' => 'Export tagihan kontainer'
            ],

            // ============================================
            // TAGIHAN CAT PERMISSIONS
            // ============================================
            [
                'name' => 'tagihan-cat',
                'description' => 'Akses modul Tagihan Cat'
            ],
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
                'description' => 'Update tagihan cat'
            ],
            [
                'name' => 'tagihan-cat-delete',
                'description' => 'Hapus tagihan cat'
            ],

            // ============================================
            // PRANOTA CAT PERMISSIONS
            // ============================================
            [
                'name' => 'pranota-cat',
                'description' => 'Akses modul Pranota Cat'
            ],
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
                'description' => 'Update pranota cat'
            ],
            [
                'name' => 'pranota-cat-delete',
                'description' => 'Hapus pranota cat'
            ],

            // ============================================
            // TAGIHAN KONTAINER SEWA PERMISSIONS
            // ============================================
            [
                'name' => 'tagihan-kontainer-sewa',
                'description' => 'Akses modul Tagihan Kontainer Sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-index',
                'description' => 'Lihat daftar tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-view',
                'description' => 'Lihat tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-create',
                'description' => 'Tambah tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-edit',
                'description' => 'Edit tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-update',
                'description' => 'Update tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-destroy',
                'description' => 'Hapus tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa-export',
                'description' => 'Export tagihan kontainer sewa'
            ],
            [
                'name' => 'tagihan-kontainer-sewa.group',
                'description' => 'Group tagihan kontainer sewa'
            ],

            // ============================================
            // PRANOTA KONTAINER SEWA PERMISSIONS
            // ============================================
            [
                'name' => 'pranota-kontainer-sewa',
                'description' => 'Akses modul Pranota Kontainer Sewa'
            ],
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
                'description' => 'Update pranota kontainer sewa'
            ],
            [
                'name' => 'pranota-kontainer-sewa-delete',
                'description' => 'Hapus pranota kontainer sewa'
            ],
            [
                'name' => 'pranota-kontainer-sewa-print',
                'description' => 'Cetak pranota kontainer sewa'
            ],
            [
                'name' => 'pranota-kontainer-sewa-export',
                'description' => 'Export pranota kontainer sewa'
            ],
        ];

        // Hanya insert permissions yang belum ada
        $existingPermissions = Permission::pluck('name')->toArray();
        $newPermissions = [];

        foreach ($permissions as $permission) {
            if (!in_array($permission['name'], $existingPermissions)) {
                $permission['created_at'] = now();
                $permission['updated_at'] = now();
                $newPermissions[] = $permission;
            }
        }

        // Batch insert untuk performa lebih baik
        if (!empty($newPermissions)) {
            foreach (array_chunk($newPermissions, 100) as $chunk) {
                Permission::insert($chunk);
            }
            
            $this->command->info('✅ Successfully seeded ' . count($newPermissions) . ' new permissions!');
        } else {
            $this->command->info('ℹ️ All permissions already exist in the database.');
        }
    }
}
