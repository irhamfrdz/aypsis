-- QUICK MANUAL FIX untuk Foreign Key Error
-- Copy-paste script ini ke MySQL prompt di server

-- 1. Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Drop the specific constraint
ALTER TABLE pricelist_gate_ins DROP FOREIGN KEY pricelist_gate_ins_service_id_foreign;

-- 3. Check for other constraints and drop them
SELECT CONCAT('ALTER TABLE ', TABLE_NAME, ' DROP FOREIGN KEY ', CONSTRAINT_NAME, ';') as drop_commands
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND REFERENCED_TABLE_NAME = 'master_services';

-- 4. Drop service_id columns
ALTER TABLE pricelist_gate_ins DROP COLUMN service_id;
ALTER TABLE gate_ins DROP COLUMN IF EXISTS service_id;
ALTER TABLE kontainers DROP COLUMN IF EXISTS service_id;

-- 5. Drop master_services table
DROP TABLE master_services;

-- 6. Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- 7. Mark migration as completed
INSERT INTO migrations (migration, batch) VALUES
('2025_10_20_134713_drop_service_id_from_tables',
 (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) as temp));

-- 8. Verify success
SELECT 'Foreign key fix completed!' as status;
SHOW TABLES LIKE 'master_services';

-- Exit MySQL
-- exit
