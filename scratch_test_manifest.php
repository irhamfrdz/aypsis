<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

$query = Manifest::where(function ($q) {
    $q->whereNotNull('prospek_id')
        ->orWhere(function ($sq) {
            $sq->whereNotNull('nomor_tanda_terima')
                ->where('nomor_tanda_terima', '!=', '');
        })
        ->orWhereHas('suratJalanBongkaran');
})->with(['prospek.tandaTerima', 'suratJalanBongkaran']);

$manifests = $query->get();
echo 'Found '.$manifests->count()." manifests.\n";

$saved = 0;
$errors = 0;

foreach ($manifests as $manifest) {
    try {
        $tandaTerima = $manifest->prospek ? $manifest->prospek->tandaTerima : null;
        $tttsj = null;
        $tandaTerimaLcl = null;
        $sjBongkaran = $manifest->suratJalanBongkaran;

        if (! $tandaTerima && $manifest->prospek && $manifest->prospek->keterangan) {
            if (preg_match('/Tanda Terima Tanpa Surat Jalan:\s*([^|]+)/', $manifest->prospek->keterangan, $matches)) {
                $noTttsj = trim($matches[1]);
                if (! empty($noTttsj)) {
                    $tttsj = \App\Models\TandaTerimaTanpaSuratJalan::where('no_tanda_terima', $noTttsj)->first();
                }
            }
        }

        if (! $tandaTerima && ! $tttsj && ! $sjBongkaran && $manifest->nomor_tanda_terima) {
            $tandaTerimaLcl = \App\Models\TandaTerimaLcl::where('nomor_tanda_terima', $manifest->nomor_tanda_terima)->first();
            if (! $tandaTerimaLcl && strpos($manifest->nomor_tanda_terima, ' ') !== false) {
                $parts = explode(' ', $manifest->nomor_tanda_terima);
                $lastPart = end($parts);
                $tandaTerimaLcl = \App\Models\TandaTerimaLcl::where('nomor_tanda_terima', trim($lastPart))->first();
            }
            if (! $tandaTerimaLcl) {
                $tandaTerimaLcl = DB::table('tanda_terima_lcl')->where('nomor_tanda_terima', $manifest->nomor_tanda_terima)->first();
                if (! $tandaTerimaLcl && strpos($manifest->nomor_tanda_terima, ' ') !== false) {
                    $parts = explode(' ', $manifest->nomor_tanda_terima);
                    $lastPart = end($parts);
                    $tandaTerimaLcl = DB::table('tanda_terima_lcl')->where('nomor_tanda_terima', trim($lastPart))->first();
                }
            }
        }

        if (! $tandaTerima && ! $tttsj && ! $tandaTerimaLcl && ! $sjBongkaran) {
            continue;
        }

        $penerimaName = null;
        $alamatPenerima = null;
        $pengirimName = null;
        $alamatPengirim = null;
        $nomorTandaTerima = null;
        $sealTandaTerima = null;
        $kuantitasVal = null;
        $satuanVal = null;
        $volumeVal = null;
        $tonnageVal = null;
        $termVal = null;

        if ($tandaTerima) {
            $penerimaName = $tandaTerima->penerima;
            $alamatPenerima = $tandaTerima->alamat_penerima;
            $pengirimName = $tandaTerima->pengirim;
            $alamatPengirim = $tandaTerima->alamat_pengirim ?? ($tandaTerima->suratJalan ? $tandaTerima->suratJalan->alamat : null);
            $nomorTandaTerima = $tandaTerima->no_tanda_terima;
            $sealTandaTerima = $tandaTerima->no_seal;
            $kuantitasVal = $tandaTerima->jumlah;
            $satuanVal = $tandaTerima->satuan;
            $volumeVal = $tandaTerima->meter_kubik;
            $tonnageVal = $tandaTerima->tonase;
            $termVal = $tandaTerima->term;
        } elseif ($tttsj) {
            $penerimaName = $tttsj->penerima;
            $alamatPenerima = $tttsj->alamat_penerima;
            $pengirimName = $tttsj->pengirim;
            $alamatPengirim = $tttsj->alamat_pengirim;
            $nomorTandaTerima = $tttsj->no_tanda_terima;
            $sealTandaTerima = $tttsj->no_seal;
            $kuantitasVal = $tttsj->jumlah_barang;
            $satuanVal = $tttsj->satuan_barang;
            $volumeVal = $tttsj->meter_kubik;
            $tonnageVal = $tttsj->tonase;
            $termVal = $tttsj->term ? $tttsj->term->kode : null;
        } elseif ($tandaTerimaLcl) {
            $isModel = $tandaTerimaLcl instanceof \Illuminate\Database\Eloquent\Model;
            $penerimaName = $isModel ? $tandaTerimaLcl->nama_penerima : ($tandaTerimaLcl->nama_penerima ?? null);
            $alamatPenerima = $isModel ? $tandaTerimaLcl->alamat_penerima : ($tandaTerimaLcl->alamat_penerima ?? null);
            $pengirimName = $isModel ? $tandaTerimaLcl->nama_pengirim : ($tandaTerimaLcl->nama_pengirim ?? null);
            $alamatPengirim = $isModel ? $tandaTerimaLcl->alamat_pengirim : ($tandaTerimaLcl->alamat_pengirim ?? null);
            $nomorTandaTerima = $isModel ? $tandaTerimaLcl->nomor_tanda_terima : ($tandaTerimaLcl->nomor_tanda_terima ?? null);

            if ($isModel) {
                $kuantitasVal = $tandaTerimaLcl->total_koli;
                $volumeVal = $tandaTerimaLcl->total_volume;
                $tonnageVal = $tandaTerimaLcl->total_weight;
                $items = $tandaTerimaLcl->items;
                if ($items->count() > 0) {
                    $units = $items->pluck('satuan')->unique()->filter();
                    $satuanVal = $units->count() === 1 ? $units->first() : 'PKGS';
                }
            }
        } elseif ($sjBongkaran) {
            $penerimaName = $sjBongkaran->penerima;
            $alamatPenerima = $sjBongkaran->tujuan_alamat;
            $pengirimName = $sjBongkaran->pengirim;
            $nomorTandaTerima = $manifest->nomor_tanda_terima;
            $sealTandaTerima = $sjBongkaran->no_seal;
            $termVal = $sjBongkaran->term;
        }

        $hasChanges = false;
        if ($penerimaName && $manifest->penerima != $penerimaName) {
            $manifest->penerima = $penerimaName;
            $hasChanges = true;
        }
        if ($alamatPenerima && $manifest->alamat_penerima != $alamatPenerima) {
            $manifest->alamat_penerima = $alamatPenerima;
            $hasChanges = true;
        }
        if ($pengirimName && $manifest->pengirim != $pengirimName) {
            $manifest->pengirim = $pengirimName;
            $hasChanges = true;
        }
        if ($alamatPengirim && $manifest->alamat_pengirim != $alamatPengirim) {
            $manifest->alamat_pengirim = $alamatPengirim;
            $hasChanges = true;
        }
        if ($nomorTandaTerima && $manifest->nomor_tanda_terima != $nomorTandaTerima) {
            $manifest->nomor_tanda_terima = $nomorTandaTerima;
            $hasChanges = true;
        }
        if ($sealTandaTerima !== null && $manifest->no_seal != $sealTandaTerima) {
            $manifest->no_seal = $sealTandaTerima;
            $hasChanges = true;
        }
        if ($termVal !== null && $manifest->term != $termVal) {
            $manifest->term = $termVal;
            $hasChanges = true;
        }
        if ($kuantitasVal !== null && $manifest->kuantitas != $kuantitasVal) {
            $manifest->kuantitas = $kuantitasVal;
            $hasChanges = true;
        }
        if ($satuanVal !== null && $manifest->satuan != $satuanVal) {
            $manifest->satuan = $satuanVal;
            $hasChanges = true;
        }
        if ($volumeVal !== null && $manifest->volume != $volumeVal) {
            $manifest->volume = $volumeVal;
            $hasChanges = true;
        }
        if ($tonnageVal !== null && $manifest->tonnage != $tonnageVal) {
            $manifest->tonnage = $tonnageVal;
            $hasChanges = true;
        }
        if ($alamatPenerima && $manifest->alamat_pengiriman != $alamatPenerima) {
            $manifest->alamat_pengiriman = $alamatPenerima;
            $hasChanges = true;
        }

        if ($hasChanges) {
            $manifest->save();
            $saved++;
        }
    } catch (\Exception $e) {
        $errors++;
        echo "Error on manifest ID {$manifest->id}: ".$e->getMessage()."\n";
        if ($errors >= 5) {
            echo "Too many errors, aborting diagnostics.\n";
            break;
        }
    }
}

echo "Done! Total updated and saved: $saved. Errors encountered: $errors.\n";
