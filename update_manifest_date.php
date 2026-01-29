<?php

use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    $voyage = 'ST02BJ26';
    $tanggalBaru = '2026-01-26';

    $count = Manifest::where('no_voyage', $voyage)->count();

    if ($count > 0) {
        Manifest::where('no_voyage', $voyage)->update([
            'tanggal_berangkat' => $tanggalBaru
        ]);
        
        DB::commit();
        echo "Berhasil memperbarui {$count} data manifest untuk voyage {$voyage} menjadi tanggal {$tanggalBaru}.\n";
    } else {
        echo "Tidak ditemukan data manifest dengan nomor voyage {$voyage}.\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
}
