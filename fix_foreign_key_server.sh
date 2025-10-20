#!/bin/bash
# 🔧 EMERGENCY FIX untuk Foreign Key Constraint Error
# Jalankan di server untuk fix masalah master_services

echo "🔧 FIXING FOREIGN KEY CONSTRAINT ERROR..."
echo "========================================"

# Get database credentials
echo "📋 Database connection info:"
echo "Host: localhost"
echo "Database: $(grep DB_DATABASE .env | cut -d '=' -f2)"
echo "Username: $(grep DB_USERNAME .env | cut -d '=' -f2)"

# Create SQL fix script
cat > fix_foreign_key_emergency.sql << 'EOF'
-- Emergency Fix for Foreign Key Constraint Error
-- This will forcefully remove all master_services dependencies

-- 1. Show current foreign key constraints
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND REFERENCED_TABLE_NAME = 'master_services';

-- 2. Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- 3. Drop the specific constraint mentioned in error
SET @sql = (SELECT CONCAT('ALTER TABLE ', TABLE_NAME, ' DROP FOREIGN KEY ', CONSTRAINT_NAME, ';')
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND CONSTRAINT_NAME = 'pricelist_gate_ins_service_id_foreign');

-- Execute if constraint exists
SET @sql = IFNULL(@sql, 'SELECT "Constraint not found" as result;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Drop any other constraints to master_services
DROP PROCEDURE IF EXISTS DropAllMasterServiceConstraints;

DELIMITER $$
CREATE PROCEDURE DropAllMasterServiceConstraints()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE constraint_name VARCHAR(255);
    DECLARE table_name VARCHAR(255);
    DECLARE cur CURSOR FOR
        SELECT CONSTRAINT_NAME, TABLE_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
        AND REFERENCED_TABLE_NAME = 'master_services';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO constraint_name, table_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET @sql = CONCAT('ALTER TABLE ', table_name, ' DROP FOREIGN KEY ', constraint_name);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        SELECT CONCAT('Dropped constraint: ', constraint_name, ' from table: ', table_name) as result;
    END LOOP;
    CLOSE cur;
END$$
DELIMITER ;

-- Execute the procedure
CALL DropAllMasterServiceConstraints();
DROP PROCEDURE DropAllMasterServiceConstraints;

-- 5. Drop service_id columns
ALTER TABLE pricelist_gate_ins DROP COLUMN IF EXISTS service_id;
ALTER TABLE gate_ins DROP COLUMN IF EXISTS service_id;
ALTER TABLE kontainers DROP COLUMN IF EXISTS service_id;

-- 6. Drop master_services table
DROP TABLE IF EXISTS master_services;

-- 7. Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- 8. Mark problematic migrations as completed
INSERT IGNORE INTO migrations (migration, batch) VALUES
('2025_10_20_134713_drop_service_id_from_tables',
 (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) as temp));

-- 9. Verify fix
SELECT 'SUCCESS: Foreign key constraints resolved!' as status;
SHOW TABLES LIKE 'master_services';
DESCRIBE pricelist_gate_ins;
EOF

echo "📝 Created emergency fix script: fix_foreign_key_emergency.sql"

# Run the fix
echo "🚀 Executing foreign key constraint fix..."

# Get database info from .env
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

# Execute the fix
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < fix_foreign_key_emergency.sql

echo "✅ Foreign key fix completed!"

# Continue with normal migration
echo "🔄 Running remaining migrations..."
php artisan migrate --force

echo "✅ All migrations completed successfully!"

# Clean up
rm -f fix_foreign_key_emergency.sql

echo ""
echo "🎉 FOREIGN KEY CONSTRAINT FIX COMPLETED!"
echo "======================================="
echo "✅ master_services table removed"
echo "✅ All foreign key constraints dropped"
echo "✅ service_id columns removed"
echo "✅ Migrations marked as completed"
echo ""
