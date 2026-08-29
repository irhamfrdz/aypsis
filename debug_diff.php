<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = \Carbon\Carbon::parse('2026-08-22')->startOfDay();
$endDate = \Carbon\Carbon::parse('2026-08-28')->endOfDay();

$qReport = \App\Models\SuratJalan::where('rit', 'menggunakan_rit')
    ->where(function ($q) {
        $q->whereNotNull('tanggal_checkpoint')
            ->orWhereHas('tandaTerima')
            ->orWhere(function ($subQ) {
                $subQ->where('kegiatan', 'bongkaran')->whereNotNull('tanggal_tanda_terima');
            })
            ->orWhere('status', 'approved');
    })
    ->where(function ($q) use ($startDate, $endDate) {
        $q->where(function ($subQ) use ($startDate, $endDate) {
            $subQ->whereHas('tandaTerima', function ($ttQuery) use ($startDate, $endDate) {
                $ttQuery->where(\DB::raw('DATE(tanggal)'), '>=', $startDate->toDateString())
                    ->where(\DB::raw('DATE(tanggal)'), '<=', $endDate->toDateString());
            });
        })
        ->orWhere(function ($subQ) use ($startDate, $endDate) {
            $subQ->where('kegiatan', 'bongkaran')
                ->whereNotNull('tanggal_tanda_terima')
                ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
        })
        ->orWhere(function ($subQ) use ($startDate, $endDate) {
            $subQ->whereNotNull('tanggal_checkpoint')
                ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
        })
        ->orWhere(function ($subQ) use ($startDate, $endDate) {
            $subQ->where('status', 'approved')
                ->where(\DB::raw('DATE(tanggal_surat_jalan)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal_surat_jalan)'), '<=', $endDate->toDateString());
        });
    });

$reportIds = $qReport->pluck('id')->toArray();
echo "qReport count: " . $qReport->count() . "\n";
echo "qReport unique IDs: " . count(array_unique($reportIds)) . "\n";

$qPranota = \App\Models\SuratJalan::with(['tandaTerima', 'approvals'])->where(function ($q) {
    $q->where('status', 'approved')
        ->orWhere('status', 'sudah_checkpoint')
        ->orWhere('status', 'active')
        ->orWhereNotNull('tanggal_checkpoint')
        ->orWhereHas('tandaTerima')
        ->orWhereHas('approvals', function ($sub) {
            $sub->where('status', 'approved');
        });
})
->where('rit', 'menggunakan_rit')
->where('status_pembayaran_uang_rit', \App\Models\SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR)
->whereNotIn('id', function ($query) {
    $query->select('surat_jalan_id')
        ->from('pranota_uang_rits')
        ->whereNotNull('surat_jalan_id')
        ->whereNotIn('status', ['cancelled']);
})
->where(function ($q) {
    $q->whereNotNull('tanggal_checkpoint')
        ->orWhereHas('tandaTerima')
        ->orWhere(function ($subQ) {
            $subQ->where('kegiatan', 'bongkaran')->whereNotNull('tanggal_tanda_terima');
        })
        ->orWhere('status', 'approved');
})
->where(function ($q) use ($startDate, $endDate) {
    $q->where(function ($subQ) use ($startDate, $endDate) {
        $subQ->whereHas('tandaTerima', function ($ttQuery) use ($startDate, $endDate) {
            $ttQuery->where(\DB::raw('DATE(tanggal)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal)'), '<=', $endDate->toDateString());
        });
    })
    ->orWhere(function ($subQ) use ($startDate, $endDate) {
        $subQ->where('kegiatan', 'bongkaran')
            ->whereNotNull('tanggal_tanda_terima')
            ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
            ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
    })
    ->orWhere(function ($subQ) use ($startDate, $endDate) {
        $subQ->whereNotNull('tanggal_checkpoint')
            ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
            ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
    })
    ->orWhere(function ($subQ) use ($startDate, $endDate) {
        $subQ->where('status', 'approved')
            ->where(\DB::raw('DATE(tanggal_surat_jalan)'), '>=', $startDate->toDateString())
            ->where(\DB::raw('DATE(tanggal_surat_jalan)'), '<=', $endDate->toDateString());
    });
});

$pranotaIds = $qPranota->pluck('id')->toArray();
echo "qPranota count: " . $qPranota->count() . "\n";
echo "qPranota unique IDs: " . count(array_unique($pranotaIds)) . "\n";

$diff1 = array_diff($reportIds, $pranotaIds);
echo "In Report but NOT in Pranota: " . json_encode(array_values($diff1)) . "\n";

$diff2 = array_diff($pranotaIds, $reportIds);
echo "In Pranota but NOT in Report: " . json_encode(array_values($diff2)) . "\n";

foreach($diff1 as $id) {
    $sj = \App\Models\SuratJalan::find($id);
    echo "ID: {$sj->id}, No SJ: {$sj->nomor_surat_jalan}, Status Uang Rit: {$sj->status_pembayaran_uang_rit}\n";
    
    $inPranota = \App\Models\PranotaUangRit::where('surat_jalan_id', $id)->whereNotIn('status', ['cancelled'])->exists();
    echo "In Pranota (DB): " . ($inPranota ? 'Yes' : 'No') . "\n";
}

foreach($diff2 as $id) {
    $sj = \App\Models\SuratJalan::find($id);
    echo "ID: {$sj->id}, No SJ: {$sj->nomor_surat_jalan}, Status Uang Rit: {$sj->status_pembayaran_uang_rit}\n";
}
