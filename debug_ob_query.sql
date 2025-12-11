-- Debug query untuk cek status OB
-- Ganti nilai nama_kapal dan no_voyage sesuai dengan yang di screenshot

-- 1. Cek semua data BL untuk kapal dan voyage ini
SELECT 
    id,
    nomor_kontainer,
    sudah_ob,
    supir_id,
    tanggal_ob,
    nama_kapal,
    no_voyage,
    CHAR_LENGTH(nama_kapal) as kapal_length,
    CHAR_LENGTH(no_voyage) as voyage_length,
    HEX(nama_kapal) as kapal_hex,
    HEX(no_voyage) as voyage_hex
FROM bls 
WHERE nama_kapal = 'KM Sentosa 18' 
AND no_voyage = 'ST05PJ25'
ORDER BY id;

-- 2. Count sudah OB dengan berbagai cara
SELECT 
    'Method 1: sudah_ob = true' as method,
    COUNT(*) as count
FROM bls 
WHERE nama_kapal = 'KM Sentosa 18' 
AND no_voyage = 'ST05PJ25'
AND sudah_ob = true

UNION ALL

SELECT 
    'Method 2: sudah_ob = 1' as method,
    COUNT(*) as count
FROM bls 
WHERE nama_kapal = 'KM Sentosa 18' 
AND no_voyage = 'ST05PJ25'
AND sudah_ob = 1

UNION ALL

SELECT 
    'Method 3: sudah_ob IS NOT NULL AND != 0' as method,
    COUNT(*) as count
FROM bls 
WHERE nama_kapal = 'KM Sentosa 18' 
AND no_voyage = 'ST05PJ25'
AND sudah_ob IS NOT NULL
AND sudah_ob != 0;

-- 3. Cek struktur kolom sudah_ob
SHOW COLUMNS FROM bls LIKE 'sudah_ob';

-- 4. Cek data yang sudah OB di halaman supir (dari AYPU0291421)
SELECT 
    id,
    nomor_kontainer,
    sudah_ob,
    CAST(sudah_ob AS UNSIGNED) as sudah_ob_int,
    supir_id,
    tanggal_ob
FROM bls 
WHERE nomor_kontainer = 'AYPU0291421';
