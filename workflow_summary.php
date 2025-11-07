<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratJalan;
use App\Models\PranotaSuratJalan;

echo "=== STATUS WORKFLOW SUMMARY ===\n\n";

// Check current status distribution
echo "1. Current status distribution:\n";
$statusCounts = SuratJalan::selectRaw('status_pembayaran, COUNT(*) as count')
    ->groupBy('status_pembayaran')
    ->get();

foreach ($statusCounts as $status) {
    echo "   {$status->status_pembayaran}: {$status->count} surat jalan\n";
}

// Check surat jalans with pranota_surat_jalan_id
echo "\n2. Surat jalans linked to pranota:\n";
$linkedSuratJalans = SuratJalan::whereNotNull('pranota_surat_jalan_id')
    ->select('id', 'no_surat_jalan', 'status_pembayaran', 'pranota_surat_jalan_id')
    ->limit(5)
    ->get();

if ($linkedSuratJalans->count() > 0) {
    foreach ($linkedSuratJalans as $sj) {
        echo "   SJ #{$sj->id} ({$sj->no_surat_jalan}): {$sj->status_pembayaran} -> Pranota #{$sj->pranota_surat_jalan_id}\n";
    }
} else {
    echo "   No surat jalans linked to pranota yet.\n";
}

// Check unlinked surat jalans
echo "\n3. Surat jalans NOT linked to pranota:\n";
$unlinkedSuratJalans = SuratJalan::whereNull('pranota_surat_jalan_id')
    ->select('id', 'no_surat_jalan', 'status_pembayaran')
    ->limit(5)
    ->get();

foreach ($unlinkedSuratJalans as $sj) {
    echo "   SJ #{$sj->id} ({$sj->no_surat_jalan}): {$sj->status_pembayaran}\n";
}

echo "\n=== WORKFLOW EXPLANATION ===\n";
echo "✓ New surat jalan → status: 'belum_masuk_pranota'\n";
echo "✓ Surat jalan added to pranota → status: 'belum_dibayar'\n";
echo "✓ Surat jalan removed from pranota → status: 'belum_masuk_pranota'\n";
echo "✓ After payment → status: 'sudah_dibayar'\n";

echo "\n=== COMPLETED ===\n";