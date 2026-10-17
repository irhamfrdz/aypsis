-- Jalankan pada database production yang digunakan aplikasi AYPSIS.
-- Target: tanggal berangkat seluruh manifest voyage AP14BJ26 menjadi 15 Oktober 2026.

-- Periksa jumlah dan tanggal yang saat ini tersimpan sebelum perubahan.
SELECT tanggal_berangkat, COUNT(*) AS jumlah_manifest
FROM manifests
WHERE no_voyage = 'AP14BJ26'
GROUP BY tanggal_berangkat
ORDER BY tanggal_berangkat;

START TRANSACTION;

UPDATE manifests
SET tanggal_berangkat = '2026-10-15'
WHERE no_voyage = 'AP14BJ26'
  AND (tanggal_berangkat IS NULL OR tanggal_berangkat <> '2026-10-15');

-- Jumlah baris yang diubah oleh UPDATE di atas.
SELECT ROW_COUNT() AS jumlah_diubah;

-- Pastikan seluruh manifest voyage ini sudah memiliki tanggal yang diminta.
SELECT COUNT(*) AS total_manifest,
       SUM(CASE WHEN tanggal_berangkat = '2026-10-15' THEN 1 ELSE 0 END) AS tanggal_sesuai
FROM manifests
WHERE no_voyage = 'AP14BJ26';

COMMIT;
