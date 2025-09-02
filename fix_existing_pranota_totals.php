<?php

// Fix untuk update total_amount pada pranota existing yang showing Rp 0,00
// Script ini akan:
// 1. Find pranota dengan total_amount = 0 atau null tapi ada tagihan_ids
// 2. Recalculate total_amount berdasarkan Grand Total dari tagihan

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Pranota;
use App\Models\DaftarTagihanKontainerSewa;

// Setup database connection (gunakan config yang sama dengan Laravel)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Fix Existing Pranota Totals ===\n\n";

try {
    // Find pranota dengan total_amount = 0 atau null tapi memiliki tagihan
    $problemPranota = DB::table('pranota')
        ->where(function($query) {
            $query->where('total_amount', 0)
                  ->orWhereNull('total_amount');
        })
        ->where('jumlah_tagihan', '>', 0)
        ->get();

    echo "Found " . count($problemPranota) . " pranota dengan total amount issue\n\n";

    if (count($problemPranota) == 0) {
        echo "No pranota need fixing. All totals are correct!\n";
        exit;
    }

    foreach ($problemPranota as $pranota) {
        echo "Processing Pranota: {$pranota->no_invoice}\n";
        echo "  Current total_amount: {$pranota->total_amount}\n";
        echo "  Jumlah tagihan: {$pranota->jumlah_tagihan}\n";

        // Decode tagihan_ids
        $tagihanIds = json_decode($pranota->tagihan_ids, true);

        if (empty($tagihanIds)) {
            echo "  → Skipping: No tagihan IDs found\n\n";
            continue;
        }

        echo "  Tagihan IDs: " . implode(', ', $tagihanIds) . "\n";

        // Calculate correct total from Grand Total of tagihan
        $correctTotal = DB::table('daftar_tagihan_kontainer_sewa')
            ->whereIn('id', $tagihanIds)
            ->sum('grand_total');

        echo "  Calculated total from grand_total: Rp " . number_format($correctTotal, 2, ',', '.') . "\n";

        // Update pranota
        $updated = DB::table('pranota')
            ->where('id', $pranota->id)
            ->update([
                'total_amount' => $correctTotal,
                'updated_at' => now()
            ]);

        if ($updated) {
            echo "  ✓ Successfully updated total_amount\n";
        } else {
            echo "  ✗ Failed to update\n";
        }

        echo "\n";
    }

    echo "=== Fix completed! ===\n";
    echo "Please check your pranota list to verify the Total Amount now shows correctly.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Make sure you're running this from the Laravel project root directory.\n";
}
