<?php

use App\Models\PranotaUangRit;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Illuminate\Support\Facades\DB;

$startDate = '2026-05-02';
$endDate = '2026-05-08';

DB::beginTransaction();

try {
    // 1. Cari semua Surat Jalan (Reguler) dalam range tanggal tersebut
    $sjIds = SuratJalan::where(function ($q) use ($startDate, $endDate) {
        $q->whereBetween(DB::raw('DATE(tanggal_checkpoint)'), [$startDate, $endDate])
            ->orWhereHas('tandaTerima', function ($tt) use ($startDate, $endDate) {
                $tt->whereBetween(DB::raw('DATE(tanggal)'), [$startDate, $endDate]);
            });
    })
        ->pluck('id');

    // 2. Cari semua Surat Jalan (Bongkaran) dalam range tanggal tersebut
    $sjbIds = SuratJalanBongkaran::where(function ($q) use ($startDate, $endDate) {
        $q->whereBetween(DB::raw('DATE(tanggal_checkpoint)'), [$startDate, $endDate])
            ->orWhereHas('tandaTerima', function ($tt) use ($startDate, $endDate) {
                $tt->whereBetween(DB::raw('DATE(tanggal_tanda_terima)'), [$startDate, $endDate]);
            });
    })
        ->pluck('id');

    // 3. Cari Pranota yang mereferensikan Surat Jalan tersebut
    $pranotas = PranotaUangRit::whereIn('surat_jalan_id', $sjIds)
        ->orWhereIn('surat_jalan_bongkaran_id', $sjbIds)
        ->get();

    echo 'Ditemukan '.$pranotas->count()." Pranota yang berisi Surat Jalan dalam range tersebut.\n";

    foreach ($pranotas as $pranota) {
        $noPranota = $pranota->no_pranota;

        // Reset status SJ di dalam Pranota ini
        $noSuratJalanList = explode(', ', $pranota->no_surat_jalan);
        $cleanNoList = array_map(function ($val) {
            return str_replace(' (Bongkaran)', '', trim($val));
        }, $noSuratJalanList);

        SuratJalan::whereIn('no_surat_jalan', $cleanNoList)->update(['status_pembayaran_uang_rit' => 'belum_dibayar']);
        SuratJalanBongkaran::whereIn('nomor_surat_jalan', $cleanNoList)->update(['status_pembayaran_uang_rit' => 'belum_bayar']);

        // Hapus data detail dan pranota
        DB::table('pranota_uang_rit_supir_details')->where('no_pranota', $noPranota)->delete();
        $pranota->delete();

        echo "Berhasil mereset Pranota: $noPranota\n";
    }

    // 4. Force reset SEMUA Surat Jalan di range tersebut agar statusnya 'belum_dibayar'
    // (Jaga-jaga jika ada yang statusnya 'dibayar' tapi pranotanya sudah terhapus manual)
    SuratJalan::whereIn('id', $sjIds)->update(['status_pembayaran_uang_rit' => 'belum_dibayar']);
    SuratJalanBongkaran::whereIn('id', $sjbIds)->update(['status_pembayaran_uang_rit' => 'belum_bayar']);

    DB::commit();
    echo "\nSelesai! Semua Surat Jalan pada range $startDate s/d $endDate sekarang berstatus 'Belum Dibayar' dan siap diproses kembali.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo 'Terjadi kesalahan: '.$e->getMessage()."\n";
}
