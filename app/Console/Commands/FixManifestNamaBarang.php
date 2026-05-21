<?php

namespace App\Console\Commands;

use App\Models\Manifest;
use Illuminate\Console\Command;

class FixManifestNamaBarang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifest:fix-nama-barang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki nama_barang pada semua data manifest lama yang salah menggunakan jenis_barang (SNACK, dsb)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai perbaikan nama barang pada tabel Manifest...');

        $manifests = Manifest::whereNotNull('prospek_id')->get();
        $updatedCount = 0;

        $this->output->progressStart($manifests->count());

        foreach ($manifests as $manifest) {
            $this->output->progressAdvance();

            $prospek = $manifest->prospek;
            if ($prospek && $prospek->tandaTerima) {
                $tt = $prospek->tandaTerima;

                $itemNames = [];
                if (! empty($tt->dimensi_items) && is_array($tt->dimensi_items)) {
                    foreach ($tt->dimensi_items as $item) {
                        if (! empty($item['nama_barang'])) {
                            $itemNames[] = $item['nama_barang'];
                        }
                    }
                } elseif (! empty($tt->dimensi_details) && is_array($tt->dimensi_details)) {
                    foreach ($tt->dimensi_details as $item) {
                        if (! empty($item['nama_barang'])) {
                            $itemNames[] = $item['nama_barang'];
                        }
                    }
                } elseif (! empty($tt->nama_barang)) {
                    if (is_array($tt->nama_barang)) {
                        $itemNames = $tt->nama_barang;
                    } elseif (is_string($tt->nama_barang) && $tt->nama_barang !== 'null') {
                        $itemNames[] = $tt->nama_barang;
                    }
                }

                if (! empty($itemNames)) {
                    $newName = implode(', ', $itemNames);

                    // Cek jika berbeda dengan yang ada sekarang
                    if ($manifest->nama_barang !== $newName && ! empty($newName)) {
                        $manifest->nama_barang = $newName;
                        // Update nomor_tanda_terima juga sekalian kalau kosong
                        if (empty($manifest->nomor_tanda_terima)) {
                            $manifest->nomor_tanda_terima = $tt->nomor_tanda_terima ?? $tt->no_surat_jalan;
                        }
                        $manifest->save();
                        $updatedCount++;
                    }
                }
            }
        }

        $this->output->progressFinish();
        $this->info("Berhasil memperbarui {$updatedCount} data manifest!");
    }
}
