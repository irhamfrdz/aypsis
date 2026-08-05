<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncGpsHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:sync-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi riwayat lokasi GPS setiap 5 menit dan hapus data lebih dari 14 hari';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\GpsIdService $gpsService)
    {
        $this->info('Memulai sinkronisasi riwayat GPS...');

        // 1. Ambil data mobil
        $mobils = \App\Models\Mobil::whereNotNull('imei_gps')
            ->where('imei_gps', '!=', '')
            ->get();

        $imeis = $mobils->pluck('imei_gps')->filter()->toArray();
        if (empty($imeis)) {
            $this->info('Tidak ada IMEI GPS yang terdaftar.');
            return;
        }

        // 2. Fetch data dari API GPS
        $bulkGpsData = $gpsService->getLatestLocationsBulk($imeis);
        
        $insertedCount = 0;

        foreach ($mobils as $mobil) {
            $gpsData = $bulkGpsData[$mobil->imei_gps] ?? null;
            
            if ($gpsData && isset($gpsData['status']) && $gpsData['status']) {
                $payload = $gpsData['message']['data'] ?? [];
                
                $statusText = 'Berhenti';
                if (($payload['speed'] ?? 0) > 0) {
                    $statusText = 'Berjalan';
                } elseif (isset($payload['acc']) && $payload['acc'] == 'ON') {
                    $statusText = 'Mesin Menyala';
                }

                // Cek apakah data lokasi valid
                if (isset($payload['latitude']) && isset($payload['longitude'])) {
                    // Cek duplikasi record pada waktu yang sama
                    $recordedAt = isset($payload['last_update']) ? \Carbon\Carbon::parse($payload['last_update']) : now();
                    
                    $exists = \App\Models\GpsHistory::where('mobil_id', $mobil->id)
                        ->where('recorded_at', $recordedAt)
                        ->exists();

                    if (!$exists) {
                        \App\Models\GpsHistory::create([
                            'mobil_id' => $mobil->id,
                            'imei_gps' => $mobil->imei_gps,
                            'lat' => $payload['latitude'],
                            'lng' => $payload['longitude'],
                            'speed' => $payload['speed'] ?? 0,
                            'status' => $statusText,
                            'alamat' => $payload['address'] ?? $payload['location'] ?? null,
                            'recorded_at' => $recordedAt,
                        ]);
                        $insertedCount++;
                    }
                }
            }
        }

        $this->info("Berhasil menyimpan {$insertedCount} riwayat lokasi.");

        // 3. Hapus data yang usianya lebih dari 14 hari
        $deletedCount = \App\Models\GpsHistory::where('recorded_at', '<', now()->subDays(14))->delete();
        $this->info("Berhasil menghapus {$deletedCount} riwayat lama (lebih dari 14 hari).");
    }
}
