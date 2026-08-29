<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = \Carbon\Carbon::parse('2026-08-22')->startOfDay();
$endDate = \Carbon\Carbon::parse('2026-08-28')->endOfDay();

$qBongkaran = \App\Models\SuratJalanBongkaran::where(function ($q) {
    $q->where('rit', 'menggunakan_rit')
        ->orWhereNull('rit');
})
->where(function ($q) {
    $q->whereNotNull('tanggal_checkpoint')
        ->orWhereHas('tandaTerima');
})
->where(function ($q) use ($startDate, $endDate) {
    $q->where(function ($subQ) use ($startDate, $endDate) {
        $subQ->whereHas('tandaTerima', function ($ttQuery) use ($startDate, $endDate) {
            $ttQuery->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
        });
    })
    ->orWhere(function ($subQ) use ($startDate, $endDate) {
        $subQ->whereNotNull('tanggal_checkpoint')
            ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
            ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
    });
});

echo "Bongkaran count: " . $qBongkaran->count() . "\n";
