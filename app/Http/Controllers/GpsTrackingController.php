<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Services\GpsIdService;
use Illuminate\Http\Request;

class GpsTrackingController extends Controller
{
    protected $gpsService;

    public function __construct(GpsIdService $gpsService)
    {
        $this->gpsService = $gpsService;
    }

    /**
     * Tampilkan halaman Live Tracking Peta
     */
    public function index()
    {
        // Ambil semua mobil yang sudah didaftarkan IMEI GPS-nya
        $mobils = Mobil::whereNotNull('imei_gps')
            ->where('imei_gps', '!=', '')
            ->get();

        $googleMapsApiKey = env('GOOGLE_MAPS_API_KEY', '');

        return view('gps-tracking.index', compact('mobils', 'googleMapsApiKey'));
    }

    /**
     * Endpoint API untuk Ajax call mendapatkan koordinat terbaru dari semua mobil
     */
    public function getLatestLocations()
    {
        $mobils = Mobil::whereNotNull('imei_gps')
            ->where('imei_gps', '!=', '')
            ->get();

        $imeis = $mobils->pluck('imei_gps')->filter()->toArray();
        $bulkGpsData = !empty($imeis) ? $this->gpsService->getLatestLocationsBulk($imeis) : [];

        $locations = [];

        foreach ($mobils as $mobil) {
            $gpsData = $bulkGpsData[$mobil->imei_gps] ?? null;
            
            // Jika request API berhasil dan mengembalikan koordinat
            if ($gpsData && isset($gpsData['status']) && $gpsData['status']) {
                $payload = $gpsData['message']['data'] ?? [];
                
                $statusText = 'Berhenti';
                if (($payload['speed'] ?? 0) > 0) {
                    $statusText = 'Berjalan';
                } elseif (isset($payload['acc']) && $payload['acc'] == 'ON') {
                    $statusText = 'Mesin Menyala';
                }

                $locations[] = [
                    'mobil_id' => $mobil->id,
                    'nomor_polisi' => $mobil->nomor_polisi,
                    'merek' => $mobil->merek,
                    'jenis' => $mobil->jenis,
                    'lat' => $payload['latitude'] ?? null,
                    'lng' => $payload['longitude'] ?? null,
                    'speed' => $payload['speed'] ?? 0,
                    'status' => $statusText,
                    'last_update' => $payload['last_update'] ?? now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}
