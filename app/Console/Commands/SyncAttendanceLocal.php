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
    protected $signature = 'attendance:sync-local';

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

        $mesins = Mesin::where('status', 'Aktif')->get();
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
            if (!$syncedDirectly && $hasMdb) {
                $this->info('Menggunakan metode fallback database lokal (.mdb)...');
                try {
                    $conn = new \PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$mdbPath;Uid=;Pwd=;");
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                    // Fetch logs from last 60 days
                    $query = "SELECT c.USERID, u.Badgenumber, c.CHECKTIME, c.CHECKTYPE 
                              FROM CHECKINOUT c 
                              INNER JOIN USERINFO u ON c.USERID = u.USERID
                              WHERE c.CHECKTIME >= DateAdd('d', -60, Now())";
                    $stmt = $conn->query($query);
                    $mdbLogs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    $syncedCount = 0;
                    foreach ($mdbLogs as $log) {
                        $nik = trim($log['Badgenumber']);
                        if (is_numeric($nik)) {
                            $nik = str_pad($nik, 4, '0', STR_PAD_LEFT);
                        }
                        $type = (in_array(strtoupper($log['CHECKTYPE']), ['I', '0', 'MASUK'])) ? 'Masuk' : 'Pulang';
                        $logTime = Carbon::parse($log['CHECKTIME'])->format('Y-m-d H:i:s');

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
