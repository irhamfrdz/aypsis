<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    
    $oldName = 'KM. SRIWIJAYA';
    $newName = 'KM. SRIWIJAYA RAYA';
    
    echo "=== Update Nama Kapal dari '{$oldName}' ke '{$newName}' ===\n\n";
    
    // Update tabel naik_kapal
    $updatedNaikKapal = DB::table('naik_kapal')
        ->where('nama_kapal', $oldName)
        ->update(['nama_kapal' => $newName]);
    
    echo "✓ Updated naik_kapal: {$updatedNaikKapal} records\n";
    
    // Update tabel bls
    $updatedBls = DB::table('bls')
        ->where('nama_kapal', $oldName)
        ->update(['nama_kapal' => $newName]);
    
    echo "✓ Updated bls: {$updatedBls} records\n";
    
    // Update tabel biaya_kapals
    $updatedBiayaKapals = DB::table('biaya_kapals')
        ->where('nama_kapal', $oldName)
        ->update(['nama_kapal' => $newName]);
    
    echo "✓ Updated biaya_kapals: {$updatedBiayaKapals} records\n";
    
    // Update tabel master_kapals
    $updatedMasterKapals = DB::table('master_kapals')
        ->where('nama_kapal', $oldName)
        ->update(['nama_kapal' => $newName]);
    
    echo "✓ Updated master_kapals: {$updatedMasterKapals} records\n";
    
    // Check for other tables that might have nama_kapal column
    echo "\n=== Checking other tables ===\n";
    
    // Get all tables
    $tables = DB::select('SHOW TABLES');
    $databaseName = DB::getDatabaseName();
    $tableKey = "Tables_in_{$databaseName}";
    
    $additionalUpdates = [];
    foreach ($tables as $table) {
        $tableName = $table->$tableKey;
        
        // Skip already updated tables
        if (in_array($tableName, ['naik_kapal', 'bls', 'biaya_kapals', 'master_kapals', 'migrations'])) {
            continue;
        }
        
        // Check if table has nama_kapal column
        try {
            $columns = DB::select("SHOW COLUMNS FROM {$tableName} LIKE 'nama_kapal'");
            if (!empty($columns)) {
                // Check if there are records with old name
                $count = DB::table($tableName)->where('nama_kapal', $oldName)->count();
                if ($count > 0) {
                    $updated = DB::table($tableName)
                        ->where('nama_kapal', $oldName)
                        ->update(['nama_kapal' => $newName]);
                    
                    echo "✓ Updated {$tableName}: {$updated} records\n";
                    $additionalUpdates[] = $tableName;
                }
            }
        } catch (\Exception $e) {
            // Skip tables that can't be queried
            continue;
        }
    }
    
    if (empty($additionalUpdates)) {
        echo "No additional tables found with nama_kapal column containing old name.\n";
    }
    
    DB::commit();
    
    echo "\n✅ All updates completed successfully!\n";
    echo "\n=== Summary ===\n";
    echo "Updated tables:\n";
    echo "- naik_kapal: {$updatedNaikKapal} records\n";
    echo "- bls: {$updatedBls} records\n";
    echo "- biaya_kapals: {$updatedBiayaKapals} records\n";
    echo "- master_kapals: {$updatedMasterKapals} records\n";
    if (!empty($additionalUpdates)) {
        foreach ($additionalUpdates as $table) {
            echo "- {$table}\n";
        }
    }
    
    echo "\nNama kapal berhasil diubah dari '{$oldName}' ke '{$newName}'\n";
    echo "Sekarang dropdown voyage seharusnya menampilkan data yang benar.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
