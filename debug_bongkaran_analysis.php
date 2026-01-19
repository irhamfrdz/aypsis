<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = '2026-01-11';
$endDate = '2026-01-17';

// 1. Get ALL Bongkaran items that appear in Report
$reportBongkaran = \App\Models\SuratJalanBongkaran::where(function($q) {
        $q->where('rit', 'menggunakan_rit')->orWhereNull('rit');
    })
    ->where(function($q) use ($startDate, $endDate) {
        $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
            $tt->whereBetween(\DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
             $subQ->whereNotNull('tanggal_checkpoint')
                  ->whereBetween(\DB::raw('DATE(tanggal_checkpoint)'), [$startDate, $endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
             $subQ->whereNotNull('tanggal_surat_jalan')
                  ->whereBetween(\DB::raw('DATE(tanggal_surat_jalan)'), [$startDate, $endDate]);
        });
    })->get();

echo "Total Bongkaran di Report: " . $reportBongkaran->count() . "\n";

// 2. Check which ones would fail the strict Pranota filter (Only Tanda Terima)
$missingBecauseOfFilter = [];
$missingBecauseOfStatus = [];
$readyToPay = [];

foreach($reportBongkaran as $bg) {
    // Check if it has Tanda Terima in range
    $hasTT = false;
    if ($bg->tandaTerima) {
        $ttDate = substr($bg->tandaTerima->tanggal_tanda_terima, 0, 10);
        if ($ttDate >= $startDate && $ttDate <= $endDate) {
            $hasTT = true;
        }
    }

    // Check payment status
    $isPaid = ($bg->status_pembayaran_uang_rit != 'belum_bayar' && $bg->status_pembayaran_uang_rit != null);
    
    // Check if in existing pranota
    $inPranota = \DB::table('pranota_uang_rits')->where('surat_jalan_bongkaran_id', $bg->id)->where('status', '!=', 'cancelled')->exists();

    $statusInfo = "[ID: {$bg->id} | No: {$bg->nomor_surat_jalan}] HasTT: " . ($hasTT?'YES':'NO') . " | Paid: " . ($isPaid?'YES':'NO') . " | InPranota: " . ($inPranota?'YES':'NO');

    if (!$hasTT) {
        $missingBecauseOfFilter[] = $statusInfo;
    } elseif ($isPaid || $inPranota) {
        $missingBecauseOfStatus[] = $statusInfo;
    } else {
        $readyToPay[] = $statusInfo;
    }
}

echo "\n--- ANALYSIS OF " . $reportBongkaran->count() . " BONGKARAN ITEMS ---\n";
echo "1. Missing because strict filter (No Tanda Terima in range): " . count($missingBecauseOfFilter) . "\n";
foreach($missingBecauseOfFilter as $msg) echo "   $msg\n";

echo "\n2. Missing because already Paid/In Process: " . count($missingBecauseOfStatus) . "\n";
foreach($missingBecauseOfStatus as $msg) echo "   $msg\n";

echo "\n3. Showing in Pranota (Ready): " . count($readyToPay) . "\n";
foreach($readyToPay as $msg) echo "   $msg\n";
