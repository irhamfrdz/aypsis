<?php

require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Setup database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'aypsis',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== PERBAIKAN TOTAL AMOUNT PRANOTA ===\n\n";

// Get all pranota
$pranotas = DB::table('pranotalist')->get();

foreach ($pranotas as $pranota) {
    echo "Processing Pranota: {$pranota->no_invoice}\n";
    echo "Total Amount saat ini: Rp " . number_format($pranota->total_amount, 2) . "\n";

    // Decode tagihan_ids
    $tagihanIds = json_decode($pranota->tagihan_ids, true);

    if (empty($tagihanIds)) {
        echo "❌ Tagihan IDs kosong - skip\n\n";
        continue;
    }

    // Calculate correct total
    $correctTotal = 0;
    $validTagihan = 0;

    foreach ($tagihanIds as $tagihanId) {
        $tagihan = DB::table('daftar_tagihan_kontainer_sewa')->where('id', $tagihanId)->first();

        if ($tagihan) {
            $correctTotal += floatval($tagihan->grand_total);
            $validTagihan++;
        } else {
            echo "⚠️ Tagihan ID {$tagihanId} tidak ditemukan\n";
        }
    }

    echo "Total yang benar: Rp " . number_format($correctTotal, 2) . "\n";
    echo "Jumlah tagihan valid: {$validTagihan}\n";

    // Update if different
    if (abs($correctTotal - floatval($pranota->total_amount)) > 0.01) {
        echo "🔄 Updating total amount...\n";

        DB::table('pranotalist')
            ->where('id', $pranota->id)
            ->update([
                'total_amount' => $correctTotal,
                'jumlah_tagihan' => $validTagihan,
                'updated_at' => now()
            ]);

        echo "✅ Total amount updated!\n";
    } else {
        echo "✅ Total amount sudah benar\n";
    }

    echo "---\n\n";
}

echo "=== SELESAI ===\n";
