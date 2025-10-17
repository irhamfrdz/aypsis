-- SQL untuk memperbaiki migration master_kapals secara manual
-- Jalankan ini jika rollback tidak memungkinkan karena ada data production

-- 1. Tandai migration sebagai sudah dijalankan (skip yang bermasalah)
UPDATE migrations SET batch = (SELECT MAX(batch) FROM (SELECT batch FROM migrations) as temp) 
WHERE migration = '2025_10_16_160202_add_capacity_fields_to_master_kapals_table';

-- 2. Tambahkan kolom-kolom yang diperlukan secara manual
ALTER TABLE master_kapals 
ADD COLUMN kapasitas_kontainer_palka INT NULL COMMENT 'Kapasitas kontainer di palka kapal' AFTER lokasi;

ALTER TABLE master_kapals 
ADD COLUMN kapasitas_kontainer_deck INT NULL COMMENT 'Kapasitas kontainer di deck kapal' AFTER kapasitas_kontainer_palka;

ALTER TABLE master_kapals 
ADD COLUMN gross_tonnage DECIMAL(12,2) NULL COMMENT 'Gross tonnage kapal dalam ton' AFTER kapasitas_kontainer_deck;

-- 3. Verify struktur tabel
DESCRIBE master_kapals;