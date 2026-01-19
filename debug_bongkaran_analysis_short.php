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
                  ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate, $endDate);
        });
    })->get();

$missingBecauseOfFilter = 0;
$missingBecauseOfStatus = 0;
$readyToPay = 0;

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

    if (!$hasTT) {
        $missingBecauseOfFilter++;
    } elseif ($isPaid || $inPranota) {
        $missingBecauseOfStatus++;
    } else {
        $readyToPay++;
    }
}

echo "FILTER_MISS:$missingBecauseOfFilter\nSTATUS_MISS:$missingBecauseOfStatus\nREADY:$readyToPay\n";
