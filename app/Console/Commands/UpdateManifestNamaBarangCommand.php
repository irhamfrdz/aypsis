<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateManifestNamaBarangCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifest:update-nama-barang';

    protected $description = 'Update existing manifest nama_barang to match detailed items from Tanda Terima';

    public function handle()
    {
        $this->info("Starting Manifest nama_barang update...");
        
        $updated = 0;
        
        \App\Models\Manifest::chunkById(200, function ($manifests) use (&$updated) {
            foreach ($manifests as $manifest) {
                try {
                    $tt = null;
                    if (!empty($manifest->nomor_tanda_terima)) {
                        $tt = \App\Models\TandaTerima::where('no_surat_jalan', $manifest->nomor_tanda_terima)->first();
                    }
                    if (!$tt && $manifest->prospek_id) {
                        $prospek = \App\Models\Prospek::find($manifest->prospek_id);
                        if ($prospek && $prospek->tandaTerima) {
                            $tt = $prospek->tandaTerima;
                        }
                    }

                    if ($tt) {
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
                            // Check if it's different to save queries
                            if ($manifest->nama_barang !== $newName) {
                                $manifest->nama_barang = $newName;
                                $manifest->save();
                                $updated++;
                                $this->info("Updated Manifest ID {$manifest->id} (BL: {$manifest->nomor_bl}) with detailed items.");
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("Error updating Manifest ID {$manifest->id}: " . $e->getMessage());
                }
            }
        });
        
        $this->info("Finished updating {$updated} manifests.");
    }
}
