<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = \Carbon\Carbon::parse('2026-08-22')->startOfDay();
$endDate = \Carbon\Carbon::parse('2026-08-28')->endOfDay();

// =================== REPORT RIT ===================
$qReportSJ = \App\Models\SuratJalan::where('rit', 'menggunakan_rit')
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

$qReportBongkaran = \App\Models\SuratJalanBongkaran::where(function ($q) {
    $q->where('rit', 'menggunakan_rit')->orWhereNull('rit');
})->where(function ($q) {
    $q->whereNotNull('tanggal_checkpoint')->orWhereHas('tandaTerima');
})->where(function ($q) use ($startDate, $endDate) {
    $q->where(function ($subQ) use ($startDate, $endDate) {
        $subQ->whereHas('tandaTerima', function ($ttQuery) use ($startDate, $endDate) {
            $ttQuery->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
        });
    })->orWhere(function ($subQ) use ($startDate, $endDate) {
        $subQ->whereNotNull('tanggal_checkpoint')
            ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
            ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
    });
});

$reportSJ = $qReportSJ->pluck('id')->toArray();
$reportBongkaran = $qReportBongkaran->pluck('id')->toArray();

// =================== PRANOTA UANG RIT ===================
$qPranotaSJ = \App\Models\SuratJalan::with(['tandaTerima', 'approvals'])->where(function ($q) {
    $q->where('status', 'approved')->orWhere('status', 'sudah_checkpoint')->orWhere('status', 'active')
        ->orWhereNotNull('tanggal_checkpoint')->orWhereHas('tandaTerima')->orWhereHas('approvals', function ($sub) {
            $sub->where('status', 'approved');
        });
})->where('rit', 'menggunakan_rit')->where('status_pembayaran_uang_rit', \App\Models\SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR)
->whereNotIn('id', function ($query) {
    $query->select('surat_jalan_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_id')->whereNotIn('status', ['cancelled']);
})->where(function ($q) {
    $q->whereNotNull('tanggal_checkpoint')->orWhereHas('tandaTerima')->orWhere(function ($subQ) {
        $subQ->where('kegiatan', 'bongkaran')->whereNotNull('tanggal_tanda_terima');
    })->orWhere('status', 'approved');
})->where(function ($q) use ($startDate, $endDate) {
    $q->where(function ($subQ) use ($startDate, $endDate) {
        $subQ->whereHas('tandaTerima', function ($ttQuery) use ($startDate, $endDate) {
            $ttQuery->where(\DB::raw('DATE(tanggal)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal)'), '<=', $endDate->toDateString());
        });
    })->orWhere(function ($subQ) use ($startDate, $endDate) {
        $subQ->where('kegiatan', 'bongkaran')
            ->whereNotNull('tanggal_tanda_terima')
            ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
            ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
    })->orWhere(function ($subQ) use ($startDate, $endDate) {
        $subQ->whereNotNull('tanggal_checkpoint')
            ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
            ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
    })->orWhere(function ($subQ) use ($startDate, $endDate) {
        $subQ->where('status', 'approved')
            ->where(\DB::raw('DATE(tanggal_surat_jalan)'), '>=', $startDate->toDateString())
            ->where(\DB::raw('DATE(tanggal_surat_jalan)'), '<=', $endDate->toDateString());
    });
});

$qPranotaBongkaran = \App\Models\SuratJalanBongkaran::with(['tandaTerima'])
    ->where(function ($q) use ($startDate, $endDate) {
        $q->whereHas('tandaTerima', function ($query) use ($startDate, $endDate) {
            $query->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
        })->orWhere(function ($subQ) use ($startDate, $endDate) {
            $subQ->whereNotNull('tanggal_checkpoint')
                ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
                ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
        });
    })->where(function ($q) {
        $q->where('rit', 'menggunakan_rit')->orWhereNull('rit');
    })->where(function ($q) {
        $q->where('status_pembayaran_uang_rit', 'belum_bayar')->orWhereNull('status_pembayaran_uang_rit');
    })->whereNotIn('id', function ($query) {
        $query->select('surat_jalan_bongkaran_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_bongkaran_id')->whereNotIn('status', ['cancelled']);
    });

$pranotaSJ = $qPranotaSJ->pluck('id')->toArray();
$pranotaBongkaran = $qPranotaBongkaran->pluck('id')->toArray();

echo "Report SJ: " . count($reportSJ) . ", Report Bongkaran: " . count($reportBongkaran) . "\n";
echo "Pranota SJ: " . count($pranotaSJ) . ", Pranota Bongkaran: " . count($pranotaBongkaran) . "\n";

$diffSJ = array_diff($reportSJ, $pranotaSJ);
if(count($diffSJ) > 0) {
    echo "--- MISSING SURAT JALAN ---\n";
    foreach($diffSJ as $id) {
        $sj = \App\Models\SuratJalan::find($id);
        echo "ID: {$id}, No: {$sj->no_surat_jalan}, Status Pembayaran: {$sj->status_pembayaran_uang_rit}\n";
    }
}

$diffBongkaran = array_diff($reportBongkaran, $pranotaBongkaran);
if(count($diffBongkaran) > 0) {
    echo "--- MISSING BONGKARAN ---\n";
    foreach($diffBongkaran as $id) {
        $sjb = \App\Models\SuratJalanBongkaran::find($id);
        echo "ID: {$id}, No: {$sjb->nomor_surat_jalan}, Status Pembayaran: {$sjb->status_pembayaran_uang_rit}\n";
    }
}
