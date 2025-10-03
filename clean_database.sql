-- ===================================================================
-- SCRIPT SQL UNTUK MEMBERSIHKAN DATABASE DAFTAR TAGIHAN KONTAINER SEWA
-- ===================================================================
--
-- PERINGATAN: Script ini akan menghapus SEMUA data dari tabel!
-- Pastikan Anda sudah backup database sebelum menjalankan script ini!
--
-- Cara menjalankan:
-- 1. Masuk ke MySQL/phpMyAdmin
-- 2. Pilih database aypsis
-- 3. Copy-paste script ini ke SQL query
-- 4. Jalankan query
-- ===================================================================

-- Tampilkan jumlah data sebelum dihapus
SELECT
    COUNT(*) as 'Jumlah Data Sebelum Dihapus',
    'daftar_tagihan_kontainer_sewa' as 'Tabel'
FROM daftar_tagihan_kontainer_sewa;

-- Nonaktifkan foreign key check sementara
SET FOREIGN_KEY_CHECKS = 0;

-- Hapus semua data dari tabel
DELETE FROM daftar_tagihan_kontainer_sewa;

-- Reset auto increment ID ke 1
ALTER TABLE daftar_tagihan_kontainer_sewa AUTO_INCREMENT = 1;

-- Aktifkan kembali foreign key check
SET FOREIGN_KEY_CHECKS = 1;

-- Verifikasi pembersihan
SELECT
    COUNT(*) as 'Jumlah Data Setelah Dihapus',
    'daftar_tagihan_kontainer_sewa' as 'Tabel'
FROM daftar_tagihan_kontainer_sewa;

-- Tampilkan status auto increment
SELECT
    AUTO_INCREMENT as 'Next Auto Increment Value',
    'daftar_tagihan_kontainer_sewa' as 'Tabel'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'daftar_tagihan_kontainer_sewa';

SELECT '✅ PEMBERSIHAN DATABASE SELESAI!' as 'Status';
