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

        $nopols = $mobils->pluck('nomor_polisi')->filter()->toArray();
        $activeSjs = \DB::table('surat_jalans')
            ->select('*', \DB::raw("'Muatan' as tipe_sj"))
            ->whereIn('no_plat', $nopols)
            ->whereIn('status', ['draft', 'belum masuk checkpoint', 'sudah_checkpoint'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('no_plat');

        $activeSjBongkarans = \DB::table('surat_jalan_bongkarans')
            ->select('*', \DB::raw("'Bongkaran' as tipe_sj"))
            ->whereIn('no_plat', $nopols)
            ->whereIn('status', ['draft', 'belum masuk checkpoint', 'sudah_checkpoint'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('no_plat');

        $activeSjBatam = \DB::table('surat_jalan_bongkaran_batams')
            ->select('*', \DB::raw("'Bongkaran Batam' as tipe_sj"))
            ->whereIn('no_plat', $nopols)
            ->whereIn('status', ['draft', 'belum masuk checkpoint', 'sudah_checkpoint'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('no_plat');

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

                $sjList = $activeSjs->get($mobil->nomor_polisi) ?? collect();
                $sjbList = $activeSjBongkarans->get($mobil->nomor_polisi) ?? collect();
                $sjbatamList = $activeSjBatam->get($mobil->nomor_polisi) ?? collect();
                
                // Gabungkan semua surat jalan aktif yang dimiliki truk ini
                $allActiveSj = $sjList->concat($sjbList)->concat($sjbatamList)->sortByDesc('created_at')->values();

                $info_sjs = [];
                foreach ($allActiveSj as $activeSj) {
                    $isBongkaran = isset($activeSj->nomor_surat_jalan);
                    $info_sjs[] = [
                        'no_surat_jalan' => $isBongkaran ? $activeSj->nomor_surat_jalan : $activeSj->no_surat_jalan,
                        'tujuan' => $activeSj->tujuan_pengiriman ?? '-',
                        'no_kontainer' => $activeSj->no_kontainer ?? '-',
                        'jenis_barang' => $activeSj->jenis_barang ?? '-',
                        'tipe' => $activeSj->tipe_sj
                    ];
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
                    'info_sjs' => $info_sjs,
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
