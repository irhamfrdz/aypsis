<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportAttlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:attlog {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data absensi dari file attlog.dat (USB ZKTeco)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return;
        }

        $this->info("Membaca file {$file}...");
        
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $total = count($lines);
        $this->info("Total data ditemukan: {$total} baris.");

        // Load semua karyawan ke memory untuk mempercepat pencarian ID
        $karyawans = Karyawan::pluck('id', 'nik')->toArray();
        
        $inserted = 0;
        $skipped = 0;
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $chunk = [];
        $chunkSize = 500;

        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', trim($line));
            
            // Pastikan format memenuhi standar minimal: NIK, Date, Time, dll.
            if (count($parts) < 4) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $nik = str_pad($parts[0], 4, '0', STR_PAD_LEFT);
            $logTime = $parts[1] . ' ' . $parts[2];
            
            // Validasi waktu
            if (!strtotime($logTime)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Di file attlog.dat USB, biasanya state ada di index ke-4
            $state = isset($parts[4]) ? (int) $parts[4] : 0;
            
            // Terjemahkan state ke tipe (ini tipe standar, tapi karena mesin CLX agak ngaco, 
            // kita tambahkan logika pintar khusus pagi hari otomatis jadi Masuk)
            $hour = (int) date('H', strtotime($logTime));
            
            if ($hour >= 5 && $hour <= 12) {
                $type = 'Masuk';
            } elseif ($hour >= 15 && $hour <= 23) {
                $type = 'Pulang';
            } else {
                // Fallback ke state asli jika di luar jam sibuk (lembur/istirahat)
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
            }

            $karyawan_id = $karyawans[$nik] ?? null;

            $chunk[] = [
                'nik' => $nik,
                'waktu' => $logTime,
                'tipe' => $type,
                'karyawan_id' => $karyawan_id,
                'mesin_id' => null, 
                'keterangan' => 'Import Manual attlog.dat',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($chunk) >= $chunkSize) {
                // insertOrIgnore digunakan agar tidak error jika ada data ganda (menghindari Unique Constraint)
                DB::table('absensis')->insertOrIgnore($chunk);
                $inserted += count($chunk);
                $chunk = [];
            }
            
            $bar->advance();
        }

        // Insert sisa data yang kurang dari chunkSize
        if (count($chunk) > 0) {
            DB::table('absensis')->insertOrIgnore($chunk);
            $inserted += count($chunk);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Import Selesai! Berhasil diproses: {$inserted} | Dilewati (format salah): {$skipped}");
    }
}
