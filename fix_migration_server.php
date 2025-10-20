<?php

// Fix migration untuk server - check kolom sebelum drop
echo "Fixing migration for server...\n";

$migrationPath = 'database/migrations/2025_10_19_135048_update_pricelist_gate_ins_to_pelabuhan_sunda_kelapa_structure.php';

if (file_exists($migrationPath)) {
    $content = file_get_contents($migrationPath);

    // Replace the down method dengan safe column dropping
    $newDownMethod = '
    public function down(): void
    {
        Schema::table(\'pricelist_gate_ins\', function (Blueprint $table) {
            // Check if columns exist before dropping
            if (Schema::hasColumn(\'pricelist_gate_ins\', \'kode\')) {
                $table->dropColumn(\'kode\');
            }
            if (Schema::hasColumn(\'pricelist_gate_ins\', \'keterangan\')) {
                $table->dropColumn(\'keterangan\');
            }
            if (Schema::hasColumn(\'pricelist_gate_ins\', \'catatan\')) {
                $table->dropColumn(\'catatan\');
            }
        });
    }';

    // Find and replace the down method
    $pattern = '/public function down\(\): void\s*\{[^}]*\}/s';
    $content = preg_replace($pattern, $newDownMethod, $content);

    // Backup and write new content
    copy($migrationPath, $migrationPath . '.backup');
    file_put_contents($migrationPath, $content);

    echo "✅ Migration fixed! Safe column dropping implemented.\n";
} else {
    echo "❌ Migration file not found!\n";
}

// Also create a fix script for the database
echo "\nCreating database fix script...\n";

$dbFixScript = "
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
";

file_put_contents('fix_pricelist_structure.sql', $dbFixScript);
echo "✅ Database fix script created: fix_pricelist_structure.sql\n";

echo "\nNext steps:\n";
echo "1. Upload this fixed migration to server\n";
echo "2. Or run the SQL fix script\n";
echo "3. Then continue with php artisan migrate\n";

?>
