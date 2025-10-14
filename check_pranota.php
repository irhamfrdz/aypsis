<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PranotaTagihanKontainerSewa;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== ANALISIS PRANOTA KOSONG ===\n\n";

// Ambil 3 pranota terbaru
$pranotaList = PranotaTagihanKontainerSewa::latest()->limit(3)->get();

foreach ($pranotaList as $pranota) {
    echo "Pranota: {$pranota->no_invoice}\n";
    echo "Tanggal: {$pranota->created_at}\n";
    echo "Status: {$pranota->status}\n";
    echo "Jumlah Tagihan: {$pranota->jumlah_tagihan}\n";
    echo "Total Amount: " . number_format($pranota->total_amount ?? 0, 0, ',', '.') . "\n";

    if (is_array($pranota->tagihan_kontainer_sewa_ids)) {
        echo "Tagihan IDs: " . implode(', ', $pranota->tagihan_kontainer_sewa_ids) . "\n";

        // Cek apakah tagihan masih ada di database
        $existingTagihan = DaftarTagihanKontainerSewa::whereIn('id', $pranota->tagihan_kontainer_sewa_ids)->get();
        echo "Tagihan yang masih ada: {$existingTagihan->count()} dari " . count($pranota->tagihan_kontainer_sewa_ids) . "\n";

        if ($existingTagihan->count() > 0) {
            echo "Contoh nomor kontainer: " . $existingTagihan->first()->nomor_kontainer . "\n";
        }
    } else {
        echo "Tagihan IDs: " . ($pranota->tagihan_kontainer_sewa_ids ?? 'NULL') . "\n";
    }

    echo "---\n";
}

// Cek total tagihan yang terhubung ke pranota
echo "\n=== STATISTIK ===\n";
$totalPranota = PranotaTagihanKontainerSewa::count();
$totalTagihan = DaftarTagihanKontainerSewa::count();
$tagihanDenganPranota = DaftarTagihanKontainerSewa::whereNotNull('pranota_id')->count();

echo "Total Pranota: {$totalPranota}\n";
echo "Total Tagihan: {$totalTagihan}\n";
echo "Tagihan dengan Pranota ID: {$tagihanDenganPranota}\n";

// Cek apakah ada tagihan yang status_pranota = 'included' tapi pranota_id NULL
$inconsistentTagihan = DaftarTagihanKontainerSewa::where('status_pranota', 'included')
    ->whereNull('pranota_id')
    ->count();
echo "Tagihan inconsistent (status included tapi pranota_id NULL): {$inconsistentTagihan}\n";

// Cek pranota yang tagihan_kontainer_sewa_ids kosong atau NULL
$pranotaKosong = PranotaTagihanKontainerSewa::where(function($q) {
    $q->whereNull('tagihan_kontainer_sewa_ids')
      ->orWhere('tagihan_kontainer_sewa_ids', '[]')
      ->orWhere('tagihan_kontainer_sewa_ids', 'null');
})->count();
echo "Pranota dengan tagihan_ids kosong: {$pranotaKosong}\n";

echo "\n=== SELESAI ===\n";
