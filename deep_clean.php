<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DEEP DATABASE CHECK ===\n";

// Cek dengan berbagai cara
echo "1. Checking pranota_uang_rits table:\n";
$allRecords = DB::table('pranota_uang_rits')->get();
echo "Total records: " . $allRecords->count() . "\n";

if ($allRecords->count() > 0) {
    foreach ($allRecords as $record) {
        echo "Found: ID={$record->id}, no_pranota={$record->no_pranota}\n";
    }
}

echo "\n2. Specific check for PUR-10-25-000001:\n";
$specificRecord = DB::table('pranota_uang_rits')
    ->where('no_pranota', 'PUR-10-25-000001')
    ->first();

if ($specificRecord) {
    echo "❌ Found the problematic record!\n";
    echo "ID: {$specificRecord->id}\n";
    echo "no_pranota: {$specificRecord->no_pranota}\n";
    echo "created_at: {$specificRecord->created_at}\n";
    
    // Force delete
    $deleted = DB::table('pranota_uang_rits')
        ->where('no_pranota', 'PUR-10-25-000001')
        ->delete();
    echo "✅ Deleted {$deleted} record(s)\n";
} else {
    echo "✅ No specific record found\n";
}

echo "\n3. Check with LIKE pattern:\n";
$purRecords = DB::table('pranota_uang_rits')
    ->where('no_pranota', 'like', 'PUR%')
    ->get();

if ($purRecords->count() > 0) {
    echo "Found {$purRecords->count()} PUR records:\n";
    foreach ($purRecords as $record) {
        echo "- {$record->no_pranota} (ID: {$record->id})\n";
    }
    
    // Delete all PUR records
    $deleted = DB::table('pranota_uang_rits')
        ->where('no_pranota', 'like', 'PUR%')
        ->delete();
    echo "✅ Deleted {$deleted} PUR record(s)\n";
} else {
    echo "✅ No PUR records found\n";
}

echo "\n4. TRUNCATE table (nuclear option):\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');
DB::table('pranota_uang_rits')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "✅ Table truncated\n";

echo "\n5. Clean nomor_terakhir:\n";
$deletedNomor = DB::table('nomor_terakhir')->where('modul', 'PUR')->delete();
echo "✅ Deleted {$deletedNomor} PUR nomor_terakhir record(s)\n";

echo "\n=== FINAL VERIFICATION ===\n";
$finalCount = DB::table('pranota_uang_rits')->count();
$finalNomor = DB::table('nomor_terakhir')->where('modul', 'PUR')->count();

echo "pranota_uang_rits count: {$finalCount}\n";
echo "PUR nomor_terakhir count: {$finalNomor}\n";

if ($finalCount === 0 && $finalNomor === 0) {
    echo "✅ COMPLETELY CLEAN! Ready for new pranota.\n";
} else {
    echo "❌ Still has records\n";
}

echo "=== DONE ===\n";