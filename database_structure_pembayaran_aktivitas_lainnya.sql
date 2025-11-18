-- ============================================================
-- DROP DAN RECREATE TABEL PEMBAYARAN AKTIVITAS LAINNYA
-- Tanggal: 2024-11-17
-- Deskripsi: Struktur database baru yang sesuai dengan form pembayaran aktivitas lainnya
-- ============================================================

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS=0;

-- Drop tabel lama
DROP TABLE IF EXISTS `pembayaran_uang_muka_supir_details`;
DROP TABLE IF EXISTS `pembayaran_aktivitas_lainnya_supir`;
DROP TABLE IF EXISTS `pembayaran_aktivitas_lainnya_items`;
DROP TABLE IF EXISTS `pembayaran_aktivitas_lainnya`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- ============================================================
-- CREATE TABLE: pembayaran_aktivitas_lainnya
-- ============================================================
CREATE TABLE `pembayaran_aktivitas_lainnya` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  
  -- Informasi Pembayaran Dasar
  `nomor_pembayaran` varchar(50) NOT NULL COMMENT 'Format: PMS1116000001',
  `nomor_accurate` varchar(100) DEFAULT NULL COMMENT 'Nomor referensi dari sistem Accurate',
  `tanggal_pembayaran` date NOT NULL,
  
  -- Informasi Voyage
  `nomor_voyage` varchar(100) DEFAULT NULL COMMENT 'Nomor voyage dari tabel naik_kapal atau bls',
  `nama_kapal` varchar(200) DEFAULT NULL COMMENT 'Nama kapal terkait voyage',
  
  -- Informasi Pembayaran
  `total_pembayaran` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total pembayaran/uang muka',
  `aktivitas_pembayaran` text NOT NULL COMMENT 'Deskripsi aktivitas pembayaran (wajib diisi)',
  
  -- Plat Nomor (untuk KIR & STNK)
  `plat_nomor` varchar(50) DEFAULT NULL COMMENT 'Plat nomor untuk kegiatan KIR & STNK',
  
  -- Akun Bank dan Biaya
  `pilih_bank` bigint unsigned DEFAULT NULL COMMENT 'ID dari master_coa (kategori Bank/Kas)',
  `akun_biaya_id` bigint unsigned DEFAULT NULL COMMENT 'ID dari master_coa (tipe BIAYA)',
  
  -- Jenis Transaksi
  `jenis_transaksi` enum('debit','kredit') NOT NULL DEFAULT 'kredit' COMMENT 'Jenis transaksi: debit (pemasukan) atau kredit (pengeluaran)',
  
  -- Jenis Pembayaran
  `is_dp` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Apakah ini pembayaran DP/uang muka',
  
  -- Status dan Approval
  `status` enum('draft','pending','approved','rejected','paid') NOT NULL DEFAULT 'draft',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembayaran_aktivitas_lainnya_nomor_pembayaran_unique` (`nomor_pembayaran`),
  KEY `pembayaran_aktivitas_lainnya_tanggal_pembayaran_index` (`tanggal_pembayaran`),
  KEY `pembayaran_aktivitas_lainnya_status_index` (`status`),
  KEY `pembayaran_aktivitas_lainnya_status_tanggal_pembayaran_index` (`status`,`tanggal_pembayaran`),
  KEY `pembayaran_aktivitas_lainnya_nomor_voyage_index` (`nomor_voyage`),
  KEY `pembayaran_aktivitas_lainnya_created_by_index` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CREATE TABLE: pembayaran_aktivitas_lainnya_supir
-- ============================================================
CREATE TABLE `pembayaran_aktivitas_lainnya_supir` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pembayaran_id` bigint unsigned NOT NULL COMMENT 'FK ke pembayaran_aktivitas_lainnya',
  `supir_id` bigint unsigned NOT NULL COMMENT 'FK ke master_supir',
  `jumlah_uang_muka` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Jumlah uang muka untuk supir ini',
  `keterangan` text COMMENT 'Keterangan tambahan untuk uang muka supir',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `pembayaran_aktivitas_lainnya_supir_pembayaran_id_index` (`pembayaran_id`),
  KEY `pembayaran_aktivitas_lainnya_supir_supir_id_index` (`supir_id`),
  CONSTRAINT `pembayaran_aktivitas_lainnya_supir_pembayaran_id_foreign` 
    FOREIGN KEY (`pembayaran_id`) 
    REFERENCES `pembayaran_aktivitas_lainnya` (`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- STRUKTUR FIELD FORM
-- ============================================================
-- 1. Nomor Pembayaran (nomor_pembayaran) - Auto generate format PMS1116000001
-- 2. Nomor Accurate (nomor_accurate) - Optional
-- 3. Tanggal Pembayaran (tanggal_pembayaran) - Required
-- 4. Nomor Voyage (nomor_voyage) - Required, dari tabel naik_kapal & bls
-- 5. Nama Kapal (nama_kapal) - Optional, auto-fill dari voyage
-- 6. Tabel Daftar Supir:
--    - supir_id (dari master_supir)
--    - jumlah_uang_muka
--    - keterangan (optional)
-- 7. Total Pembayaran (total_pembayaran) - Auto sum dari uang muka supir
-- 8. Aktivitas Pembayaran (aktivitas_pembayaran) - Required, min 5 karakter
-- 9. Plat Nomor (plat_nomor) - Optional, untuk KIR & STNK
-- 10. Pilih Bank (pilih_bank) - Required, dari master_coa kategori Bank/Kas
-- 11. Akun Biaya (akun_biaya_id) - Required, dari master_coa tipe BIAYA
-- 12. Jenis Transaksi (jenis_transaksi) - Required, enum: debit/kredit
-- 13. Jenis Pembayaran DP (is_dp) - Checkbox, boolean

-- ============================================================
-- NOTES
-- ============================================================
-- * Tabel pembayaran_aktivitas_lainnya_items dihapus karena tidak digunakan
-- * Tabel pembayaran_uang_muka_supir_details dihapus, diganti dengan pembayaran_aktivitas_lainnya_supir
-- * Field yang dihapus: metode_pembayaran, referensi_pembayaran, kegiatan
-- * Field yang ditambah: is_dp, aktivitas_pembayaran
-- * Field yang diubah: total_nominal -> total_pembayaran
-- * Relasi: pembayaran_aktivitas_lainnya hasMany pembayaran_aktivitas_lainnya_supir
