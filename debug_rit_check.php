<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = '2026-01-11';
$endDate = '2026-01-17';

// --- REPORT RIT LOGIC ---
$reportSJ = \App\Models\SuratJalan::where('rit', 'menggunakan_rit') // Only Regular SJ
    ->where(function($q) use ($startDate, $endDate) {
        $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
            $tt->whereBetween(\DB::raw('DATE(tanggal)'), [$startDate, $endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
             $subQ->where('kegiatan', 'bongkaran')
                  ->whereNotNull('tanggal_tanda_terima')
                  ->whereBetween(\DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
             $subQ->whereNotNull('tanggal_checkpoint')
                  ->whereBetween(\DB::raw('DATE(tanggal_checkpoint)'), [$startDate, $endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
             $subQ->where('status', 'approved')
                  ->whereBetween(\DB::raw('DATE(tanggal_surat_jalan)'), [$startDate, $endDate]);
        });
    })->count();

$reportBongkaran = \App\Models\SuratJalanBongkaran::where(function($q) {
        $q->where('rit', 'menggunakan_rit')
          ->orWhereNull('rit');
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
    })->count();

$totalReport = $reportSJ + $reportBongkaran;

// --- PRANOTA (CREATE) LOGIC ---
$pranotaSJ = \App\Models\SuratJalan::where('rit', 'menggunakan_rit')
    ->where(function($q) use ($startDate, $endDate) {
        $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
            $tt->whereBetween(\DB::raw('DATE(tanggal)'), [$startDate, $endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
             $subQ->where('kegiatan', 'bongkaran')
                  ->whereNotNull('tanggal_tanda_terima')
                  ->whereBetween(\DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
             $subQ->whereNotNull('tanggal_checkpoint')
                  ->whereBetween(\DB::raw('DATE(tanggal_checkpoint)'), [$startDate, $endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
             $subQ->where('status', 'approved')
                  ->whereBetween(\DB::raw('DATE(tanggal_surat_jalan)'), [$startDate, $endDate]);
        });
    })
    ->where('status_pembayaran_uang_rit', 'belum_dibayar') // FILTER UTAMA
    ->whereNotIn('id', function($q) {
        $q->select('surat_jalan_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_id')->where('status', '!=', 'cancelled');
    })->count();

$pranotaBongkaran = \App\Models\SuratJalanBongkaran::where(function($q) {
        $q->where('rit', 'menggunakan_rit')
          ->orWhereNull('rit');
    })
    ->where(function($q) use ($startDate, $endDate) {
        $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
            $tt->whereBetween(\DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]);
        }); // Note: Pranota Create controller seems stricter on date filter for bongkaran (only tanda terima?) - checking code...
        // Ah, PranotaUangRitController line 303 uses only tandaTerima date for Bongkaran!
        // "whereHas('tandaTerima', ...)"
    })
    ->where(function($q) {
        $q->where('status_pembayaran_uang_rit', 'belum_bayar') // Note: 'belum_bayar' vs 'belum_dibayar' - check enum/string
          ->orWhereNull('status_pembayaran_uang_rit');
    })
    ->whereNotIn('id', function($q) {
        $q->select('surat_jalan_bongkaran_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_bongkaran_id')->where('status', '!=', 'cancelled');
    })->count();
    
$totalPranota = $pranotaSJ + $pranotaBongkaran;

// --- OUTPUT RESULTS ---
echo "\n====================== DIAGNOSTIC RESULT ======================\n";
echo "Date Range: $startDate - $endDate\n\n";

echo "1. REPORT RIT (Total Activity):\n";
echo "   - Regular SJ  : $reportSJ\n";
echo "   - Bongkaran   : $reportBongkaran\n";
echo "   - TOTAL REPORT: $totalReport\n\n";

echo "2. PRANOTA UANG RIT (Ready to Pay):\n";
echo "   - Regular SJ  : $pranotaSJ\n";
echo "   - Bongkaran   : $pranotaBongkaran\n";
echo "   - TOTAL PRANOTA: $totalPranota\n\n";

echo "3. DIFFERENCE (Paid / In Process):\n";
echo "   - Difference  : " . ($totalReport - $totalPranota) . "\n\n";

echo "   Possible causes for difference:\n";
echo "   - Status Pembayaran != 'belum_dibayar'\n";
echo "   - Existing Active Pranota (Draft/Submitted)\n";
echo "   - Bungkeran date filter logic difference (Report is looser, Pranota is stricter)\n";
