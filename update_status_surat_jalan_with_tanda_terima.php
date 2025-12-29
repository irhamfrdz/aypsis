<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Update Status Surat Jalan yang Sudah Ada Tanda Terima ===\n\n";

try {
    // Get all surat_jalan IDs that have tanda_terima
    $suratJalanWithTandaTerima = DB::table('tanda_terimas')
        ->select('surat_jalan_id')
        ->distinct()
        ->whereNotNull('surat_jalan_id')
        ->pluck('surat_jalan_id')
        ->toArray();

    echo "Found " . count($suratJalanWithTandaTerima) . " surat jalan with tanda terima\n\n";

    if (empty($suratJalanWithTandaTerima)) {
        echo "✓ No surat jalan to update\n";
        exit(0);
    }

    // Get current status of these surat jalan
    $suratJalanData = DB::table('surat_jalans')
        ->whereIn('id', $suratJalanWithTandaTerima)
        ->select('id', 'no_surat_jalan', 'status_surat_jalan')
        ->get();

    echo "=== Current Status ===\n";
    $needUpdateCount = 0;
    foreach ($suratJalanData as $sj) {
        $status = $sj->status_surat_jalan ?: 'NULL';
        echo "  [{$sj->id}] {$sj->no_surat_jalan} - Status: {$status}";
        
        if ($sj->status_surat_jalan !== 'selesai') {
            echo " ❌ (needs update)";
            $needUpdateCount++;
        } else {
            echo " ✓ (already selesai)";
        }
        echo "\n";
    }

    echo "\n=== Summary ===\n";
    echo "Total surat jalan with tanda terima: " . count($suratJalanWithTandaTerima) . "\n";
    echo "Already have status 'selesai': " . (count($suratJalanWithTandaTerima) - $needUpdateCount) . "\n";
    echo "Need to be updated: {$needUpdateCount}\n\n";

    if ($needUpdateCount === 0) {
        echo "✓ All surat jalan already have status 'selesai'\n";
        exit(0);
    }

    // Ask for confirmation
    echo "Do you want to proceed with updating {$needUpdateCount} surat jalan? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);

    if ($confirmation !== 'yes' && $confirmation !== 'y') {
        echo "\n❌ Update cancelled by user\n";
        exit(0);
    }

    echo "\n=== Starting Update ===\n";

    // Update all surat jalan that have tanda terima to status 'selesai'
    $updated = DB::table('surat_jalans')
        ->whereIn('id', $suratJalanWithTandaTerima)
        ->where(function($query) {
            $query->whereNull('status_surat_jalan')
                  ->orWhere('status_surat_jalan', '!=', 'selesai');
        })
        ->update([
            'status_surat_jalan' => 'selesai',
            'updated_at' => now()
        ]);

    echo "✓ Updated {$updated} surat jalan records\n\n";

    // Verify the update
    echo "=== Verification ===\n";
    $verifyData = DB::table('surat_jalans')
        ->whereIn('id', $suratJalanWithTandaTerima)
        ->select('id', 'no_surat_jalan', 'status_surat_jalan')
        ->get();

    $allSelesai = true;
    foreach ($verifyData as $sj) {
        $status = $sj->status_surat_jalan;
        echo "  [{$sj->id}] {$sj->no_surat_jalan} - Status: {$status}";
        
        if ($status === 'selesai') {
            echo " ✓\n";
        } else {
            echo " ❌ (FAILED)\n";
            $allSelesai = false;
        }
    }

    echo "\n";
    if ($allSelesai) {
        echo "✅ All surat jalan successfully updated to 'selesai'\n";
    } else {
        echo "⚠ Some surat jalan failed to update. Please check manually.\n";
    }

    // Show statistics by status
    echo "\n=== Status Statistics ===\n";
    $statusStats = DB::table('surat_jalans')
        ->select('status_surat_jalan', DB::raw('count(*) as total'))
        ->groupBy('status_surat_jalan')
        ->orderBy('total', 'desc')
        ->get();

    foreach ($statusStats as $stat) {
        $status = $stat->status_surat_jalan ?: 'NULL/Empty';
        echo "  {$status}: {$stat->total} records\n";
    }

    echo "\n✅ Script completed successfully!\n";

} catch (\Exception $e) {
    echo "\n❌ Error occurred: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
