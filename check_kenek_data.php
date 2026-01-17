<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\TandaTerima;

$startDate = '2026-01-11';
$endDate = '2026-01-17';

echo "=== ANALISIS SURAT JALAN UNTUK PRANOTA UANG RIT KENEK ===\n";
echo "Rentang tanggal: $startDate - $endDate\n\n";

// 1. Total surat jalan dengan kenek
$totalSjKenek = SuratJalan::whereNotNull('kenek')->where('kenek', '!=', '')->count();
echo "1. Total Surat Jalan dengan kenek: $totalSjKenek\n\n";

// 2. Surat jalan dengan kenek yang punya tanda terima
$sjKenekWithTT = SuratJalan::whereNotNull('kenek')
    ->where('kenek', '!=', '')
    ->whereHas('tandaTerima')
    ->count();
echo "2. Surat Jalan dengan kenek & Tanda Terima: $sjKenekWithTT\n\n";

// 3. Surat jalan dengan kenek, tanda terima dalam range
$sjKenekInRange = SuratJalan::whereNotNull('kenek')
    ->where('kenek', '!=', '')
    ->whereHas('tandaTerima', function($q) use ($startDate, $endDate) {
        $q->whereBetween('tanggal', [$startDate, $endDate]);
    })
    ->count();
echo "3. Surat Jalan dengan kenek & TT dalam range ($startDate - $endDate): $sjKenekInRange\n\n";

// 4. Sample surat jalan dengan kenek
echo "4. Sample 10 Surat Jalan dengan kenek:\n";
$samples = SuratJalan::whereNotNull('kenek')
    ->where('kenek', '!=', '')
    ->with('tandaTerima')
    ->orderBy('id', 'desc')
    ->take(10)
    ->get();

foreach ($samples as $s) {
    $ttDate = $s->tandaTerima ? $s->tandaTerima->tanggal : 'NULL';
    echo "   ID: {$s->id} | No: {$s->no_surat_jalan} | Kenek: {$s->kenek} | Status: {$s->status} | TT Date: $ttDate\n";
}

echo "\n";

// 5. Semua tanda terima dalam range
echo "5. Tanda Terima dalam range ($startDate - $endDate):\n";
$ttInRange = TandaTerima::whereBetween('tanggal', [$startDate, $endDate])->get();
echo "   Total: " . $ttInRange->count() . "\n";
foreach ($ttInRange->take(10) as $tt) {
    echo "   ID: {$tt->id} | SJ ID: {$tt->surat_jalan_id} | Tanggal: {$tt->tanggal}\n";
}

echo "\n";

// 6. Check surat jalan bongkaran dengan kenek
$totalSjbKenek = SuratJalanBongkaran::whereNotNull('kenek')->where('kenek', '!=', '')->count();
echo "6. Total Surat Jalan Bongkaran dengan kenek: $totalSjbKenek\n\n";

// 7. Status pembayaran uang rit
echo "7. Status pembayaran uang rit kenek pada surat jalan dengan kenek:\n";
$statusCounts = SuratJalan::whereNotNull('kenek')
    ->where('kenek', '!=', '')
    ->selectRaw('status_pembayaran_uang_rit_kenek, COUNT(*) as count')
    ->groupBy('status_pembayaran_uang_rit_kenek')
    ->get();
foreach ($statusCounts as $sc) {
    echo "   {$sc->status_pembayaran_uang_rit_kenek}: {$sc->count}\n";
}

echo "\n";

// 8. Check if column exists
echo "8. Checking columns in surat_jalans table:\n";
$columns = DB::select("SHOW COLUMNS FROM surat_jalans LIKE '%kenek%'");
foreach ($columns as $col) {
    echo "   Column: {$col->Field}\n";
}

echo "\n=== END ===\n";
