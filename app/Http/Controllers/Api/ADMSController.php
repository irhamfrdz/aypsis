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
        
        // Response wajib agar mesin tahu server merespon dan siap menerima data
        $response = "GET OPTION FROM: {$sn}\n";
        $response .= "Stamp=9999\n";
        $response .= "OpStamp=9999\n";
        $response .= "ErrorDelay=60\n";
        $response .= "Delay=30\n";
        $response .= "TransTimes=00:00;14:00\n";
        $response .= "TransInterval=1\n";
        $response .= "TransFlag=1111000000\n";
        $response .= "TimeZone=74\n"; // Jakarta GMT+7
        $response .= "Realtime=1\n";
        $response .= "Encrypt=0\n";

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
        
        if ($table === 'ATTLOG') {
            $this->processAttLog($rawData, $sn);
        }

        // Harus membalas "OK" agar mesin menganggap data sudah terkirim 
        // dan menghapusnya dari memori antrean pengiriman.
        return response("OK\n", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Mesin mengecek apakah ada command/perintah dari server (GET /iclock/getrequest)
     */
    public function getRequest(Request $request)
    {
        // Balas OK untuk memberitahu tidak ada perintah (reboot, clear log, dll)
        return response("OK\n", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Parse raw string data dari ZKTeco dan simpan ke database
     */
    private function processAttLog($rawData, $sn)
    {
        // Format raw biasanya dipisah dengan newline \n
        $lines = explode("\n", $rawData);
        
        $employees = Karyawan::select('id', 'nik')->whereNotNull('nik')->get()->pluck('id', 'nik')->toArray();
        
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
                    $logTime = Carbon::parse($datetimeStr)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    continue; // Skip format tanggal salah
                }
                
                // Index ke-3 biasanya state (0=Masuk, 1=Pulang, dll)
                $state = isset($parts[3]) ? (int) $parts[3] : 0;
                $type = (in_array($state, [0, 4, 5])) ? 'Masuk' : 'Pulang';

                // Cegah duplikasi
                $exists = Absensi::where('nik', $nik)
                                 ->where('waktu', $logTime)
                                 ->where('tipe', $type)
                                 ->exists();

                if (!$exists) {
                    Absensi::create([
                        'nik' => $nik,
                        'waktu' => $logTime,
                        'tipe' => $type,
                        'karyawan_id' => $employees[$nik] ?? null,
                        'keterangan' => 'ADMS Push (SN: ' . $sn . ')',
                    ]);
                }
            }
        }
    }
}
