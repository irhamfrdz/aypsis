<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$container = $argv[1] ?? 'CBHU3952697';

echo "=== Verifikasi Tagihan Kontainer: {$container} ===\n\n";

// Data di kontainers
$kontainer = DB::table('kontainers')
    ->where('nomor_seri_gabungan', $container)
    ->first();

if (!$kontainer) {
    echo "❌ Kontainer tidak ditemukan!\n";
    exit(1);
}

echo "📦 Data Master Kontainer:\n";
echo "   Vendor: {$kontainer->vendor}\n";
echo "   Ukuran: {$kontainer->ukuran}\n";
echo "   Tanggal Mulai: {$kontainer->tanggal_mulai_sewa}\n";
echo "   Tanggal Selesai: " . ($kontainer->tanggal_selesai_sewa ?? 'NULL (masih berjalan)') . "\n\n";

// Data di daftar_tagihan_kontainer_sewa
$tagihan = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', $container)
    ->orderBy('periode')
    ->get();

echo "📋 Data Tagihan ({$tagihan->count()} records):\n";
echo str_repeat("─", 100) . "\n";
printf("%-8s %-12s %-12s %-12s %-15s %-15s\n", 
    "Periode", "Tgl Awal", "Tgl Akhir", "DPP", "Status Pranota", "Vendor");
echo str_repeat("─", 100) . "\n";

foreach ($tagihan as $t) {
    $status = $t->status_pranota ?? 'belum';
    $dpp = number_format($t->dpp, 0, ',', '.');
    printf("%-8s %-12s %-12s Rp %-10s %-15s %-15s\n", 
        $t->periode, 
        $t->tanggal_awal, 
        $t->tanggal_akhir,
        $dpp,
        $status,
        $t->vendor
    );
}

echo str_repeat("─", 100) . "\n";

// Summary
$belumPranota = $tagihan->where('status_pranota', null)->count();
$sudahPaid = $tagihan->where('status_pranota', 'paid')->count();

echo "\n📊 Summary:\n";
echo "   Total Periode: {$tagihan->count()}\n";
echo "   Belum Masuk Pranota: {$belumPranota}\n";
echo "   Sudah Paid: {$sudahPaid}\n";

echo "\n✅ SELESAI\n";
