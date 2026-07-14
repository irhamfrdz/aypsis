<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mesin;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Services\ZkTecoService;
use Carbon\Carbon;

class SyncAttendanceLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:sync-local {--mesin= : ID Mesin spesifik untuk mengimpor data (opsional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data absensi dari mesin fingerprint lokal (.mdb / UDP) secara otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai sinkronisasi absensi...');

        $mesinId = $this->option('mesin');
        $query = Mesin::where('status', 'Aktif');
        
        if ($mesinId) {
            $query->where('id', $mesinId);
        }
        
        $mesins = $query->get();
        if ($mesins->isEmpty()) {
            $this->warn('Tidak ada mesin aktif yang terdaftar.');
            return 0;
        }

        $mdbPath = env('MDB_PATH', 'C:\\Program Files (x86)\\Solution\\att2000.mdb');
        $hasMdb = file_exists($mdbPath);

        // Cache existing logs to avoid N+1 queries
        $existingLogs = Absensi::select('nik', 'waktu', 'tipe')
            ->get()
            ->mapWithKeys(function ($item) {
                $timeStr = $item->waktu instanceof Carbon 
                    ? $item->waktu->format('Y-m-d H:i:s') 
                    : Carbon::parse($item->waktu)->format('Y-m-d H:i:s');
                return [$item->nik . '_' . $timeStr . '_' . $item->tipe => true];
            })
            ->toArray();

        // Cache employees
        $employees = Karyawan::select('id', 'nik')
            ->whereNotNull('nik')
            ->get()
            ->pluck('id', 'nik')
            ->toArray();

        foreach ($mesins as $mesin) {
            $this->info("Menyinkronkan mesin: {$mesin->nama_mesin}...");
            $syncedDirectly = false;

            // Method 1: Try direct connection to the machine over the network (Real-time UDP Socket)
            if (!empty($mesin->ip_address)) {
                $this->info("Mencoba koneksi langsung ke mesin ({$mesin->ip_address}:{$mesin->port})...");
                try {
                    $service = new ZkTecoService($mesin->ip_address, $mesin->port);
                    if ($service->connect()) {
                        $logs = $service->getAttendance();
                        $service->disconnect();

                        if (!empty($logs)) {
                            $syncedCount = 0;
                            foreach ($logs as $log) {
                                $nik = trim($log['pin']);
                                if (is_numeric($nik)) {
                                    $nik = str_pad($nik, 4, '0', STR_PAD_LEFT);
                                }
                                $type = $log['type'];
                                $logTime = Carbon::parse($log['timestamp'])->format('Y-m-d H:i:s');

                                $key = $nik . '_' . $logTime . '_' . $type;
                                if (isset($existingLogs[$key])) {
                                    continue;
                                }

                                Absensi::create([
                                    'nik' => $nik,
                                    'waktu' => $logTime,
                                    'tipe' => $type,
                                    'karyawan_id' => $employees[$nik] ?? null,
                                    'mesin_id' => $mesin->id,
                                    'keterangan' => 'Auto-sync mesin '.$mesin->nama_mesin,
                                ]);

                                $syncedCount++;
                            }
                            $this->info("Berhasil! {$syncedCount} data absensi baru diimpor langsung dari mesin.");
                        } else {
                            $this->info('Tidak ada data absensi baru yang ditemukan di mesin.');
                        }
                        $syncedDirectly = true;
                    } else {
                        $this->warn("Gagal terhubung langsung ke IP mesin {$mesin->nama_mesin}.");
                    }
                } catch (\Exception $e) {
                    $this->error("Gagal melakukan koneksi langsung: " . $e->getMessage());
                }
            }

            // Method 2: Fallback to reading the local Solution desktop database file (.mdb)
            if ($hasMdb) {
                $this->info('Membaca database lokal (.mdb) untuk memastikan tidak ada data yang terlewat...');
                try {
                    $conn = new \PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$mdbPath;Uid=;Pwd=;");
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                    // Fetch all machines from MDB to map MDB SENSORID / SN to IP Address
                    $macStmt = $conn->query("SELECT ID, IP, sn FROM Machines");
                    $mdbMachines = $macStmt->fetchAll(\PDO::FETCH_ASSOC);

                    // Build a lookup map: MDB Machine ID -> Laravel Mesin ID & MDB Machine SN -> Laravel Mesin ID
                    $laravelMesins = \App\Models\Mesin::all();
                    $machineMap = [];
                    $snMap = [];
                    foreach ($mdbMachines as $mm) {
                        $ip = trim($mm['IP']);
                        $sn = trim($mm['sn']);
                        $mdbId = $mm['ID'];

                        $matchedMesin = $laravelMesins->first(function ($m) use ($ip) {
                            return trim($m->ip_address) === $ip;
                        });

                        if ($matchedMesin) {
                            $machineMap[$mdbId] = $matchedMesin->id;
                            if (!empty($sn)) {
                                $snMap[$sn] = $matchedMesin->id;
                            }
                        }
                    }

                    // Fetch logs from last 60 days
                    $query = "SELECT c.USERID, u.Badgenumber, c.CHECKTIME, c.CHECKTYPE, c.SENSORID, c.sn 
                              FROM CHECKINOUT c 
                              INNER JOIN USERINFO u ON c.USERID = u.USERID
                              WHERE c.CHECKTIME >= DateAdd('d', -60, Now())";
                    $stmt = $conn->query($query);
                    $mdbLogs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    $syncedCount = 0;
                    foreach ($mdbLogs as $log) {
                        $logSensorId = $log['SENSORID'] ?? null;
                        $logSn = trim($log['sn'] ?? '');

                        $resolvedMesinId = null;
                        if (!empty($logSn) && isset($snMap[$logSn])) {
                            $resolvedMesinId = $snMap[$logSn];
                        } elseif (isset($machineMap[$logSensorId])) {
                            $resolvedMesinId = $machineMap[$logSensorId];
                        }

                        // Fallback to the first machine in the list if cannot resolve
                        if (!$resolvedMesinId) {
                            $resolvedMesinId = $laravelMesins->first()->id ?? $mesin->id;
                        }

                        // Only sync logs belonging to the current machine in this iteration
                        if ($resolvedMesinId != $mesin->id) {
                            continue;
                        }

                        $nik = trim($log['Badgenumber']);
                        if (is_numeric($nik)) {
                            $nik = str_pad($nik, 4, '0', STR_PAD_LEFT);
                        }
                        $type = (in_array(strtoupper($log['CHECKTYPE']), ['I', '0', 'MASUK'])) ? 'Masuk' : 'Pulang';
                        $logTime = Carbon::parse($log['CHECKTIME'])->format('Y-m-d H:i:s');
                        
                        $hour = (int) Carbon::parse($log['CHECKTIME'])->format('H');
                        if ($hour >= 4 && $hour < 12) {
                            $type = 'Masuk';
                        }

                        $key = $nik . '_' . $logTime . '_' . $type;
                        if (isset($existingLogs[$key])) {
                            continue;
                        }

                        Absensi::create([
                            'nik' => $nik,
                            'waktu' => $logTime,
                            'tipe' => $type,
                            'karyawan_id' => $employees[$nik] ?? null,
                            'mesin_id' => $resolvedMesinId,
                            'keterangan' => 'Auto-sync database lokal '.$mesin->nama_mesin,
                        ]);

                        $syncedCount++;
                    }

                    $this->info("Berhasil! {$syncedCount} data absensi baru diimpor dari database lokal.");

                } catch (\Exception $e) {
                    $this->error('Gagal membaca database lokal: ' . $e->getMessage());
                }
            }
        }

        $this->info('Sinkronisasi selesai!');
        return 0;
    }
}
