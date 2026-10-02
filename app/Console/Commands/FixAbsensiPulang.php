<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixAbsensiPulang extends Command
{
    protected $signature = 'absensi:fix-pulang';

    protected $description = 'Koreksi log absensi sore (>= 12:00) yang tercatat sebagai Masuk menjadi Pulang jika sudah memiliki absen masuk pagi';

    public function handle()
    {
        $this->info('Mencari log absensi sore yang salah tercatat sebagai Masuk...');

        $subQuery = DB::select("
            SELECT a2.id
            FROM absensis a2
            JOIN absensis a1 ON a1.nik = a2.nik
                AND DATE(a1.waktu) = DATE(a2.waktu)
                AND a1.id != a2.id
                AND a1.waktu < a2.waktu
            WHERE a2.tipe = 'Masuk'
              AND a1.tipe = 'Masuk'
              AND TIME(a2.waktu) >= '12:00:00'
              AND TIME(a1.waktu) < '12:00:00'
              AND NOT EXISTS (
                  SELECT 1 FROM absensis a3
                  WHERE a3.nik = a2.nik
                    AND DATE(a3.waktu) = DATE(a2.waktu)
                    AND LOWER(a3.tipe) IN ('pulang', 'keluar')
              )
        ");

        $ids = array_unique(array_column($subQuery, 'id'));
        $count = count($ids);

        if ($count === 0) {
            $this->info('Tidak ada data yang perlu dikoreksi. Semua log absensi sudah valid.');

            return 0;
        }

        $this->info("Ditemukan {$count} data absensi sore yang perlu diubah menjadi Pulang.");

        $chunks = array_chunk($ids, 500);
        $totalUpdated = 0;
        foreach ($chunks as $chunk) {
            $affected = DB::table('absensis')
                ->whereIn('id', $chunk)
                ->update([
                    'tipe' => 'Pulang',
                    'keterangan' => DB::raw("CONCAT(COALESCE(keterangan, ''), ' | Koreksi auto-switch State Pulang')"),
                ]);
            $totalUpdated += $affected;
        }

        $this->info("Berhasil mengoreksi {$totalUpdated} data absensi menjadi 'Pulang'!");

        return 0;
    }
}
