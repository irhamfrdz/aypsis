-- Script untuk mengembalikan status surat jalan agar muncul kembali di halaman create pranota
-- Gunakan script ini jika Anda tidak sengaja menghapus pranota

-- OPSI 1: Reset SEMUA surat jalan yang sudah masuk pranota kembali ke 'belum_dibayar'
-- HATI-HATI: Ini akan mereset SEMUA surat jalan, termasuk yang pranotanya masih ada
UPDATE surat_jalans 
SET status_pembayaran_uang_rit = 'belum_dibayar'
WHERE status_pembayaran_uang_rit = 'sudah_masuk_pranota';

-- OPSI 2: Reset hanya surat jalan yang pranotanya sudah dihapus atau tidak ada
-- Lebih aman karena hanya mereset surat jalan yang pranota-nya tidak ada
UPDATE surat_jalans 
SET status_pembayaran_uang_rit = 'belum_dibayar'
WHERE status_pembayaran_uang_rit = 'sudah_masuk_pranota'
AND id NOT IN (
    SELECT DISTINCT surat_jalan_id 
    FROM pranota_uang_rits 
    WHERE surat_jalan_id IS NOT NULL
    AND status != 'cancelled'
);

-- OPSI 3: Reset berdasarkan nomor pranota tertentu yang sudah dihapus
-- Ganti 'PUR-01-26-000001' dengan nomor pranota yang Anda hapus
UPDATE surat_jalans 
SET status_pembayaran_uang_rit = 'belum_dibayar'
WHERE id IN (
    -- Jika pranota masih ada di database tapi status cancelled
    SELECT DISTINCT surat_jalan_id 
    FROM pranota_uang_rits 
    WHERE no_pranota = 'PUR-01-26-000001'
    AND surat_jalan_id IS NOT NULL
)
-- Atau jika ingin mereset berdasarkan tanggal tertentu
-- WHERE DATE(created_at) = '2026-01-10';

-- UNTUK SURAT JALAN BONGKARAN:
-- Opsi 1: Reset semua surat jalan bongkaran
UPDATE surat_jalan_bongkarans 
SET status_pembayaran_uang_rit = 'belum_dibayar'
WHERE status_pembayaran_uang_rit = 'lunas';

-- Opsi 2: Reset hanya yang pranotanya sudah dihapus
UPDATE surat_jalan_bongkarans 
SET status_pembayaran_uang_rit = 'belum_dibayar'
WHERE status_pembayaran_uang_rit = 'lunas'
AND id NOT IN (
    SELECT DISTINCT surat_jalan_bongkaran_id 
    FROM pranota_uang_rits 
    WHERE surat_jalan_bongkaran_id IS NOT NULL
    AND status != 'cancelled'
);

-- VERIFIKASI: Cek berapa surat jalan yang akan diubah (jalankan ini SEBELUM update)
SELECT COUNT(*) as total_akan_direset
FROM surat_jalans 
WHERE status_pembayaran_uang_rit = 'sudah_masuk_pranota'
AND id NOT IN (
    SELECT DISTINCT surat_jalan_id 
    FROM pranota_uang_rits 
    WHERE surat_jalan_id IS NOT NULL
    AND status != 'cancelled'
);

-- VERIFIKASI SETELAH UPDATE: Cek berapa surat jalan dengan status 'belum_dibayar'
SELECT status_pembayaran_uang_rit, COUNT(*) as total
FROM surat_jalans
WHERE rit = 'menggunakan_rit'
GROUP BY status_pembayaran_uang_rit;
