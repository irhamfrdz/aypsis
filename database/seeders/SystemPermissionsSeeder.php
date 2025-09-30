<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class SystemPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah permission sudah ada untuk menghindari duplikasi
        $existingPermissions = Permission::pluck('name')->toArray();
        $newPermissions = [];

        // Daftar semua permissions yang digunakan dalam sistem
        $allPermissions = [
            // Master modules
            ['name' => 'master-karyawan', 'description' => 'Akses modul Master Karyawan'],
            ['name' => 'master-karyawan-view', 'description' => 'Melihat data Master Karyawan'],
            ['name' => 'master-karyawan-create', 'description' => 'Membuat data Master Karyawan'],
            ['name' => 'master-karyawan-update', 'description' => 'Mengupdate data Master Karyawan'],
            ['name' => 'master-karyawan-delete', 'description' => 'Menghapus data Master Karyawan'],
            ['name' => 'master-karyawan-print', 'description' => 'Mencetak data Master Karyawan'],
            ['name' => 'master-karyawan-export', 'description' => 'Mengekspor data Master Karyawan'],
            ['name' => 'master-karyawan-import', 'description' => 'Mengimpor data Master Karyawan'],
            ['name' => 'master.karyawan.index', 'description' => 'Akses halaman index Master Karyawan'],
            ['name' => 'master.karyawan.create', 'description' => 'Akses halaman create Master Karyawan'],
            ['name' => 'master.karyawan.store', 'description' => 'Menyimpan data Master Karyawan'],
            ['name' => 'master.karyawan.show', 'description' => 'Melihat detail Master Karyawan'],
            ['name' => 'master.karyawan.edit', 'description' => 'Akses halaman edit Master Karyawan'],
            ['name' => 'master.karyawan.update', 'description' => 'Mengupdate data Master Karyawan'],
            ['name' => 'master.karyawan.destroy', 'description' => 'Menghapus data Master Karyawan'],
            ['name' => 'master.karyawan.print', 'description' => 'Mencetak Master Karyawan'],
            ['name' => 'master.karyawan.export', 'description' => 'Mengekspor Master Karyawan'],
            ['name' => 'master.karyawan.import', 'description' => 'Mengimpor Master Karyawan'],
            ['name' => 'master.karyawan.approve', 'description' => 'Menyetujui Master Karyawan'],

            ['name' => 'master-user', 'description' => 'Akses modul Master User'],
            ['name' => 'master-user-view', 'description' => 'Melihat data Master User'],
            ['name' => 'master-user-create', 'description' => 'Membuat data Master User'],
            ['name' => 'master-user-update', 'description' => 'Mengupdate data Master User'],
            ['name' => 'master-user-delete', 'description' => 'Menghapus data Master User'],

            ['name' => 'master-kontainer', 'description' => 'Akses modul Master Kontainer'],
            ['name' => 'master-kontainer-view', 'description' => 'Melihat data Master Kontainer'],

            ['name' => 'master-tujuan', 'description' => 'Akses modul Master Tujuan'],
            ['name' => 'master-tujuan-view', 'description' => 'Melihat data Master Tujuan'],

            ['name' => 'master-kegiatan', 'description' => 'Akses modul Master Kegiatan'],
            ['name' => 'master-kegiatan-view', 'description' => 'Melihat data Master Kegiatan'],

            ['name' => 'master-permission', 'description' => 'Akses modul Master Permission'],
            ['name' => 'master-permission-view', 'description' => 'Melihat data Master Permission'],

            ['name' => 'master-mobil', 'description' => 'Akses modul Master Mobil'],
            ['name' => 'master-mobil-view', 'description' => 'Melihat data Master Mobil'],

            ['name' => 'master-pranota', 'description' => 'Akses modul Master Pranota'],
            ['name' => 'master-pranota-tagihan-kontainer', 'description' => 'Akses modul Master Pranota Tagihan Kontainer'],
            ['name' => 'master-pembayaran-pranota-supir', 'description' => 'Akses modul Master Pembayaran Pranota Supir'],

            ['name' => 'master-pekerjaan', 'description' => 'Akses modul Master Pekerjaan'],
            ['name' => 'master-pekerjaan-view', 'description' => 'Melihat data Master Pekerjaan'],
            ['name' => 'master-pekerjaan-create', 'description' => 'Membuat data Master Pekerjaan'],
            ['name' => 'master-pekerjaan-update', 'description' => 'Mengupdate data Master Pekerjaan'],
            ['name' => 'master-pekerjaan-delete', 'description' => 'Menghapus data Master Pekerjaan'],
            ['name' => 'master-pekerjaan-print', 'description' => 'Mencetak data Master Pekerjaan'],
            ['name' => 'master-pekerjaan-export', 'description' => 'Mengekspor data Master Pekerjaan'],

            ['name' => 'master-pajak', 'description' => 'Akses modul Master Pajak'],
            ['name' => 'master-pajak-view', 'description' => 'Melihat data Master Pajak'],
            ['name' => 'master-pajak-create', 'description' => 'Membuat data Master Pajak'],
            ['name' => 'master-pajak-update', 'description' => 'Mengupdate data Master Pajak'],
            ['name' => 'master-pajak-delete', 'description' => 'Menghapus data Master Pajak'],

            ['name' => 'master-bank', 'description' => 'Akses modul Master Bank'],
            ['name' => 'master-bank-view', 'description' => 'Melihat data Master Bank'],
            ['name' => 'master-bank-create', 'description' => 'Membuat data Master Bank'],
            ['name' => 'master-bank-update', 'description' => 'Mengupdate data Master Bank'],
            ['name' => 'master-bank-delete', 'description' => 'Menghapus data Master Bank'],

            ['name' => 'master-coa', 'description' => 'Akses modul Master COA'],
            ['name' => 'master-coa-view', 'description' => 'Melihat data Master COA'],
            ['name' => 'master-coa-create', 'description' => 'Membuat data Master COA'],
            ['name' => 'master-coa-update', 'description' => 'Mengupdate data Master COA'],
            ['name' => 'master-coa-delete', 'description' => 'Menghapus data Master COA'],

            ['name' => 'master-tipe-akun', 'description' => 'Akses modul Master Tipe Akun'],
            ['name' => 'master-tipe-akun-view', 'description' => 'Melihat data Master Tipe Akun'],
            ['name' => 'master-tipe-akun-create', 'description' => 'Membuat data Master Tipe Akun'],
            ['name' => 'master-tipe-akun-update', 'description' => 'Mengupdate data Master Tipe Akun'],
            ['name' => 'master-tipe-akun-delete', 'description' => 'Menghapus data Master Tipe Akun'],

            ['name' => 'master-cabang', 'description' => 'Akses modul Master Cabang'],
            ['name' => 'master-cabang-view', 'description' => 'Melihat data Master Cabang'],
            ['name' => 'master-cabang-create', 'description' => 'Membuat data Master Cabang'],
            ['name' => 'master-cabang-update', 'description' => 'Mengupdate data Master Cabang'],
            ['name' => 'master-cabang-delete', 'description' => 'Menghapus data Master Cabang'],

            ['name' => 'master-vendor-bengkel', 'description' => 'Akses modul Master Vendor Bengkel'],
            ['name' => 'master-vendor-bengkel-view', 'description' => 'Melihat data Master Vendor Bengkel'],
            ['name' => 'master-vendor-bengkel-create', 'description' => 'Membuat data Master Vendor Bengkel'],
            ['name' => 'master-vendor-bengkel-update', 'description' => 'Mengupdate data Master Vendor Bengkel'],
            ['name' => 'master-vendor-bengkel-delete', 'description' => 'Menghapus data Master Vendor Bengkel'],

            ['name' => 'master-pricelist-sewa-kontainer', 'description' => 'Akses modul Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer-view', 'description' => 'Melihat data Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer-create', 'description' => 'Membuat data Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer-update', 'description' => 'Mengupdate data Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer-delete', 'description' => 'Menghapus data Master Pricelist Sewa Kontainer'],

            ['name' => 'master-pricelist-cat', 'description' => 'Akses modul Master Pricelist CAT'],
            ['name' => 'master-pricelist-cat-view', 'description' => 'Melihat data Master Pricelist CAT'],
            ['name' => 'master-pricelist-cat-create', 'description' => 'Membuat data Master Pricelist CAT'],
            ['name' => 'master-pricelist-cat-update', 'description' => 'Mengupdate data Master Pricelist CAT'],
            ['name' => 'master-pricelist-cat-delete', 'description' => 'Menghapus data Master Pricelist CAT'],

            ['name' => 'master-stock-kontainer', 'description' => 'Akses modul Master Stock Kontainer'],
            ['name' => 'master-stock-kontainer-view', 'description' => 'Melihat data Master Stock Kontainer'],
            ['name' => 'master-stock-kontainer-create', 'description' => 'Membuat data Master Stock Kontainer'],
            ['name' => 'master-stock-kontainer-update', 'description' => 'Mengupdate data Master Stock Kontainer'],
            ['name' => 'master-stock-kontainer-delete', 'description' => 'Menghapus data Master Stock Kontainer'],

            ['name' => 'master-kode-nomor', 'description' => 'Akses modul Master Kode Nomor'],
            ['name' => 'master-kode-nomor-view', 'description' => 'Melihat data Master Kode Nomor'],
            ['name' => 'master-kode-nomor-create', 'description' => 'Membuat data Master Kode Nomor'],
            ['name' => 'master-kode-nomor-update', 'description' => 'Mengupdate data Master Kode Nomor'],
            ['name' => 'master-kode-nomor-delete', 'description' => 'Menghapus data Master Kode Nomor'],

            // Tagihan modules
            ['name' => 'tagihan-kontainer', 'description' => 'Akses modul Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-view', 'description' => 'Melihat data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-create', 'description' => 'Membuat data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-update', 'description' => 'Mengupdate data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-delete', 'description' => 'Menghapus data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-print', 'description' => 'Mencetak Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-export', 'description' => 'Mengekspor data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-import', 'description' => 'Mengimpor data Tagihan Kontainer Sewa'],

            ['name' => 'tagihan-cat', 'description' => 'Akses modul Tagihan CAT'],
            ['name' => 'tagihan-cat-view', 'description' => 'Melihat data Tagihan CAT'],
            ['name' => 'tagihan-cat-create', 'description' => 'Membuat data Tagihan CAT'],
            ['name' => 'tagihan-cat-update', 'description' => 'Mengupdate data Tagihan CAT'],
            ['name' => 'tagihan-cat-delete', 'description' => 'Menghapus data Tagihan CAT'],
            ['name' => 'tagihan-cat-print', 'description' => 'Mencetak Tagihan CAT'],
            ['name' => 'tagihan-cat-export', 'description' => 'Mengekspor data Tagihan CAT'],
            ['name' => 'tagihan-cat-import', 'description' => 'Mengimpor data Tagihan CAT'],
            ['name' => 'tagihan-cat-approve', 'description' => 'Menyetujui Tagihan CAT'],

            ['name' => 'tagihan-kontainer-sewa', 'description' => 'Akses modul Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-view', 'description' => 'Melihat data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-create', 'description' => 'Membuat data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-update', 'description' => 'Mengupdate data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-delete', 'description' => 'Menghapus data Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-print', 'description' => 'Mencetak Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-export', 'description' => 'Mengekspor data Tagihan Kontainer Sewa'],

            ['name' => 'tagihan-perbaikan-kontainer', 'description' => 'Akses modul Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-view', 'description' => 'Melihat data Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-create', 'description' => 'Membuat data Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-update', 'description' => 'Mengupdate data Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-delete', 'description' => 'Menghapus data Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-print', 'description' => 'Mencetak Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-export', 'description' => 'Mengekspor data Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-approve', 'description' => 'Menyetujui Tagihan Perbaikan Kontainer'],

            // Pranota modules
            ['name' => 'pranota-supir', 'description' => 'Akses modul Pranota Supir'],
            ['name' => 'pranota-supir-view', 'description' => 'Melihat data Pranota Supir'],
            ['name' => 'pranota-supir-create', 'description' => 'Membuat data Pranota Supir'],
            ['name' => 'pranota-supir-update', 'description' => 'Mengupdate data Pranota Supir'],
            ['name' => 'pranota-supir-delete', 'description' => 'Menghapus data Pranota Supir'],
            ['name' => 'pranota-supir-print', 'description' => 'Mencetak Pranota Supir'],
            ['name' => 'pranota-supir-export', 'description' => 'Mengekspor data Pranota Supir'],

            ['name' => 'pranota-cat', 'description' => 'Akses modul Pranota CAT'],
            ['name' => 'pranota-cat-view', 'description' => 'Melihat data Pranota CAT'],
            ['name' => 'pranota-cat-create', 'description' => 'Membuat data Pranota CAT'],
            ['name' => 'pranota-cat-update', 'description' => 'Mengupdate data Pranota CAT'],
            ['name' => 'pranota-cat-delete', 'description' => 'Menghapus data Pranota CAT'],
            ['name' => 'pranota-cat-print', 'description' => 'Mencetak Pranota CAT'],
            ['name' => 'pranota-cat-export', 'description' => 'Mengekspor data Pranota CAT'],

            ['name' => 'pranota-kontainer-sewa', 'description' => 'Akses modul Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-view', 'description' => 'Melihat data Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-create', 'description' => 'Membuat data Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-update', 'description' => 'Mengupdate data Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-delete', 'description' => 'Menghapus data Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-print', 'description' => 'Mencetak Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-export', 'description' => 'Mengekspor data Pranota Kontainer Sewa'],

            ['name' => 'pranota-perbaikan-kontainer', 'description' => 'Akses modul Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-view', 'description' => 'Melihat data Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-create', 'description' => 'Membuat data Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-update', 'description' => 'Mengupdate data Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-delete', 'description' => 'Menghapus data Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-print', 'description' => 'Mencetak Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-export', 'description' => 'Mengekspor data Pranota Perbaikan Kontainer'],

            // Pembayaran modules
            ['name' => 'pembayaran-pranota-supir', 'description' => 'Akses modul Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-view', 'description' => 'Melihat data Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-create', 'description' => 'Membuat data Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-update', 'description' => 'Mengupdate data Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-delete', 'description' => 'Menghapus data Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-print', 'description' => 'Mencetak Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-export', 'description' => 'Mengekspor data Pembayaran Pranota Supir'],

            ['name' => 'pembayaran-pranota-kontainer', 'description' => 'Akses modul Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-view', 'description' => 'Melihat data Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-create', 'description' => 'Membuat data Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-update', 'description' => 'Mengupdate data Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-delete', 'description' => 'Menghapus data Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-print', 'description' => 'Mencetak Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-export', 'description' => 'Mengekspor data Pembayaran Pranota Kontainer'],

            ['name' => 'pembayaran-pranota-cat', 'description' => 'Akses modul Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-view', 'description' => 'Melihat data Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-create', 'description' => 'Membuat data Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-update', 'description' => 'Mengupdate data Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-delete', 'description' => 'Menghapus data Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-print', 'description' => 'Mencetak Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-export', 'description' => 'Mengekspor data Pembayaran Pranota CAT'],

            ['name' => 'pembayaran-pranota-tagihan-kontainer', 'description' => 'Akses modul Pembayaran Pranota Tagihan Kontainer'],
            ['name' => 'pembayaran-pranota-tagihan-kontainer-view', 'description' => 'Melihat data Pembayaran Pranota Tagihan Kontainer'],
            ['name' => 'pembayaran-pranota-tagihan-kontainer-create', 'description' => 'Membuat data Pembayaran Pranota Tagihan Kontainer'],
            ['name' => 'pembayaran-pranota-tagihan-kontainer-update', 'description' => 'Mengupdate data Pembayaran Pranota Tagihan Kontainer'],
            ['name' => 'pembayaran-pranota-tagihan-kontainer-delete', 'description' => 'Menghapus data Pembayaran Pranota Tagihan Kontainer'],
            ['name' => 'pembayaran-pranota-tagihan-kontainer-print', 'description' => 'Mencetak Pembayaran Pranota Tagihan Kontainer'],
            ['name' => 'pembayaran-pranota-tagihan-kontainer-export', 'description' => 'Mengekspor data Pembayaran Pranota Tagihan Kontainer'],

            ['name' => 'pembayaran-pranota-perbaikan-kontainer', 'description' => 'Akses modul Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-view', 'description' => 'Melihat data Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-create', 'description' => 'Membuat data Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-update', 'description' => 'Mengupdate data Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-delete', 'description' => 'Menghapus data Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-print', 'description' => 'Mencetak Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-export', 'description' => 'Mengekspor data Pembayaran Pranota Perbaikan Kontainer'],

            // Perbaikan modules
            ['name' => 'perbaikan-kontainer', 'description' => 'Akses modul Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-view', 'description' => 'Melihat data Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-create', 'description' => 'Membuat data Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-update', 'description' => 'Mengupdate data Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-delete', 'description' => 'Menghapus data Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-print', 'description' => 'Mencetak Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-export', 'description' => 'Mengekspor data Perbaikan Kontainer'],

            // Other modules
            ['name' => 'permohonan', 'description' => 'Akses modul Permohonan'],
            ['name' => 'permohonan-create', 'description' => 'Membuat Permohonan'],
            ['name' => 'permohonan-view', 'description' => 'Melihat Permohonan'],
            ['name' => 'permohonan-edit', 'description' => 'Mengedit Permohonan'],
            ['name' => 'permohonan-delete', 'description' => 'Menghapus Permohonan'],

            ['name' => 'permohonan-memo', 'description' => 'Akses modul Permohonan Memo'],
            ['name' => 'permohonan-memo-create', 'description' => 'Membuat Permohonan Memo'],
            ['name' => 'permohonan-memo-view', 'description' => 'Melihat Permohonan Memo'],
            ['name' => 'permohonan-memo-edit', 'description' => 'Mengedit Permohonan Memo'],
            ['name' => 'permohonan-memo-delete', 'description' => 'Menghapus Permohonan Memo'],

            ['name' => 'profile', 'description' => 'Akses modul Profile'],
            ['name' => 'profile-show', 'description' => 'Melihat Profile'],
            ['name' => 'profile-edit', 'description' => 'Mengedit Profile'],
            ['name' => 'profile-update', 'description' => 'Mengupdate Profile'],
            ['name' => 'profile-destroy', 'description' => 'Menghapus Profile'],

            ['name' => 'supir', 'description' => 'Akses modul Supir'],
            ['name' => 'supir-dashboard', 'description' => 'Akses Dashboard Supir'],
            ['name' => 'supir-checkpoint', 'description' => 'Akses Checkpoint Supir'],

            ['name' => 'approval', 'description' => 'Akses modul Approval'],
            ['name' => 'approval-dashboard', 'description' => 'Akses Dashboard Approval'],
            ['name' => 'approval-mass_process', 'description' => 'Proses Massal Approval'],
            ['name' => 'approval-create', 'description' => 'Membuat Approval'],
            ['name' => 'approval-riwayat', 'description' => 'Melihat Riwayat Approval'],
            ['name' => 'approval-view', 'description' => 'Melihat Approval'],
            ['name' => 'approval-approve', 'description' => 'Menyetujui Approval'],
            ['name' => 'approval-print', 'description' => 'Mencetak Approval'],

            ['name' => 'admin', 'description' => 'Akses modul Admin'],
            ['name' => 'admin-debug', 'description' => 'Debug Admin'],
            ['name' => 'admin-features', 'description' => 'Fitur Admin'],

            ['name' => 'user-approval', 'description' => 'Akses modul User Approval'],
            ['name' => 'user-approval-view', 'description' => 'Melihat User Approval'],

            ['name' => 'storage-local', 'description' => 'Akses Storage Lokal'],

            ['name' => 'login', 'description' => 'Login'],
            ['name' => 'logout', 'description' => 'Logout'],
        ];

        // Filter permissions yang belum ada
        foreach ($allPermissions as $permission) {
            if (!in_array($permission['name'], $existingPermissions)) {
                $newPermissions[] = $permission;
            }
        }

        // Insert permissions baru
        if (!empty($newPermissions)) {
            foreach ($newPermissions as $permission) {
                Permission::create([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                ]);
            }

            $this->command->info('Created ' . count($newPermissions) . ' new permissions:');
            foreach ($newPermissions as $permission) {
                $this->command->line('  - ' . $permission['name'] . ': ' . $permission['description']);
            }
        } else {
            $this->command->info('No new permissions to create.');
        }
    }
}
