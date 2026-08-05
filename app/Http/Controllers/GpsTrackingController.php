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
        // Ambil semua mobil yang sudah didaftarkan IMEI GPS-nya beserta data supir
        $mobils = Mobil::with('karyawan')
            ->whereNotNull('imei_gps')
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
        $mobils = Mobil::with('karyawan')
            ->whereNotNull('imei_gps')
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
                    'supir' => $mobil->karyawan ? ($mobil->karyawan->nama_panggilan ?? $mobil->karyawan->nama_lengkap) : 'Tidak Ada Supir',
                    'lat' => $payload['latitude'] ?? null,
                    'lng' => $payload['longitude'] ?? null,
                    'speed' => $payload['speed'] ?? 0,
                    'status' => $statusText,
                    'alamat' => $payload['address'] ?? $payload['location'] ?? null,
                    'last_update' => $payload['last_update'] ?? now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Dapatkan riwayat pergerakan GPS untuk sebuah mobil (maks. 2 minggu)
     */
    public function getHistory($mobil_id)
    {
        // Hitung batas waktu mulai diam
        // Menggunakan waktu saat data ditarik
        $mobil = Mobil::findOrFail($mobil_id);
        
        $history = \App\Models\GpsHistory::where('mobil_id', $mobil_id)
            ->where('recorded_at', '>=', now()->subDays(14))
            ->orderBy('recorded_at', 'asc')
            ->get();
            
        // Logika untuk mendeteksi berapa hari mobil tidak bergerak dari posisi terakhir
        $lastLocation = $history->last();
        $daysNotMoving = 0;
        
        if ($lastLocation) {
            // Cari dari belakang ke depan, kapan mobil terakhir memiliki kecepatan > 0 atau berubah posisi signifikan
            // Namun untuk lebih mudah, cek kapan terakhir kali bergerak (speed > 0)
            $lastMoved = $history->where('speed', '>', 0)->last();
            
            if ($lastMoved) {
                $daysNotMoving = $lastMoved->recorded_at->diffInDays(now());
            } else {
                // Jika tidak pernah bergerak sama sekali dalam riwayat ini
                $firstRecord = $history->first();
                $daysNotMoving = $firstRecord ? $firstRecord->recorded_at->diffInDays(now()) : 0;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'mobil' => [
                    'nomor_polisi' => $mobil->nomor_polisi,
                    'merek' => $mobil->merek,
                ],
                'days_not_moving' => floor($daysNotMoving),
                'history' => $history->map(function($item) {
                    return [
                        'lat' => $item->lat,
                        'lng' => $item->lng,
                        'speed' => $item->speed,
                        'status' => $item->status,
                        'recorded_at' => $item->recorded_at->format('Y-m-d H:i:s'),
                        'alamat' => $item->alamat,
                    ];
                })
            ]
        ]);
    }
}
