-- ============================================
-- FIX MIGRATION ERROR: Table Already Exists
-- ============================================
-- File ini untuk menandai migration sebagai sudah dijalankan
-- tanpa benar-benar menjalankan CREATE TABLE yang error

-- 1. CEK BATCH NUMBER TERAKHIR
SELECT MAX(batch) as last_batch FROM migrations;

-- 2. CEK MIGRATION APA SAJA YANG SUDAH DIJALANKAN
SELECT migration, batch FROM migrations 
WHERE migration LIKE '%tanda_terima%lcl%' 
ORDER BY batch DESC, migration;

-- 3. INSERT MIGRATION YANG BELUM DIJALANKAN KE TABEL MIGRATIONS
-- Ganti angka 26 dengan (last_batch + 1) dari query #1

SET @next_batch = (SELECT MAX(batch) + 1 FROM migrations);

INSERT INTO migrations (migration, batch) VALUES 
('2025_10_25_124630_create_tanda_terima_lcl_table', @next_batch),
('2025_10_25_124657_create_tanda_terima_lcl_items_table', @next_batch),
('2025_10_27_103613_fix_tanda_terima_lcl_tujuan_pengiriman_foreign_key', @next_batch),
('2025_10_27_130917_add_nomor_kontainer_to_tanda_terima_lcl_table', @next_batch),
('2025_10_27_131639_add_size_kontainer_to_tanda_terima_lcl_table', @next_batch),
('2025_10_27_131957_add_seal_columns_to_tanda_terima_lcl_table', @next_batch),
('2025_10_27_150123_fix_size_kontainer_enum_values_in_tanda_terima_lcl_table', @next_batch),
('2025_12_01_153953_drop_jenis_barang_id_from_tanda_terima_lcl_table', @next_batch),
('2025_12_01_155316_add_item_details_to_tanda_terima_lcl_items_table', @next_batch),
('2025_12_04_143703_make_nomor_tanda_terima_nullable_in_tanda_terima_lcl_table', @next_batch),
('2025_12_04_160829_add_gambar_surat_jalan_to_tanda_terima_lcl_table', @next_batch),
('2025_12_04_161933_add_jenis_kontainer_to_tanda_terima_lcl_table', @next_batch),
('2025_12_15_090852_create_tanda_terima_lcl_tables', @next_batch),
('2025_12_15_091741_remove_unused_columns_from_tanda_terimas_lcl_table', @next_batch),
('2025_12_15_092519_add_item_number_and_fix_columns_to_tanda_terima_lcl_items_table', @next_batch),
('2025_12_15_144001_drop_unused_pivot_tables_tanda_terima_lcl', @next_batch),
('2025_12_15_144324_add_container_fields_to_tanda_terimas_lcl_table', @next_batch),
('2025_12_15_144932_create_tanda_terima_lcl_kontainer_pivot_table', @next_batch),
('2025_12_15_160000_add_single_penerima_pengirim_to_tanda_terimas_lcl_table', @next_batch),
('2025_12_15_114000_create_pembayaran_pranota_obs_table', @next_batch),
('2025_12_15_120000_add_status_to_pranota_obs_table', @next_batch)
ON DUPLICATE KEY UPDATE migration = migration;

-- 4. VERIFIKASI MIGRATION SUDAH MASUK
SELECT * FROM migrations WHERE batch = @next_batch ORDER BY migration;

-- 5. CEK STRUKTUR TABEL tanda_terimas_lcl
DESCRIBE tanda_terimas_lcl;

-- 6. PASTIKAN KOLOM-KOLOM INI ADA (jika belum ada, tambahkan manual):

-- Tambah kolom penerima jika belum ada
ALTER TABLE tanda_terimas_lcl ADD COLUMN IF NOT EXISTS nama_penerima VARCHAR(255) NULL AFTER term_id;
ALTER TABLE tanda_terimas_lcl ADD COLUMN IF NOT EXISTS pic_penerima VARCHAR(255) NULL AFTER nama_penerima;
ALTER TABLE tanda_terimas_lcl ADD COLUMN IF NOT EXISTS telepon_penerima VARCHAR(255) NULL AFTER pic_penerima;
ALTER TABLE tanda_terimas_lcl ADD COLUMN IF NOT EXISTS alamat_penerima TEXT NULL AFTER telepon_penerima;

-- Tambah kolom pengirim jika belum ada
ALTER TABLE tanda_terimas_lcl ADD COLUMN IF NOT EXISTS nama_pengirim VARCHAR(255) NULL AFTER alamat_penerima;
ALTER TABLE tanda_terimas_lcl ADD COLUMN IF NOT EXISTS pic_pengirim VARCHAR(255) NULL AFTER nama_pengirim;
ALTER TABLE tanda_terimas_lcl ADD COLUMN IF NOT EXISTS telepon_pengirim VARCHAR(255) NULL AFTER pic_pengirim;
ALTER TABLE tanda_terimas_lcl ADD COLUMN IF NOT EXISTS alamat_pengirim TEXT NULL AFTER telepon_pengirim;

-- Perbaiki nama kolom tujuan_pengiriman jadi tujuan_pengiriman_id
-- (Cek dulu apakah kolom tujuan_pengiriman masih ada)
-- ALTER TABLE tanda_terimas_lcl CHANGE COLUMN tujuan_pengiriman tujuan_pengiriman_id BIGINT UNSIGNED NULL;

-- 7. CEK/BUAT TABEL PIVOT KONTAINER LCL
CREATE TABLE IF NOT EXISTS `tanda_terima_lcl_kontainer_pivot` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanda_terima_lcl_id` bigint unsigned NOT NULL,
  `nomor_kontainer` varchar(255) NOT NULL,
  `size_kontainer` varchar(50) DEFAULT NULL,
  `tipe_kontainer` varchar(50) DEFAULT NULL,
  `nomor_seal` varchar(255) DEFAULT NULL,
  `tanggal_seal` date DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tanda_terima_lcl_kontainer_pivot_tanda_terima_lcl_id_foreign` (`tanda_terima_lcl_id`),
  KEY `tanda_terima_lcl_kontainer_pivot_assigned_by_foreign` (`assigned_by`),
  KEY `idx_nomor_kontainer` (`nomor_kontainer`),
  KEY `idx_kontainer_tanggal` (`nomor_kontainer`,`assigned_at`),
  CONSTRAINT `tanda_terima_lcl_kontainer_pivot_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tanda_terima_lcl_kontainer_pivot_tanda_terima_lcl_id_foreign` FOREIGN KEY (`tanda_terima_lcl_id`) REFERENCES `tanda_terimas_lcl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. VERIFIKASI SEMUA TABEL TANDA TERIMA LCL
SHOW TABLES LIKE '%tanda_terima%lcl%';

-- Expected results:
-- tanda_terimas_lcl
-- tanda_terima_lcl_items
-- tanda_terima_lcl_kontainer_pivot

-- 9. SETELAH SEMUA SELESAI, COBA MIGRATE LAGI DI SERVER
-- php artisan migrate

-- 10. CEK LOG JIKA ADA ERROR
-- php artisan migrate --verbose
