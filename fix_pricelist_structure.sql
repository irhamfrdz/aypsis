
-- Fix for pricelist_gate_ins table structure
-- Run this if migration still fails

-- Check current table structure
DESCRIBE pricelist_gate_ins;

-- Add missing columns if they don't exist
ALTER TABLE pricelist_gate_ins 
ADD COLUMN IF NOT EXISTS pelabuhan VARCHAR(255) NULL AFTER id,
ADD COLUMN IF NOT EXISTS kegiatan VARCHAR(255) NULL AFTER pelabuhan,
ADD COLUMN IF NOT EXISTS gudang VARCHAR(255) NULL AFTER kegiatan,
ADD COLUMN IF NOT EXISTS kontainer VARCHAR(255) NULL AFTER gudang,
ADD COLUMN IF NOT EXISTS muatan VARCHAR(255) NULL AFTER kontainer,
ADD COLUMN IF NOT EXISTS tarif DECIMAL(15,2) NULL AFTER muatan,
ADD COLUMN IF NOT EXISTS status ENUM('aktif', 'tidak_aktif') DEFAULT 'aktif' AFTER tarif;

-- Mark migration as completed
INSERT IGNORE INTO migrations (migration, batch) 
VALUES ('2025_10_19_135048_update_pricelist_gate_ins_to_pelabuhan_sunda_kelapa_structure', 
        (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) as temp));
