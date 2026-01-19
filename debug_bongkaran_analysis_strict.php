<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = '2026-01-11';
$endDate = '2026-01-17';

// 1. Get ALL Bongkaran items that appear in NEW STRICT Report (TT or Checkpoint)
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
        });
    })->get();

echo "Counts under NEW Logic (No SJ Date):\n";
echo "Valid Bongkaran Count: " . $reportBongkaran->count() . "\n";
