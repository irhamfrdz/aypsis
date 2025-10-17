-- SQL untuk memperbaiki migration master_kapals secara manual (Server Compatible)
-- Jalankan ini jika rollback tidak memungkinkan karena ada data production

-- 1. Hapus migration record yang gagal
DELETE FROM migrations WHERE migration = '2025_10_16_160202_add_capacity_fields_to_master_kapals_table';

-- 2. Check struktur tabel saat ini
SELECT 'Current table structure:' as info;
DESCRIBE master_kapals;

-- 3. Tambahkan kolom-kolom yang diperlukan secara conditional
-- Script ini akan skip jika kolom sudah ada

-- Check if kapasitas_kontainer_palka exists, if not add it
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'master_kapals'
AND COLUMN_NAME = 'kapasitas_kontainer_palka';

-- Determine best position for new column
SET @after_column = '';
SELECT CASE
    WHEN EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'master_kapals' AND COLUMN_NAME = 'pelayaran')
    THEN 'pelayaran'
    WHEN EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'master_kapals' AND COLUMN_NAME = 'lokasi')
    THEN 'lokasi'
    WHEN EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'master_kapals' AND COLUMN_NAME = 'catatan')
    THEN 'catatan'
    ELSE 'nama_kapal'
END INTO @after_column;

-- Add kapasitas_kontainer_palka if it doesn't exist
SET @sql1 = IF(@col_exists = 0,
    CONCAT('ALTER TABLE master_kapals ADD COLUMN kapasitas_kontainer_palka INT NULL COMMENT ''Kapasitas kontainer di palka kapal'' AFTER `', @after_column, '`'),
    'SELECT ''kapasitas_kontainer_palka already exists'' as status');
PREPARE stmt1 FROM @sql1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

-- Check if kapasitas_kontainer_deck exists
SET @col_exists2 = 0;
SELECT COUNT(*) INTO @col_exists2
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'master_kapals'
AND COLUMN_NAME = 'kapasitas_kontainer_deck';

SET @sql2 = IF(@col_exists2 = 0,
    'ALTER TABLE master_kapals ADD COLUMN kapasitas_kontainer_deck INT NULL COMMENT ''Kapasitas kontainer di deck kapal'' AFTER kapasitas_kontainer_palka',
    'SELECT ''kapasitas_kontainer_deck already exists'' as status');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Check if gross_tonnage exists
SET @col_exists3 = 0;
SELECT COUNT(*) INTO @col_exists3
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'master_kapals'
AND COLUMN_NAME = 'gross_tonnage';

SET @sql3 = IF(@col_exists3 = 0,
    'ALTER TABLE master_kapals ADD COLUMN gross_tonnage DECIMAL(12,2) NULL COMMENT ''Gross tonnage kapal dalam ton'' AFTER kapasitas_kontainer_deck',
    'SELECT ''gross_tonnage already exists'' as status');
PREPARE stmt3 FROM @sql3;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

-- 4. Insert successful migration record
INSERT INTO migrations (migration, batch)
VALUES ('2025_10_16_160202_add_capacity_fields_to_master_kapals_table',
        (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) as temp));

-- 5. Verify final struktur tabel
SELECT 'Final table structure:' as info;
DESCRIBE master_kapals;

SELECT 'Migration fix completed successfully' as status;
