<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixSuratJalanMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:surat-jalan-master';
    protected $description = 'Fix spelling and casing of Karyawan and Kendaraan on existing Surat Jalan data';

    public function handle()
    {
        $this->info("Fetching Master Data...");
        
        $supirMap = [];
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')->get(['nama_panggilan', 'nama_lengkap']);
        foreach ($karyawanSupirs as $k) {
            if ($k->nama_panggilan) $supirMap[strtolower(trim($k->nama_panggilan))] = $k->nama_panggilan;
            if ($k->nama_lengkap)   $supirMap[strtolower(trim($k->nama_lengkap))] = $k->nama_panggilan ?: $k->nama_lengkap;
        }

        $kraniMap = [];
        $karyawanKranis = \App\Models\Karyawan::where('divisi', 'krani')->get(['nama_panggilan', 'nama_lengkap']);
        foreach ($karyawanKranis as $k) {
            if ($k->nama_panggilan) $kraniMap[strtolower(trim($k->nama_panggilan))] = $k->nama_panggilan;
            if ($k->nama_lengkap)   $kraniMap[strtolower(trim($k->nama_lengkap))] = $k->nama_panggilan ?: $k->nama_lengkap;
        }

        $allKendaraansMap = [];
        foreach (\App\Models\Mobil::all(['nomor_polisi']) as $m) {
            if ($m->nomor_polisi) {
                $allKendaraansMap[strtolower(trim(str_replace(' ', '', $m->nomor_polisi)))] = $m->nomor_polisi;
            }
        }

        $this->info("Fixing Surat Jalan Bongkaran Batam...");
        
        $suratJalans = \App\Models\SuratJalanBongkaranBatam::all();
        $updatedCount = 0;

        // Manual alias mapping for old invalid data
        $aliasSupir = [
            'taufik' => 'TAUFIK H',
            'andrisyah' => 'ANDRIYANSYAH',
        ];

        foreach ($suratJalans as $sj) {
            $changed = false;
            
            // Fix Supir
            if (!empty($sj->supir)) {
                $key = strtolower(trim($sj->supir));
                
                // Apply manual alias if exists
                if (isset($aliasSupir[$key])) {
                    $key = strtolower(trim($aliasSupir[$key]));
                }

                if (isset($supirMap[$key]) && $sj->supir !== $supirMap[$key]) {
                    $sj->supir = $supirMap[$key];
                    $changed = true;
                }
            }
            
            // Fix Kenek
            if (!empty($sj->kenek)) {
                $key = strtolower(trim($sj->kenek));
                if (isset($kraniMap[$key]) && $sj->kenek !== $kraniMap[$key]) {
                    $sj->kenek = $kraniMap[$key];
                    $changed = true;
                }
            }
            
            // Fix Krani
            if (!empty($sj->krani)) {
                $key = strtolower(trim($sj->krani));
                if (isset($kraniMap[$key]) && $sj->krani !== $kraniMap[$key]) {
                    $sj->krani = $kraniMap[$key];
                    $changed = true;
                }
            }
            
            // Fix No Plat
            if (!empty($sj->no_plat)) {
                $key = strtolower(trim(str_replace(' ', '', $sj->no_plat)));
                if (isset($allKendaraansMap[$key]) && $sj->no_plat !== $allKendaraansMap[$key]) {
                    $sj->no_plat = $allKendaraansMap[$key];
                    $changed = true;
                }
            }

            if ($changed) {
                // Disable timestamps so we don't change updated_at accidentally (optional, but good practice for cleanup)
                $sj->timestamps = false;
                $sj->save();
                $updatedCount++;
            }
        }

        $this->info("Updated {$updatedCount} records in SuratJalanBongkaranBatam.");
    }
}
