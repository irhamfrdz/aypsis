-- Quick SQL Fix for Foreign Key Constraint Error
-- Run this in MySQL if migration still fails

-- 1. Check existing foreign key constraints
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND REFERENCED_TABLE_NAME = 'master_services';

-- 2. Drop all foreign key constraints referencing master_services
SET FOREIGN_KEY_CHECKS = 0;

-- Drop the specific constraint mentioned in error
ALTER TABLE pricelist_gate_ins DROP FOREIGN KEY pricelist_gate_ins_service_id_foreign;

-- Check for other constraints
SELECT CONCAT('ALTER TABLE ', TABLE_NAME, ' DROP FOREIGN KEY ', CONSTRAINT_NAME, ';') as drop_statement
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND REFERENCED_TABLE_NAME = 'master_services';

-- 3. Drop service_id columns
ALTER TABLE pricelist_gate_ins DROP COLUMN IF EXISTS service_id;
ALTER TABLE gate_ins DROP COLUMN IF EXISTS service_id;
ALTER TABLE kontainers DROP COLUMN IF EXISTS service_id;

-- 4. Drop master_services table
DROP TABLE IF EXISTS master_services;

-- 5. Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- 6. Mark migration as completed
INSERT IGNORE INTO migrations (migration, batch) VALUES 
('2025_10_20_134713_drop_service_id_from_tables', 
 (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) as temp));

-- 7. Verify tables are clean
SHOW CREATE TABLE pricelist_gate_ins;
SHOW CREATE TABLE gate_ins;
SHOW CREATE TABLE kontainers;