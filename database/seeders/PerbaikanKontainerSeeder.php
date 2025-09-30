<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PerbaikanKontainer;

class PerbaikanKontainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $adminUserId = DB::table('users')->where('username', 'admin')->value('id') ?? 1;

        // Get existing kontainers and vendors
        $kontainers = DB::table('kontainers')->pluck('nomor_seri_gabungan', 'id')->toArray();
        $vendors = DB::table('vendor_bengkel')->pluck('id', 'nama_bengkel')->toArray();

        // Ensure we have vendors by running VendorBengkelSeeder if needed
        if (empty($vendors)) {
            $this->call(VendorBengkelSeeder::class);
            $vendors = DB::table('vendor_bengkel')->pluck('id', 'nama_bengkel')->toArray();
        }

        // Ensure we have kontainers by running KontainerSeeder if needed
        if (empty($kontainers)) {
            $this->call(KontainerSeeder::class);
            $kontainers = DB::table('kontainers')->pluck('nomor_seri_gabungan', 'id')->toArray();
        }

        $perbaikanData = [
            [
                'nomor_tagihan' => PerbaikanKontainer::generateNomorTagihan(),
                'nomor_kontainer' => 'ABCD1234567',
                'tanggal_perbaikan' => '2025-09-01',
                'estimasi_kerusakan_kontainer' => 'maintenance',
                'deskripsi_perbaikan' => 'Pemeriksaan rutin dan perawatan kontainer',
                'realisasi_kerusakan' => 'Maintenance rutin berhasil dilakukan',
                'estimasi_biaya_perbaikan' => 500000.00,
                'realisasi_biaya_perbaikan' => 450000.00,
                'vendor_bengkel_id' => $vendors['AYP Cat Service'] ?? array_values($vendors)[0],
                'vendor_bengkel' => 'AYP Cat Service',
                'status_perbaikan' => 'sudah_dibayar',
                'catatan' => 'Perbaikan selesai dengan baik',
                'jenis_catatan' => 'maintenance',
                'teknisi' => 'Ahmad',
                'prioritas' => 'normal',
                'sparepart_dibutuhkan' => 'Tidak ada sparepart tambahan',
                'tanggal_catatan' => '2025-09-01',
                'tanggal_cat' => '2025-09-02',
                'estimasi_waktu' => '2 hari',
                'tanggal_selesai' => '2025-09-03',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nomor_tagihan' => PerbaikanKontainer::generateNomorTagihan(),
                'nomor_kontainer' => 'EFGH6543213',
                'tanggal_perbaikan' => '2025-09-05',
                'estimasi_kerusakan_kontainer' => 'repair',
                'deskripsi_perbaikan' => 'Perbaikan kerusakan pada pintu kontainer',
                'realisasi_kerusakan' => 'Pintu kontainer berhasil diperbaiki',
                'estimasi_biaya_perbaikan' => 1200000.00,
                'realisasi_biaya_perbaikan' => 1100000.00,
                'vendor_bengkel_id' => $vendors['PT. Container Repair Indonesia'] ?? array_values($vendors)[0],
                'vendor_bengkel' => 'PT. Container Repair Indonesia',
                'status_perbaikan' => 'sudah_masuk_pranota',
                'catatan' => 'Menunggu pembayaran dari pranota',
                'jenis_catatan' => 'repair',
                'teknisi' => 'Budi',
                'prioritas' => 'high',
                'sparepart_dibutuhkan' => 'Engsel pintu baru',
                'tanggal_catatan' => '2025-09-05',
                'tanggal_cat' => null,
                'estimasi_waktu' => '3 hari',
                'tanggal_selesai' => '2025-09-08',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nomor_tagihan' => PerbaikanKontainer::generateNomorTagihan(),
                'nomor_kontainer' => 'ABCD1234567',
                'tanggal_perbaikan' => '2025-09-10',
                'estimasi_kerusakan_kontainer' => 'inspection',
                'deskripsi_perbaikan' => 'Inspeksi kondisi kontainer sebelum pengiriman',
                'realisasi_kerusakan' => 'Inspeksi selesai, kondisi baik',
                'estimasi_biaya_perbaikan' => 200000.00,
                'realisasi_biaya_perbaikan' => 180000.00,
                'vendor_bengkel_id' => $vendors['Zona Container Painting'] ?? array_values($vendors)[0],
                'vendor_bengkel' => 'Zona Container Painting',
                'status_perbaikan' => 'belum_masuk_pranota',
                'catatan' => 'Perlu pengecatan ulang',
                'jenis_catatan' => 'inspection',
                'teknisi' => 'Citra',
                'prioritas' => 'normal',
                'sparepart_dibutuhkan' => 'Cat dan kuas',
                'tanggal_catatan' => '2025-09-10',
                'tanggal_cat' => '2025-09-12',
                'estimasi_waktu' => '1 hari',
                'tanggal_selesai' => '2025-09-11',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nomor_tagihan' => PerbaikanKontainer::generateNomorTagihan(),
                'nomor_kontainer' => 'EFGH6543213',
                'tanggal_perbaikan' => '2025-09-15',
                'estimasi_kerusakan_kontainer' => 'replacement',
                'deskripsi_perbaikan' => 'Penggantian floor kontainer yang rusak',
                'realisasi_kerusakan' => 'Floor berhasil diganti',
                'estimasi_biaya_perbaikan' => 2500000.00,
                'realisasi_biaya_perbaikan' => 2400000.00,
                'vendor_bengkel_id' => $vendors['Bengkel Kontainer Maju Jaya'] ?? array_values($vendors)[0],
                'vendor_bengkel' => 'Bengkel Kontainer Maju Jaya',
                'status_perbaikan' => 'sudah_dibayar',
                'catatan' => 'Penggantian floor selesai dengan baik',
                'jenis_catatan' => 'replacement',
                'teknisi' => 'Dedi',
                'prioritas' => 'urgent',
                'sparepart_dibutuhkan' => 'Floor panel baru',
                'tanggal_catatan' => '2025-09-15',
                'tanggal_cat' => null,
                'estimasi_waktu' => '5 hari',
                'tanggal_selesai' => '2025-09-20',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nomor_tagihan' => PerbaikanKontainer::generateNomorTagihan(),
                'nomor_kontainer' => 'ABCD1234567',
                'tanggal_perbaikan' => '2025-09-20',
                'estimasi_kerusakan_kontainer' => 'cleaning',
                'deskripsi_perbaikan' => 'Pembersihan kontainer dari kotoran dan karat',
                'realisasi_kerusakan' => 'Pembersihan selesai, kontainer bersih',
                'estimasi_biaya_perbaikan' => 300000.00,
                'realisasi_biaya_perbaikan' => 280000.00,
                'vendor_bengkel_id' => $vendors['CV. Container Maintenance Pro'] ?? array_values($vendors)[0],
                'vendor_bengkel' => 'CV. Container Maintenance Pro',
                'status_perbaikan' => 'belum_masuk_pranota',
                'catatan' => 'Perlu dilakukan pengecekan lanjutan',
                'jenis_catatan' => 'cleaning',
                'teknisi' => 'Eka',
                'prioritas' => 'low',
                'sparepart_dibutuhkan' => 'Bahan pembersih khusus',
                'tanggal_catatan' => '2025-09-20',
                'tanggal_cat' => '2025-09-22',
                'estimasi_waktu' => '2 hari',
                'tanggal_selesai' => '2025-09-22',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nomor_tagihan' => PerbaikanKontainer::generateNomorTagihan(),
                'nomor_kontainer' => 'EFGH6543213',
                'tanggal_perbaikan' => '2025-09-25',
                'estimasi_kerusakan_kontainer' => 'other',
                'deskripsi_perbaikan' => 'Perbaikan sistem pendingin reefer kontainer',
                'realisasi_kerusakan' => 'Sistem pendingin berhasil diperbaiki',
                'estimasi_biaya_perbaikan' => 1800000.00,
                'realisasi_biaya_perbaikan' => 1750000.00,
                'vendor_bengkel_id' => $vendors['PT. Container Repair Indonesia'] ?? array_values($vendors)[0],
                'vendor_bengkel' => 'PT. Container Repair Indonesia',
                'status_perbaikan' => 'sudah_masuk_pranota',
                'catatan' => 'Perbaikan sistem pendingin reefer unit',
                'jenis_catatan' => 'repair',
                'teknisi' => 'Fajar',
                'prioritas' => 'high',
                'sparepart_dibutuhkan' => 'Kompresor pendingin baru',
                'tanggal_catatan' => '2025-09-25',
                'tanggal_cat' => null,
                'estimasi_waktu' => '4 hari',
                'tanggal_selesai' => '2025-09-29',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nomor_tagihan' => PerbaikanKontainer::generateNomorTagihan(),
                'nomor_kontainer' => 'ABCD1234567',
                'tanggal_perbaikan' => '2025-08-15',
                'estimasi_kerusakan_kontainer' => 'maintenance',
                'deskripsi_perbaikan' => 'Maintenance bulanan kontainer',
                'realisasi_kerusakan' => 'Maintenance rutin selesai',
                'estimasi_biaya_perbaikan' => 400000.00,
                'realisasi_biaya_perbaikan' => 380000.00,
                'vendor_bengkel_id' => $vendors['AYP Cat Service'] ?? array_values($vendors)[0],
                'vendor_bengkel' => 'AYP Cat Service',
                'status_perbaikan' => 'sudah_dibayar',
                'catatan' => 'Maintenance bulanan sesuai jadwal',
                'jenis_catatan' => 'maintenance',
                'teknisi' => 'Gilang',
                'prioritas' => 'normal',
                'sparepart_dibutuhkan' => 'Oli dan filter',
                'tanggal_catatan' => '2025-08-15',
                'tanggal_cat' => null,
                'estimasi_waktu' => '1 hari',
                'tanggal_selesai' => '2025-08-16',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nomor_tagihan' => PerbaikanKontainer::generateNomorTagihan(),
                'nomor_kontainer' => 'EFGH6543213',
                'tanggal_perbaikan' => '2025-08-20',
                'estimasi_kerusakan_kontainer' => 'repair',
                'deskripsi_perbaikan' => 'Perbaikan seal kontainer yang bocor',
                'realisasi_kerusakan' => 'Seal berhasil diperbaiki',
                'estimasi_biaya_perbaikan' => 800000.00,
                'realisasi_biaya_perbaikan' => 750000.00,
                'vendor_bengkel_id' => $vendors['Zona Container Painting'] ?? array_values($vendors)[0],
                'vendor_bengkel' => 'Zona Container Painting',
                'status_perbaikan' => 'sudah_dibayar',
                'catatan' => 'Perbaikan seal pintu kontainer',
                'jenis_catatan' => 'repair',
                'teknisi' => 'Hendra',
                'prioritas' => 'normal',
                'sparepart_dibutuhkan' => 'Seal rubber baru',
                'tanggal_catatan' => '2025-08-20',
                'tanggal_cat' => null,
                'estimasi_waktu' => '2 hari',
                'tanggal_selesai' => '2025-08-22',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($perbaikanData as $perbaikan) {
            DB::table('perbaikan_kontainers')->updateOrInsert(
                ['nomor_tagihan' => $perbaikan['nomor_tagihan']],
                $perbaikan
            );
        }
    }
}
