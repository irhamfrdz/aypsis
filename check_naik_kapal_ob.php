<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ambil 585 prospek yang baru saja kita set jadi 'aktif' dalam 20 menit terakhir
$updatedProspeks = \App\Models\Prospek::where('status', 'aktif')
    ->where('updated_at', '>=', now()->subMinutes(20))
    ->get();

$totalProspek = $updatedProspeks->count();
$withNaikKapal = 0;
$naikKapalSudahOb = 0;
$naikKapalBelumOb = 0;
$noNaikKapal = 0;

foreach ($updatedProspeks as $prospek) {
    // Cari data naik kapal terkait
    $naikKapals = \App\Models\NaikKapal::where('prospek_id', $prospek->id);

    if (! empty($prospek->nomor_kontainer) && ! empty($prospek->no_voyage)) {
        $naikKapals = $naikKapals->orWhere(function ($q) use ($prospek) {
            $q->where('nomor_kontainer', $prospek->nomor_kontainer)
                ->where('no_voyage', $prospek->no_voyage);
        });
    }

    $naikKapals = $naikKapals->get();

    if ($naikKapals->count() > 0) {
        $withNaikKapal++;
        $isAnyOb = false;
        foreach ($naikKapals as $nk) {
            if ($nk->sudah_ob) {
                $isAnyOb = true;
            }
        }

        if ($isAnyOb) {
            $naikKapalSudahOb++;
        } else {
            $naikKapalBelumOb++;
        }
    } else {
        $noNaikKapal++;
    }
}

echo "Hasil Pengecekan Langsung ke Tabel Naik Kapal:\n";
echo "----------------------------------------------\n";
echo "Total Prospek (Aktif) yang dicek : $totalProspek data\n\n";
echo "1. Memiliki data di tabel naik_kapal : $withNaikKapal data\n";
echo "     -> Dari $withNaikKapal data tersebut, yang sudah_ob = true (Sudah OB) : $naikKapalSudahOb data\n";
echo "     -> Dari $withNaikKapal data tersebut, yang sudah_ob = false (Belum OB) : $naikKapalBelumOb data\n\n";
echo "2. TIDAK ada di tabel naik_kapal sama sekali : $noNaikKapal data\n";
