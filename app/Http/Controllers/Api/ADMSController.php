<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;

class ADMSController extends Controller
{
    /**
     * Handshake awal dari mesin ZKTeco (GET /iclock/cdata)
     * Mesin akan meminta konfigurasi dari server
     */
    public function handshake(Request $request)
    {
        $sn = $request->query('SN'); // Serial Number mesin
        
        Log::info("ADMS Handshake dari Mesin SN: {$sn}");

        // Response wajib agar mesin tahu server merespon dan siap menerima data
        $response = "GET OPTION FROM: {$sn}\r\n";
        $response .= "Stamp=0\r\n";
        $response .= "OpStamp=0\r\n";
        $response .= "ErrorDelay=60\r\n";
        $response .= "Delay=30\r\n";
        $response .= "TransTimes=00:00;14:00\r\n";
        $response .= "TransInterval=1\r\n";
        $response .= "TransFlag=1111000000\r\n";
        $response .= "TimeZone=7\r\n";
        $response .= "Realtime=1\r\n";
        $response .= "Encrypt=0\r\n";

        return response($response, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Mesin mengirim data log absensi (POST /iclock/cdata)
     */
    public function receiveData(Request $request)
    {
        $sn = $request->query('SN');
        $table = $request->query('table'); // biasanya bernilai 'ATTLOG'
        
        $rawData = $request->getContent();
        
        Log::info("ADMS Terima Data dari SN: {$sn} | Table: {$table} | Payload:", ['data' => $rawData]);

        if ($table === 'ATTLOG') {
            $this->processAttLog($rawData, $sn);
        }

        // Harus membalas "OK" agar mesin menganggap data sudah terkirim 
        // dan menghapusnya dari memori antrean pengiriman.
        return response("OK\r\n", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Mesin mengecek apakah ada command/perintah dari server (GET /iclock/getrequest)
     */
    public function getRequest(Request $request)
    {
        $sn = $request->query('SN');
        Log::info("ADMS GetRequest dari SN: {$sn}");
        
        // Balas OK untuk memberitahu tidak ada perintah (reboot, clear log, dll)
        return response("OK\r\n", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Parse raw string data dari ZKTeco dan simpan ke database
     */
    private function processAttLog($rawData, $sn)
    {
        // Format raw biasanya dipisah dengan newline \n
        $lines = explode("\n", $rawData);
        
        $employees = Karyawan::select('id', 'nik')->whereNotNull('nik')->get()->pluck('id', 'nik')->toArray();
        // Cari Mesin ID berdasarkan SN, atau gunakan mesin pertama sebagai fallback
        $mesin = \App\Models\Mesin::where('kode_mesin', $sn)
                    ->orWhere('keterangan', 'like', "%{$sn}%")
                    ->first();
                    
        if (!$mesin) {
            $mesin = \App\Models\Mesin::create([
                'kode_mesin' => $sn,
                'nama_mesin' => 'Mesin Baru ' . $sn,
                'tipe_mesin' => 'ADMS',
                'status' => 'Aktif',
                'keterangan' => 'Auto-register dari ADMS',
            ]);
        }
        
        $mesinId = $mesin->id;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Format baris: PIN\tWaktu\tState\tVerifyMethod
            // Contoh: 1511    2023-10-12 08:00:00    1    1
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 3) {
                $nik = trim($parts[0]);
                if (is_numeric($nik)) {
                    $nik = str_pad($nik, 4, '0', STR_PAD_LEFT);
                }
                
                $dateStr = $parts[1]; // misal 2023-10-12
                $timeStr = $parts[2]; // misal 08:00:00
                $datetimeStr = $dateStr . ' ' . $timeStr;
                
                try {
                    // Memastikan data yang diproses selalu menggunakan zona waktu Jakarta (WIB)
                    $logTime = Carbon::parse($datetimeStr, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    continue; // Skip format tanggal salah
                }
                
                // Index ke-2 biasanya state (0=Masuk, 1=Pulang, 2=Break Out, 3=Break In, 4=OT In, 5=OT Out)
                $state = isset($parts[2]) ? (int) $parts[2] : 0;
                
                if ($state == 0) {
                    $type = 'Masuk';
                } elseif ($state == 1) {
                    $type = 'Pulang';
                } elseif ($state == 2) {
                    $type = 'istirahat_keluar';
                } elseif ($state == 3) {
                    $type = 'istirahat_masuk';
                } elseif ($state == 4) {
                    $type = 'lembur_masuk';
                } elseif ($state == 5) {
                    $type = 'lembur_pulang';
                } else {
                    $type = 'Pulang';
                }

                // Cegah duplikasi log yang sama persis (berdasarkan NIK dan waktu spesifik)
                // Hal ini memastikan semua tarikan punch (meskipun user lupa ganti state) tetap tersimpan
                $exists = Absensi::where('nik', $nik)
                                 ->where('waktu', $logTime)
                                 ->exists();

                if (!$exists) {
                    Absensi::create([
                        'nik' => $nik,
                        'waktu' => $logTime,
                        'tipe' => $type,
                        'karyawan_id' => $employees[$nik] ?? null,
                        'mesin_id' => $mesinId,
                        'keterangan' => 'ADMS Push (SN: ' . $sn . ')',
                    ]);
                }
            }
        }
    }
}
