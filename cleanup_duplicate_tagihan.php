<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Cleanup Duplikat Tagihan Kontainer ===\n\n";

// Cari semua duplikat
$duplicates = DB::select("
    SELECT nomor_kontainer, periode, COUNT(*) as count
    FROM daftar_tagihan_kontainer_sewa
    GROUP BY nomor_kontainer, periode
    HAVING COUNT(*) > 1
    ORDER BY nomor_kontainer, periode
");

echo "Total kontainer dengan duplikat: " . count($duplicates) . "\n\n";

$totalDeleted = 0;
$totalKept = 0;

foreach ($duplicates as $dup) {
    $container = $dup->nomor_kontainer;
    $periode = $dup->periode;
    $count = $dup->count;
    
    echo "Processing {$container} - Periode {$periode} ({$count} records)\n";
    
    // Ambil semua record untuk container+periode ini
    $records = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', $container)
        ->where('periode', $periode)
        ->orderBy('id')
        ->get();
    
    // Tentukan mana yang akan di-keep:
    // Priority: pranota > invoice > latest
    // HANYA KEEP 1 RECORD per nomor_kontainer + periode!
    
    $toKeep = null;
    $toDelete = [];
    
    // Convert to array first
    $recordsArray = $records->all();
    
    // Sort by priority: pranota first, then invoice, then latest ID
    usort($recordsArray, function($a, $b) {
        // Pranota has highest priority
        if ($a->status_pranota && !$b->status_pranota) return -1;
        if (!$a->status_pranota && $b->status_pranota) return 1;
        
        // Then invoice
        if ($a->invoice_id && !$b->invoice_id) return -1;
        if (!$a->invoice_id && $b->invoice_id) return 1;
        
        // Then latest ID
        return $b->id <=> $a->id;
    });
    
    $toKeep = $recordsArray[0]; // Keep yang paling prioritas
    
    // Sisanya delete
    for ($i = 1; $i < count($recordsArray); $i++) {
        $toDelete[] = $recordsArray[$i];
    }
    
    $keepReason = '';
    if ($toKeep->status_pranota) {
        $keepReason = 'with pranota';
    } elseif ($toKeep->invoice_id) {
        $keepReason = 'with invoice';
    } else {
        $keepReason = 'latest';
    }
    
    echo "  Keep: 1 record ({$keepReason}, ID {$toKeep->id})\n";
    echo "  Delete: " . count($toDelete) . " records\n";
    
    // Delete yang perlu dihapus
    foreach ($toDelete as $rec) {
        $recReason = '';
        if ($rec->status_pranota) $recReason = ' (pranota)';
        elseif ($rec->invoice_id) $recReason = ' (invoice)';
        echo "    Deleting ID {$rec->id}{$recReason}: {$rec->masa}\n";
        DB::table('daftar_tagihan_kontainer_sewa')
            ->where('id', $rec->id)
            ->delete();
        $totalDeleted++;
    }
    
    $totalKept += 1;
    echo "\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total Records Kept: {$totalKept}\n";
echo "Total Records Deleted: {$totalDeleted}\n";
echo "\nSELESAI!\n";
