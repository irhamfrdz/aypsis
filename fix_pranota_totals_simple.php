<?php

// Simple script to fix existing pranota total amounts
// Run with: php fix_pranota_totals_simple.php

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;
use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== Fixing Existing Pranota Total Amounts ===\n\n";

try {
    // Find pranota with total_amount = 0 but has tagihan
    $problemPranota = Pranota::where(function($query) {
            $query->where('total_amount', 0)
                  ->orWhereNull('total_amount');
        })
        ->where('jumlah_tagihan', '>', 0)
        ->get();

    echo "Found " . count($problemPranota) . " pranota with total amount issues\n\n";

    if (count($problemPranota) == 0) {
        echo "✓ No pranota need fixing. All totals are correct!\n";
        exit;
    }

    foreach ($problemPranota as $pranota) {
        echo "Processing: {$pranota->no_invoice}\n";
        echo "  Current total: Rp " . number_format((float)$pranota->total_amount, 2, ',', '.') . "\n";
        echo "  Jumlah tagihan: {$pranota->jumlah_tagihan} items\n";

        // Get tagihan IDs
        $tagihanIds = $pranota->tagihan_ids;

        if (empty($tagihanIds)) {
            echo "  → Skipping: No tagihan IDs\n\n";
            continue;
        }

        // Calculate correct total from grand_total field
        $correctTotal = DaftarTagihanKontainerSewa::whereIn('id', $tagihanIds)
            ->sum('grand_total');

        echo "  New calculated total: Rp " . number_format($correctTotal, 2, ',', '.') . "\n";

        // Update the pranota
        $pranota->total_amount = $correctTotal;
        $pranota->save();

        echo "  ✓ Updated successfully!\n\n";
    }

    echo "=== All pranota totals fixed! ===\n";
    echo "Please refresh your pranota list to see the correct Total Amount values.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
