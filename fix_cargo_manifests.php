<?php

use Illuminate\Support\Facades\Log;

/**
 * Script untuk memperbaiki data `satuan` pada tabel `manifests`
 * khusus untuk Cargo (Tanda Terima Tanpa Surat Jalan) yang nilai satuannya kosong.
 * 
 * Cara menjalankan di production:
 * 1. Pull script ini ke server production (git pull)
 * 2. Jalankan perintah berikut di dalam folder project:
 *    php artisan tinker fix_cargo_manifests.php
 */

echo "Memulai perbaikan data satuan manifest Cargo...\n";
Log::info('Memulai perbaikan data satuan manifest Cargo...');

$manifests = App\Models\Manifest::where('tipe_kontainer', 'like', 'cargo')->whereNull('satuan')->get();
$count = 0;

foreach ($manifests as $m) {
    if ($m->prospek && $m->prospek->keterangan) {
        // Ambil nomor Tanda Terima Tanpa Surat Jalan dari keterangan
        if (preg_match('/Tanda Terima Tanpa Surat Jalan:\s*([^|]+)/', $m->prospek->keterangan, $matches)) {
            $nomorTt = trim($matches[1]);
            $tttsj = \App\Models\TandaTerimaTanpaSuratJalan::where('no_tanda_terima', $nomorTt)->first();
            
            if ($tttsj && $tttsj->satuan_barang) {
                $m->satuan = $tttsj->satuan_barang;
                
                // Sekalian perbaiki nomor_tanda_terima jika masih kosong
                if (empty($m->nomor_tanda_terima)) {
                    $m->nomor_tanda_terima = $tttsj->no_tanda_terima;
                }
                
                $m->save();
                $count++;
                
                echo "-> Berhasil update Manifest ID {$m->id} (TT: {$nomorTt}) dengan satuan: {$tttsj->satuan_barang}\n";
            }
        }
    }
}

$message = "Selesai! Berhasil memperbarui {$count} manifest cargo.";
echo "\n{$message}\n";
Log::info($message);

?>
