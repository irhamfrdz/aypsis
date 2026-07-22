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

        $locations = [];

        foreach ($mobils as $mobil) {
            $gpsData = $this->gpsService->getLatestLocation($mobil->imei_gps);
            
            // Jika request API berhasil dan mengembalikan koordinat
            if ($gpsData && isset($gpsData['success']) && $gpsData['success']) {
                $locations[] = [
                    'mobil_id' => $mobil->id,
                    'nomor_polisi' => $mobil->nomor_polisi,
                    'merek' => $mobil->merek,
                    'jenis' => $mobil->jenis,
                    'lat' => $gpsData['data']['lat'] ?? null,
                    'lng' => $gpsData['data']['lng'] ?? null,
                    'speed' => $gpsData['data']['speed'] ?? 0,
                    'status' => $gpsData['data']['status'] ?? 'Unknown',
                    'last_update' => $gpsData['data']['last_update'] ?? now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}
