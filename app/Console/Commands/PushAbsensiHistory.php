<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PushAbsensiHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:push-history {--url=http://192.168.1.2:8084/api/absensi/push} {--mesin=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push historical attendance data from local att2000.mdb to Production API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiUrl = $this->option('url');
        $mesinId = $this->option('mesin');
        $secret = env('API_SYNC_SECRET', 'aypsis-sync-12345');

        $mdbPath = env('MDB_PATH', 'C:\\Program Files (x86)\\Solution\\att2000.mdb');
        
        if (!file_exists($mdbPath)) {
            $this->error("Database Solution tidak ditemukan di: {$mdbPath}");
            return;
        }

        $this->info("Membaca database lokal: {$mdbPath}");

        try {
            $conn = new \PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$mdbPath;Uid=;Pwd=;");
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $query = "SELECT u.Badgenumber, c.CHECKTIME, c.CHECKTYPE 
                      FROM CHECKINOUT c 
                      INNER JOIN USERINFO u ON c.USERID = u.USERID
                      ORDER BY c.CHECKTIME DESC";
            $stmt = $conn->query($query);

            $logs = [];
            $chunks = [];
            
            while ($log = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $checktype = strtoupper($log['CHECKTYPE']);
                $type = (in_array($checktype, ['I', '0', 'MASUK'])) ? 'Masuk' : 'Pulang';
                
                $logTime = Carbon::parse($log['CHECKTIME']);
                if ($logTime->hour >= 4 && $logTime->hour < 12) {
                    $type = 'Masuk';
                }

                $logs[] = [
                    'nik' => trim($log['Badgenumber']),
                    'waktu' => $logTime->format('Y-m-d H:i:s'),
                    'tipe' => $type
                ];

                if (count($logs) >= 500) {
                    $chunks[] = $logs;
                    $logs = [];
                }
            }
            if (count($logs) > 0) {
                $chunks[] = $logs;
            }

            $total = 0;
            foreach ($chunks as $c) {
                $total += count($c);
            }
            
            if ($total === 0) {
                $this->info("Tidak ada data absensi di file MDB.");
                return;
            }

            $this->info("Ditemukan total {$total} baris absensi. Mulai mengirim ke server...");

            $berhasil = 0;
            $bar = $this->output->createProgressBar(count($chunks));
            $bar->start();

            foreach ($chunks as $chunk) {
                $response = Http::timeout(60)->withHeaders([
                    'X-Sync-Secret' => $secret
                ])->post($apiUrl, [
                    'mesin_id' => $mesinId,
                    'logs' => $chunk
                ]);

                if ($response->successful()) {
                    $berhasil += $response->json('synced_count', 0);
                } else {
                    $this->error("Gagal mengirim batch: " . $response->body());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Selesai! {$berhasil} data riwayat baru berhasil masuk ke server.");

        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }
}
