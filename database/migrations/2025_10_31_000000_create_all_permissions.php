<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            // Dashboard & Approval
            'approval-dashboard',
            'approval-tugas-1',
            'surat-jalan-approval-dashboard',

            // Master Data - User & Karyawan
            'master-user-view',
            'master-user-create',
            'master-user-update',
            'master-user-delete',
            'master-user-bulk-manage',
            'master-karyawan',
            'master-karyawan-view',
            'master-karyawan-create',
            'master-karyawan-update',
            'master-karyawan-delete',
            'master-karyawan-print',
            'master-karyawan-export',

            // Master Data - Divisi & Organisasi
            'master-divisi-view',
            'master-divisi-create',
            'master-divisi-update',
            'master-divisi-delete',
            'master-cabang-view',
            'master-pekerjaan-view',
            'master-pekerjaan-create',
            'master-pekerjaan-update',
            'master-pekerjaan-destroy',

            // Master Data - Kontainer & Stock
            'master-kontainer-view',
            'master-kontainer-create',
            'master-kontainer-update',
            'master-kontainer-delete',
            'master-stock-kontainer-view',
            'master-stock-kontainer-create',
            'master-stock-kontainer-update',
            'master-stock-kontainer-delete',

            // Master Data - Keuangan
            'master-bank-view',
            'master-bank-create',
            'master-bank-update',
            'master-bank-destroy',
            'master-pajak-view',
            'master-pajak-create',
            'master-pajak-update',
            'master-pajak-destroy',
            'master-coa-view',
            'master-coa-create',
            'master-tipe-akun-view',
            'master-kode-nomor-view',
            'master-nomor-terakhir-view',

            // Master Data - Kegiatan & Tujuan
            'master-kegiatan-view',
            'master-kegiatan-create',
            'master-kegiatan-update',
            'master-kegiatan-delete',
            'master-tujuan-view',
            'master-tujuan-create',
            'master-tujuan-update',
            'master-tujuan-delete',
            'master-tujuan-print',
            'master-tujuan-export',
            'master-tujuan-kirim-view',
            'master-tujuan-kirim-create',
            'master-tujuan-kirim-update',
            'master-tujuan-kirim-delete',

            // Master Data - Transport & Logistik
            'master-mobil-view',
            'master-mobil-create',
            'master-mobil-update',
            'master-mobil-delete',
            'master-kapal',
            'master-pelabuhan-view',
            'master-pelabuhan-create',
            'master-pelabuhan-edit',
            'master-pelabuhan-delete',

            // Master Data - Customer & Vendor
            'master-pengirim-view',
            'master-pengirim-create',
            'master-pengirim-update',
            'master-pengirim-delete',
            'master-vendor-bengkel-view',
            'master-vendor-bengkel-create',
            'master-vendor-bengkel-update',
            'master-vendor-bengkel-delete',
            'master-jenis-barang-view',
            'master-jenis-barang-create',
            'master-jenis-barang-update',
            'master-jenis-barang-delete',
            'master-term-view',
            'master-term-create',
            'master-term-update',
            'master-term-delete',

            // Master Data - Pricelist
            'master-pricelist-sewa-kontainer-view',
            'master-pricelist-sewa-kontainer-create',
            'master-pricelist-sewa-kontainer-update',
            'master-pricelist-sewa-kontainer-delete',
            'master-pricelist-cat-view',
            'master-pricelist-cat-create',
            'master-pricelist-cat-update',
            'master-pricelist-cat-delete',
            'master-pricelist-gate-in-view',
            'master-pricelist-gate-in-create',
            'master-pricelist-gate-in-update',
            'master-pricelist-gate-in-delete',

            // Master Data - Permission Management
            'master-permission-view',
            'master-permission-create',
            'master-permission-update',
            'master-permission-delete',

            // Operational - Permohonan
            'permohonan',
            'permohonan-memo-view',
            'permohonan-memo-create',
            'permohonan-memo-update',
            'permohonan-memo-delete',
            'permohonan-memo-print',

            // Operational - Order & Surat Jalan
            'order-view',
            'order-create',
            'order-update',
            'order-delete',
            'surat-jalan-view',
            'surat-jalan-create',
            'surat-jalan-update',
            'surat-jalan-delete',

            // Operational - Gate In
            'gate-in-view',
            'gate-in-create',
            'gate-in-update',
            'gate-in-delete',

            // Operational - Tanda Terima
            'tanda-terima-view',
            'tanda-terima-edit',
            'tanda-terima-delete',
            'tanda-terima-tanpa-surat-jalan-view',
            'tanda-terima-tanpa-surat-jalan-create',
            'tanda-terima-tanpa-surat-jalan-update',
            'tanda-terima-tanpa-surat-jalan-delete',

            // Operational - BL & Prospek
            'bl-view',
            'bl-create',
            'bl-edit',
            'prospek-view',
            'prospek-edit',

            // Operational - Pergerakan Kapal
            'pergerakan-kapal-view',
            'pergerakan-kapal-create',
            'pergerakan-kapal-update',
            'pergerakan-kapal-delete',

            // Pranota - General
            'pranota-view',
            'pranota-create',
            'pranota-update',
            'pranota-delete',
            'pranota-print',

            // Pranota - Supir
            'pranota-supir-view',
            'pranota-supir-create',
            'pranota-supir-update',
            'pranota-supir-delete',
            'pranota-supir-print',

            // Pranota - Kontainer Sewa
            'pranota-kontainer-sewa-view',
            'pranota-kontainer-sewa-create',
            'pranota-kontainer-sewa-update',
            'pranota-kontainer-sewa-edit',
            'pranota-kontainer-sewa-delete',
            'pranota-kontainer-sewa-print',

            // Pranota - Cat
            'pranota-cat-view',
            'pranota-cat-create',
            'pranota-cat-update',
            'pranota-cat-delete',
            'pranota-cat-print',

            // Pranota - Perbaikan Kontainer
            'pranota-perbaikan-kontainer-view',
            'pranota-perbaikan-kontainer-create',
            'pranota-perbaikan-kontainer-update',
            'pranota-perbaikan-kontainer-delete',
            'pranota-perbaikan-kontainer-print',

            // Pranota - Surat Jalan
            'pranota-surat-jalan-view',
            'pranota-surat-jalan-create',
            'pranota-surat-jalan-update',
            'pranota-surat-jalan-delete',

            // Pranota - Uang Rit & Kenek
            'pranota-uang-rit-view',
            'pranota-uang-rit-create',
            'pranota-uang-rit-update',
            'pranota-uang-rit-delete',
            'pranota-uang-rit-approve',
            'pranota-uang-rit-mark-paid',
            'pranota-uang-kenek-view',
            'pranota-uang-kenek-create',
            'pranota-uang-kenek-update',
            'pranota-uang-kenek-delete',
            'pranota-uang-kenek-approve',
            'pranota-uang-kenek-mark-paid',

            // Tagihan - General
            'tagihan-kontainer-update',
            'tagihan-kontainer-delete',

            // Tagihan - Kontainer Sewa
            'tagihan-kontainer-sewa-index',
            'tagihan-kontainer-sewa-create',
            'tagihan-kontainer-sewa-update',
            'tagihan-kontainer-sewa-destroy',

            // Tagihan - Cat
            'tagihan-cat-view',
            'tagihan-cat-create',
            'tagihan-cat-update',
            'tagihan-cat-delete',

            // Tagihan - Perbaikan Kontainer
            'tagihan-perbaikan-kontainer-view',
            'tagihan-perbaikan-kontainer-create',
            'tagihan-perbaikan-kontainer-update',
            'tagihan-perbaikan-kontainer-delete',
            'tagihan-perbaikan-kontainer-print',

            // Perbaikan Kontainer
            'perbaikan-kontainer-view',
            'perbaikan-kontainer-update',
            'perbaikan-kontainer-delete',

            // Pembayaran - Pranota Supir
            'pembayaran-pranota-supir-view',
            'pembayaran-pranota-supir-create',
            'pembayaran-pranota-supir-update',
            'pembayaran-pranota-supir-delete',
            'pembayaran-pranota-supir-print',

            // Pembayaran - Pranota Kontainer
            'pembayaran-pranota-kontainer-view',
            'pembayaran-pranota-kontainer-create',
            'pembayaran-pranota-kontainer-update',
            'pembayaran-pranota-kontainer-delete',
            'pembayaran-pranota-kontainer-print',

            // Pembayaran - Pranota Cat
            'pembayaran-pranota-cat-view',

            // Pembayaran - Pranota Perbaikan Kontainer
            'pembayaran-pranota-perbaikan-kontainer-view',
            'pembayaran-pranota-perbaikan-kontainer-create',
            'pembayaran-pranota-perbaikan-kontainer-update',
            'pembayaran-pranota-perbaikan-kontainer-delete',
            'pembayaran-pranota-perbaikan-kontainer-print',

            // Pembayaran - Pranota Surat Jalan
            'pembayaran-pranota-surat-jalan-view',
            'pembayaran-pranota-surat-jalan-create',
            'pembayaran-pranota-surat-jalan-edit',
            'pembayaran-pranota-surat-jalan-delete',

            // Pembayaran - Aktivitas Lainnya
            'pembayaran-aktivitas-lainnya-view',
            'pembayaran-aktivitas-lainnya-create',
            'pembayaran-aktivitas-lainnya-update',
            'pembayaran-aktivitas-lainnya-delete',
            'pembayaran-aktivitas-lainnya-approve',
            'pembayaran-aktivitas-lainnya-print',
            'pembayaran-aktivitas-lainnya-export',

            // Pembayaran - Uang Muka & OB
            'pembayaran-uang-muka-view',
            'pembayaran-uang-muka-create',
            'pembayaran-uang-muka-edit',
            'pembayaran-uang-muka-delete',
            'pembayaran-ob-view',
            'pembayaran-ob-create',
            'pembayaran-ob-edit',
            'pembayaran-ob-delete',

            // Realisasi Uang Muka
            'realisasi-uang-muka-view',
            'realisasi-uang-muka-create',
            'realisasi-uang-muka-edit',
            'realisasi-uang-muka-delete',

            // Aktivitas Lainnya
            'aktivitas-lainnya-view',
            'aktivitas-lainnya-create',
            'aktivitas-lainnya-update',
            'aktivitas-lainnya-delete',
            'aktivitas-lainnya-approve',

            // Vendor Kontainer Sewa
            'vendor-kontainer-sewa-view',
            'vendor-kontainer-sewa-create',
            'vendor-kontainer-sewa-edit',
            'vendor-kontainer-sewa-delete',

            // Profile Management
            'profile-update',
            'profile-delete',
        ];

        // Insert permissions into database
        $timestamp = now();
        $permissionData = [];

        foreach ($permissions as $permission) {
            $permissionData[] = [
                'name' => $permission,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($permissionData, 50);
        foreach ($chunks as $chunk) {
            DB::table('permissions')->insertOrIgnore($chunk);
        }

        echo "Total permissions created: " . count($permissions) . "\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'approval-dashboard', 'approval-tugas-1', 'surat-jalan-approval-dashboard',
            'master-user-view', 'master-user-create', 'master-user-update', 'master-user-delete', 'master-user-bulk-manage',
            'master-karyawan', 'master-karyawan-view', 'master-karyawan-create', 'master-karyawan-update', 'master-karyawan-delete', 'master-karyawan-print', 'master-karyawan-export',
            'master-divisi-view', 'master-divisi-create', 'master-divisi-update', 'master-divisi-delete',
            'master-cabang-view', 'master-pekerjaan-view', 'master-pekerjaan-create', 'master-pekerjaan-update', 'master-pekerjaan-destroy',
            'master-kontainer-view', 'master-kontainer-create', 'master-kontainer-update', 'master-kontainer-delete',
            'master-stock-kontainer-view', 'master-stock-kontainer-create', 'master-stock-kontainer-update', 'master-stock-kontainer-delete',
            'master-bank-view', 'master-bank-create', 'master-bank-update', 'master-bank-destroy',
            'master-pajak-view', 'master-pajak-create', 'master-pajak-update', 'master-pajak-destroy',
            'master-coa-view', 'master-coa-create', 'master-tipe-akun-view', 'master-kode-nomor-view', 'master-nomor-terakhir-view',
            'master-kegiatan-view', 'master-kegiatan-create', 'master-kegiatan-update', 'master-kegiatan-delete',
            'master-tujuan-view', 'master-tujuan-create', 'master-tujuan-update', 'master-tujuan-delete', 'master-tujuan-print', 'master-tujuan-export',
            'master-tujuan-kirim-view', 'master-tujuan-kirim-create', 'master-tujuan-kirim-update', 'master-tujuan-kirim-delete',
            'master-mobil-view', 'master-mobil-create', 'master-mobil-update', 'master-mobil-delete',
            'master-kapal', 'master-pelabuhan-view', 'master-pelabuhan-create', 'master-pelabuhan-edit', 'master-pelabuhan-delete',
            'master-pengirim-view', 'master-pengirim-create', 'master-pengirim-update', 'master-pengirim-delete',
            'master-vendor-bengkel-view', 'master-vendor-bengkel-create', 'master-vendor-bengkel-update', 'master-vendor-bengkel-delete',
            'master-jenis-barang-view', 'master-jenis-barang-create', 'master-jenis-barang-update', 'master-jenis-barang-delete',
            'master-term-view', 'master-term-create', 'master-term-update', 'master-term-delete',
            'master-pricelist-sewa-kontainer-view', 'master-pricelist-sewa-kontainer-create', 'master-pricelist-sewa-kontainer-update', 'master-pricelist-sewa-kontainer-delete',
            'master-pricelist-cat-view', 'master-pricelist-cat-create', 'master-pricelist-cat-update', 'master-pricelist-cat-delete',
            'master-pricelist-gate-in-view', 'master-pricelist-gate-in-create', 'master-pricelist-gate-in-update', 'master-pricelist-gate-in-delete',
            'master-permission-view', 'master-permission-create', 'master-permission-update', 'master-permission-delete',
            'permohonan', 'permohonan-memo-view', 'permohonan-memo-create', 'permohonan-memo-update', 'permohonan-memo-delete', 'permohonan-memo-print',
            'order-view', 'order-create', 'order-update', 'order-delete',
            'surat-jalan-view', 'surat-jalan-create', 'surat-jalan-update', 'surat-jalan-delete',
            'gate-in-view', 'gate-in-create', 'gate-in-update', 'gate-in-delete',
            'tanda-terima-view', 'tanda-terima-edit', 'tanda-terima-delete',
            'tanda-terima-tanpa-surat-jalan-view', 'tanda-terima-tanpa-surat-jalan-create', 'tanda-terima-tanpa-surat-jalan-update', 'tanda-terima-tanpa-surat-jalan-delete',
            'bl-view', 'bl-create', 'bl-edit', 'prospek-view', 'prospek-edit',
            'pergerakan-kapal-view', 'pergerakan-kapal-create', 'pergerakan-kapal-update', 'pergerakan-kapal-delete',
            'pranota-view', 'pranota-create', 'pranota-update', 'pranota-delete', 'pranota-print',
            'pranota-supir-view', 'pranota-supir-create', 'pranota-supir-update', 'pranota-supir-delete', 'pranota-supir-print',
            'pranota-kontainer-sewa-view', 'pranota-kontainer-sewa-create', 'pranota-kontainer-sewa-update', 'pranota-kontainer-sewa-edit', 'pranota-kontainer-sewa-delete', 'pranota-kontainer-sewa-print',
            'pranota-cat-view', 'pranota-cat-create', 'pranota-cat-update', 'pranota-cat-delete', 'pranota-cat-print',
            'pranota-perbaikan-kontainer-view', 'pranota-perbaikan-kontainer-create', 'pranota-perbaikan-kontainer-update', 'pranota-perbaikan-kontainer-delete', 'pranota-perbaikan-kontainer-print',
            'pranota-surat-jalan-view', 'pranota-surat-jalan-create', 'pranota-surat-jalan-update', 'pranota-surat-jalan-delete',
            'pranota-uang-rit-view', 'pranota-uang-rit-create', 'pranota-uang-rit-update', 'pranota-uang-rit-delete', 'pranota-uang-rit-approve', 'pranota-uang-rit-mark-paid',
            'pranota-uang-kenek-view', 'pranota-uang-kenek-create', 'pranota-uang-kenek-update', 'pranota-uang-kenek-delete', 'pranota-uang-kenek-approve', 'pranota-uang-kenek-mark-paid',
            'tagihan-kontainer-update', 'tagihan-kontainer-delete',
            'tagihan-kontainer-sewa-index', 'tagihan-kontainer-sewa-create', 'tagihan-kontainer-sewa-update', 'tagihan-kontainer-sewa-destroy',
            'tagihan-cat-view', 'tagihan-cat-create', 'tagihan-cat-update', 'tagihan-cat-delete',
            'tagihan-perbaikan-kontainer-view', 'tagihan-perbaikan-kontainer-create', 'tagihan-perbaikan-kontainer-update', 'tagihan-perbaikan-kontainer-delete', 'tagihan-perbaikan-kontainer-print',
            'perbaikan-kontainer-view', 'perbaikan-kontainer-update', 'perbaikan-kontainer-delete',
            'pembayaran-pranota-supir-view', 'pembayaran-pranota-supir-create', 'pembayaran-pranota-supir-update', 'pembayaran-pranota-supir-delete', 'pembayaran-pranota-supir-print',
            'pembayaran-pranota-kontainer-view', 'pembayaran-pranota-kontainer-create', 'pembayaran-pranota-kontainer-update', 'pembayaran-pranota-kontainer-delete', 'pembayaran-pranota-kontainer-print',
            'pembayaran-pranota-cat-view',
            'pembayaran-pranota-perbaikan-kontainer-view', 'pembayaran-pranota-perbaikan-kontainer-create', 'pembayaran-pranota-perbaikan-kontainer-update', 'pembayaran-pranota-perbaikan-kontainer-delete', 'pembayaran-pranota-perbaikan-kontainer-print',
            'pembayaran-pranota-surat-jalan-view', 'pembayaran-pranota-surat-jalan-create', 'pembayaran-pranota-surat-jalan-edit', 'pembayaran-pranota-surat-jalan-delete',
            'pembayaran-aktivitas-lainnya-view', 'pembayaran-aktivitas-lainnya-create', 'pembayaran-aktivitas-lainnya-update', 'pembayaran-aktivitas-lainnya-delete', 'pembayaran-aktivitas-lainnya-approve', 'pembayaran-aktivitas-lainnya-print', 'pembayaran-aktivitas-lainnya-export',
            'pembayaran-uang-muka-view', 'pembayaran-uang-muka-create', 'pembayaran-uang-muka-edit', 'pembayaran-uang-muka-delete',
            'pembayaran-ob-view', 'pembayaran-ob-create', 'pembayaran-ob-edit', 'pembayaran-ob-delete',
            'realisasi-uang-muka-view', 'realisasi-uang-muka-create', 'realisasi-uang-muka-edit', 'realisasi-uang-muka-delete',
            'aktivitas-lainnya-view', 'aktivitas-lainnya-create', 'aktivitas-lainnya-update', 'aktivitas-lainnya-delete', 'aktivitas-lainnya-approve',
            'vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-delete',
            'profile-update', 'profile-delete'
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};