<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = '2026-01-11';
$endDate = '2026-01-17';

$reportSJ = \App\Models\SuratJalan::where('rit', 'menggunakan_rit')->where(function($q) use ($startDate, $endDate) {
        $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) { $tt->whereBetween(\DB::raw('DATE(tanggal)'), [$startDate, $endDate]); })
          ->orWhere(function($subQ) use ($startDate, $endDate) { $subQ->where('kegiatan', 'bongkaran')->whereNotNull('tanggal_tanda_terima')->whereBetween(\DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]); })
          ->orWhere(function($subQ) use ($startDate, $endDate) { $subQ->whereNotNull('tanggal_checkpoint')->whereBetween(\DB::raw('DATE(tanggal_checkpoint)'), [$startDate, $endDate]); })
          ->orWhere(function($subQ) use ($startDate, $endDate) { $subQ->where('status', 'approved')->whereBetween(\DB::raw('DATE(tanggal_surat_jalan)'), [$startDate, $endDate]); });
    })->count();

$reportBongkaran = \App\Models\SuratJalanBongkaran::where(function($q) { $q->where('rit', 'menggunakan_rit')->orWhereNull('rit'); })
    ->where(function($q) use ($startDate, $endDate) {
        $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) { $tt->whereBetween(\DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]); })
          ->orWhere(function($subQ) use ($startDate, $endDate) { $subQ->whereNotNull('tanggal_checkpoint')->whereBetween(\DB::raw('DATE(tanggal_checkpoint)'), [$startDate, $endDate]); })
          ->orWhere(function($subQ) use ($startDate, $endDate) { $subQ->whereNotNull('tanggal_surat_jalan')->whereBetween(\DB::raw('DATE(tanggal_surat_jalan)'), [$startDate, $endDate]); });
    })->count();

$pranotaSJ = \App\Models\SuratJalan::where('rit', 'menggunakan_rit')
    ->where(function($q) use ($startDate, $endDate) {
        $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) { $tt->whereBetween(\DB::raw('DATE(tanggal)'), [$startDate, $endDate]); })
          ->orWhere(function($subQ) use ($startDate, $endDate) { $subQ->where('kegiatan', 'bongkaran')->whereNotNull('tanggal_tanda_terima')->whereBetween(\DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]); })
          ->orWhere(function($subQ) use ($startDate, $endDate) { $subQ->whereNotNull('tanggal_checkpoint')->whereBetween(\DB::raw('DATE(tanggal_checkpoint)'), [$startDate, $endDate]); })
          ->orWhere(function($subQ) use ($startDate, $endDate) { $subQ->where('status', 'approved')->whereBetween(\DB::raw('DATE(tanggal_surat_jalan)'), [$startDate, $endDate]); });
    })
    ->where('status_pembayaran_uang_rit', 'belum_dibayar')
    ->whereNotIn('id', function($q) { $q->select('surat_jalan_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_id')->where('status', '!=', 'cancelled'); })->count();

$pranotaBongkaran = \App\Models\SuratJalanBongkaran::where(function($q) { $q->where('rit', 'menggunakan_rit')->orWhereNull('rit'); })
    ->where(function($q) use ($startDate, $endDate) { $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) { $tt->whereBetween(\DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]); }); })
    ->where(function($q) { $q->where('status_pembayaran_uang_rit', 'belum_bayar')->orWhereNull('status_pembayaran_uang_rit'); })
    ->whereNotIn('id', function($q) { $q->select('surat_jalan_bongkaran_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_bongkaran_id')->where('status', '!=', 'cancelled'); })->count();

echo "REP_SJ:$reportSJ\nREP_BG:$reportBongkaran\nPRA_SJ:$pranotaSJ\nPRA_BG:$pranotaBongkaran\n";
