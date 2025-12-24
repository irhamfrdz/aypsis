<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Memperbaiki status pranota_ob_items berdasarkan biaya...\n\n";

// Get all pricelist
$pricelists = DB::table('master_pricelist_ob')->get();
$reverseMap = [];

foreach ($pricelists as $pl) {
    // Create reverse map: biaya|size => status
    $key = $pl->biaya . '|' . $pl->size_kontainer;
    $reverseMap[$key] = $pl->status_kontainer;
}

echo "Pricelist mapping:\n";
foreach ($reverseMap as $key => $status) {
    echo "  $key => $status\n";
}
echo "\n";

// Get all pranota_ob_items
$items = DB::table('pranota_ob_items')->whereNotNull('biaya')->whereNotNull('size')->get();

echo "Total items to check: " . $items->count() . "\n\n";

$fixed = 0;
$alreadyCorrect = 0;

foreach ($items as $item) {
    // Normalize size
    $sizeStr = null;
    if ($item->size) {
        $sizeInt = intval($item->size);
        if ($sizeInt === 20) {
            $sizeStr = '20ft';
        } elseif ($sizeInt === 40) {
            $sizeStr = '40ft';
        }
    }
    
    if (!$sizeStr) {
        continue;
    }
    
    // Check what status should be based on biaya
    $key = $item->biaya . '|' . $sizeStr;
    $correctStatus = $reverseMap[$key] ?? null;
    
    if (!$correctStatus) {
        echo "⚠️  Item ID {$item->id}: Biaya {$item->biaya} dengan size {$sizeStr} tidak ada di pricelist\n";
        continue;
    }
    
    // Check if status is wrong
    if ($item->status !== $correctStatus) {
        echo "🔧 Fixing Item ID {$item->id}: {$item->nomor_kontainer}\n";
        echo "   Size: {$sizeStr}, Biaya: {$item->biaya}\n";
        echo "   Status saat ini: " . ($item->status ?? 'null') . " => Status benar: {$correctStatus}\n";
        
        DB::table('pranota_ob_items')
            ->where('id', $item->id)
            ->update(['status' => $correctStatus]);
        
        $fixed++;
    } else {
        $alreadyCorrect++;
    }
}

echo "\n✅ Selesai!\n";
echo "Total yang diperbaiki: $fixed\n";
echo "Total yang sudah benar: $alreadyCorrect\n";
