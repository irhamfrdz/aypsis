<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Import Data ===\n\n";

// Check table structure
echo "1. Struktur tabel daftar_tagihan_kontainer_sewa:\n";
$columns = DB::select("DESCRIBE daftar_tagihan_kontainer_sewa");
foreach ($columns as $col) {
    echo "   - {$col->Field} ({$col->Type})\n";
}

// Check sample data
echo "\n2. Sample data (5 records):\n";
$samples = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('id', 'nomor_kontainer', 'group', 'periode', 'status_pranota', 'vendor')
    ->limit(5)
    ->get();

foreach ($samples as $sample) {
    echo "   ID: {$sample->id}\n";
    echo "   Nomor: {$sample->nomor_kontainer}\n";
    echo "   Group: " . ($sample->group ?? 'NULL') . "\n";
    echo "   Periode: " . ($sample->periode ?? 'NULL') . "\n";
    echo "   Status Pranota: " . ($sample->status_pranota ?? 'NULL') . "\n";
    echo "   Vendor: {$sample->vendor}\n";
    echo "   ---\n";
}

// Check for specific containers from CSV
echo "\n3. Checking specific containers from CSV:\n";
$containers = ['CBHU3952697', 'CBHU4077764', 'CBHU5876322'];

foreach ($containers as $container) {
    $records = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', $container)
        ->select('id', 'nomor_kontainer', 'group', 'periode', 'status_pranota')
        ->get();

    echo "\n   Kontainer: {$container}\n";
    if ($records->isEmpty()) {
        echo "   ❌ TIDAK DITEMUKAN\n";
    } else {
        echo "   ✅ Ditemukan {$records->count()} record(s):\n";
        foreach ($records as $rec) {
            echo "      - Group: " . ($rec->group ?? 'NULL') . ", Periode: " . ($rec->periode ?? 'NULL') . ", Status: " . ($rec->status_pranota ?? 'NULL') . "\n";
        }
    }
}

// Count by status
echo "\n4. Total records by status_pranota:\n";
$statusCounts = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('status_pranota', DB::raw('COUNT(*) as count'))
    ->groupBy('status_pranota')
    ->get();

foreach ($statusCounts as $status) {
    $statusLabel = $status->status_pranota ?? 'NULL (Belum masuk pranota)';
    echo "   - {$statusLabel}: {$status->count}\n";
}

echo "\n5. Total records yang bisa diimport (status_pranota IS NULL):\n";
$availableCount = DB::table('daftar_tagihan_kontainer_sewa')
    ->whereNull('status_pranota')
    ->count();
echo "   Total: {$availableCount} records\n";

if ($availableCount > 0) {
    echo "\n   Sample available records:\n";
    $available = DB::table('daftar_tagihan_kontainer_sewa')
        ->whereNull('status_pranota')
        ->select('nomor_kontainer', 'group', 'periode', 'vendor')
        ->limit(5)
        ->get();

    foreach ($available as $rec) {
        echo "      - {$rec->nomor_kontainer} | Group: " . ($rec->group ?? 'NULL') . " | Periode: " . ($rec->periode ?? 'NULL') . " | Vendor: {$rec->vendor}\n";
    }
}

echo "\n=== DONE ===\n";
