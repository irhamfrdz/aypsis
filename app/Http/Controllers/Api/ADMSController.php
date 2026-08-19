<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\AdmsCommand;
use App\Models\MesinUser;
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

        if ($table === 'ATTLOG' || $table === 'attlog') {
            $this->processAttLog($rawData, $sn);
        } elseif (stripos($table, 'user') !== false) { // Handle 'user', 'USER', 'USERINFO'
            $this->processUserInfo($rawData, $sn);
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
        
        // Cek apakah ada antrean perintah untuk mesin ini
        $command = AdmsCommand::where('sn', $sn)
            ->where('status', 'pending')
            ->orderBy('id', 'asc')
            ->first();

        if ($command) {
            $command->update(['status' => 'sent']);
            // Format wajib ADMS: C:<id>:<command>
            $response = "C:{$command->id}:{$command->command}\r\n";
            Log::info("ADMS SendCommand ke SN: {$sn} -> " . $response);
            return response($response, 200)->header('Content-Type', 'text/plain');
        }

        // Balas OK untuk memberitahu tidak ada perintah
        return response("OK\r\n", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Mesin mengirimkan hasil dari perintah (POST /iclock/devicecmd)
     */
    public function deviceCmd(Request $request)
    {
        $sn = $request->query('SN');
        $rawData = $request->getContent();
        Log::info("ADMS DeviceCmd dari SN: {$sn} | Payload:", ['data' => $rawData]);

        // Format return biasanya: ID=ReturnCode
        // Contoh: ID=1&Return=0&CMD=DATA QUERY USERINFO
        // Jika return berisi data, formatnya bisa berbeda (misal multiline)
        $lines = explode("\n", $rawData);
        $commandId = null;

        // Cari ID perintah dari baris pertama (misal: ID=1&Return=0)
        if (isset($lines[0]) && preg_match('/ID=(\d+)/', $lines[0], $matches)) {
            $commandId = $matches[1];
        }

        if ($commandId) {
            $command = AdmsCommand::find($commandId);
            if ($command) {
                $command->update([
                    'status' => 'success',
                    'response_data' => $rawData
                ]);

                // Jika ini adalah perintah tarik user
                if (strpos($command->command, 'USERINFO') !== false) {
                    $this->processUserInfo($rawData, $sn);
                }
            }
        }

        return response("OK\r\n", 200)->header('Content-Type', 'text/plain');
    }

    private function processUserInfo($rawData, $sn)
    {
        $lines = explode("\n", $rawData);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $userData = [];

            // Format 1: Dari devicecmd (misal: PIN=123 Name=Adit Pri=0)
            if (strpos(strtoupper($line), 'PIN=') !== false) {
                preg_match_all('/(\w+)=([^,\t]*)/', $line, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $idx => $key) {
                        $userData[strtoupper($key)] = trim($matches[2][$idx]);
                    }
                }
            } 
            // Format 2: Dari cdata?table=USERINFO (Tab-separated values: PIN Name Pri Pass Grp...)
            else {
                // Abaikan jika baris adalah header teks "PIN"
                if (strtoupper(trim(explode("\t", $line)[0])) === 'PIN') continue;

                // Kadang dipisahkan koma, kadang tab. Kita coba pisahkan
                $parts = preg_split('/[\t,]+/', $line);
                if (count($parts) >= 1) {
                    $userData['PIN'] = trim($parts[0]);
                    $userData['NAME'] = isset($parts[1]) ? trim($parts[1]) : null;
                    $userData['PRI'] = isset($parts[2]) ? trim($parts[2]) : null;
                    $userData['PWD'] = isset($parts[3]) ? trim($parts[3]) : null;
                    // Pada ZKTeco, index ke-4 biasanya CardNo, ke-5 Group
                    $userData['GRP'] = isset($parts[5]) ? trim($parts[5]) : null;
                }
            }

            if (!empty($userData['PIN']) && is_numeric($userData['PIN'])) {
                MesinUser::updateOrCreate(
                    ['sn' => $sn, 'pin' => $userData['PIN']],
                    [
                        'name' => $userData['NAME'] ?? null,
                        'privilege' => $userData['PRI'] ?? null,
                        'password' => $userData['PWD'] ?? null,
                        'group' => $userData['GRP'] ?? null,
                    ]
                );
            }
        }
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
                    $parsedTime = Carbon::parse($datetimeStr, 'Asia/Jakarta');
                    
                    // Jika waktu absensi adalah 09:01 atau 09:02, sesuaikan menjadi 09:00
                    if ($parsedTime->format('H:i') === '09:01' || $parsedTime->format('H:i') === '09:02') {
                        $parsedTime->setTime(9, 0, 0);
                    }
                    
                    $logTime = $parsedTime->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    continue; // Skip format tanggal salah
                }
                
                // Index ke-3 biasanya state (0=Masuk, 1=Pulang, 2=Break Out, 3=Break In, 4=OT In, 5=OT Out)
                $state = isset($parts[3]) ? (int) $parts[3] : 0;
                
                if (in_array($state, [0, 3, 4])) {
                    $type = 'Masuk';
                } else {
                    $type = 'Pulang';
                }

                // VerifyMethod biasanya di index 4 atau 3 jika tidak ada state yang panjang
                $verifyMethodRaw = isset($parts[4]) ? (int) $parts[4] : (isset($parts[3]) ? (int) $parts[3] : null);
                $verifyMode = null;
                if ($verifyMethodRaw !== null) {
                    if ($verifyMethodRaw === 1) $verifyMode = 'Fingerprint';
                    elseif ($verifyMethodRaw === 15 || $verifyMethodRaw === 14) $verifyMode = 'Face';
                    elseif ($verifyMethodRaw === 0) $verifyMode = 'Password';
                    elseif ($verifyMethodRaw === 2) $verifyMode = 'Card';
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
                        'verify_mode' => $verifyMode,
                    ]);
                }
            }
        }
    }
}
