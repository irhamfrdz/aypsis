<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

class UpdateNoPlatFromKaryawan extends Command
{
    protected $signature = 'update:no-plat-from-karyawan {--dry-run : Preview changes without saving}';
    protected $description = 'Update no_plat pada surat_jalans dan surat_jalan_bongkarans berdasarkan plat karyawan (periode 25 Feb - 24 Mar 2026)';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $startDate = '2026-02-25';
        $endDate = '2026-03-24';

        $this->info("=== Update No Plat dari Karyawan ===");
        $this->info("Periode: {$startDate} s/d {$endDate}");
        $this->info($isDryRun ? "MODE: DRY RUN (tidak akan menyimpan perubahan)" : "MODE: LIVE (perubahan akan disimpan)");
        $this->newLine();

        // Cache semua karyawan yang punya plat (lookup by nama_panggilan)
        $karyawanMap = Karyawan::whereNotNull('plat')
            ->where('plat', '!=', '')
            ->get()
            ->keyBy(function ($k) {
                return strtolower(trim($k->nama_panggilan));
            });

        $this->info("Total karyawan dengan plat: " . $karyawanMap->count());
        $this->newLine();

        // =============================================
        // 1. UPDATE SURAT JALAN
        // =============================================
        $this->info("--- Surat Jalan ---");

        $suratJalans = SuratJalan::where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
                      ->orWhereHas('tandaTerima', function ($q) use ($startDate, $endDate) {
                          $q->whereBetween('tanggal', [$startDate, $endDate]);
                      });
            })
            ->get();

        $this->info("Total Surat Jalan ditemukan: " . $suratJalans->count());

        $sjUpdated = 0;
        $sjSkipped = 0;
        $sjNotFound = [];
        $sjChanges = [];

        foreach ($suratJalans as $sj) {
            $supirName = strtolower(trim($sj->supir ?? ''));

            if (empty($supirName)) {
                $sjSkipped++;
                continue;
            }

            $karyawan = $karyawanMap->get($supirName);

            // Fallback: cari by nama_lengkap jika tidak ketemu by nama_panggilan
            if (!$karyawan) {
                $karyawan = Karyawan::whereNotNull('plat')
                    ->where('plat', '!=', '')
                    ->whereRaw('LOWER(TRIM(nama_lengkap)) = ?', [$supirName])
                    ->first();
            }

            if (!$karyawan) {
                if (!in_array($sj->supir, $sjNotFound)) {
                    $sjNotFound[] = $sj->supir;
                }
                $sjSkipped++;
                continue;
            }

            $oldPlat = $sj->no_plat;
            $newPlat = $karyawan->plat;

            // Hanya update jika berbeda
            if (strtolower(trim($oldPlat ?? '')) !== strtolower(trim($newPlat))) {
                $sjChanges[] = [
                    'ID' => $sj->id,
                    'No SJ' => $sj->no_surat_jalan,
                    'Supir' => $sj->supir,
                    'Plat Lama' => $oldPlat ?: '(kosong)',
                    'Plat Baru' => $newPlat,
                ];

                if (!$isDryRun) {
                    $sj->no_plat = $newPlat;
                    $sj->saveQuietly(); // saveQuietly agar tidak trigger event/audit
                }
                $sjUpdated++;
            } else {
                $sjSkipped++;
            }
        }

        if (count($sjChanges) > 0) {
            $this->table(['ID', 'No SJ', 'Supir', 'Plat Lama', 'Plat Baru'], $sjChanges);
        }
        $this->info("SJ Updated: {$sjUpdated} | Skipped/Sama: {$sjSkipped}");

        if (count($sjNotFound) > 0) {
            $this->warn("Supir tidak ditemukan di karyawan: " . implode(', ', $sjNotFound));
        }
        $this->newLine();

        // =============================================
        // 2. UPDATE SURAT JALAN BONGKARAN
        // =============================================
        $this->info("--- Surat Jalan Bongkaran ---");

        $suratJalanBongkarans = SuratJalanBongkaran::where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
                      ->orWhereHas('tandaTerima', function ($q) use ($startDate, $endDate) {
                          $q->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
                      });
            })
            ->get();

        $this->info("Total SJ Bongkaran ditemukan: " . $suratJalanBongkarans->count());

        $sjbUpdated = 0;
        $sjbSkipped = 0;
        $sjbNotFound = [];
        $sjbChanges = [];

        foreach ($suratJalanBongkarans as $sjb) {
            $supirName = strtolower(trim($sjb->supir ?? ''));

            if (empty($supirName)) {
                $sjbSkipped++;
                continue;
            }

            $karyawan = $karyawanMap->get($supirName);

            // Fallback: cari by nama_lengkap jika tidak ketemu by nama_panggilan
            if (!$karyawan) {
                $karyawan = Karyawan::whereNotNull('plat')
                    ->where('plat', '!=', '')
                    ->whereRaw('LOWER(TRIM(nama_lengkap)) = ?', [$supirName])
                    ->first();
            }

            if (!$karyawan) {
                if (!in_array($sjb->supir, $sjbNotFound)) {
                    $sjbNotFound[] = $sjb->supir;
                }
                $sjbSkipped++;
                continue;
            }

            $oldPlat = $sjb->no_plat;
            $newPlat = $karyawan->plat;

            // Hanya update jika berbeda
            if (strtolower(trim($oldPlat ?? '')) !== strtolower(trim($newPlat))) {
                $sjbChanges[] = [
                    'ID' => $sjb->id,
                    'No SJB' => $sjb->nomor_surat_jalan,
                    'Supir' => $sjb->supir,
                    'Plat Lama' => $oldPlat ?: '(kosong)',
                    'Plat Baru' => $newPlat,
                ];

                if (!$isDryRun) {
                    $sjb->no_plat = $newPlat;
                    $sjb->saveQuietly();
                }
                $sjbUpdated++;
            } else {
                $sjbSkipped++;
            }
        }

        if (count($sjbChanges) > 0) {
            $this->table(['ID', 'No SJB', 'Supir', 'Plat Lama', 'Plat Baru'], $sjbChanges);
        }
        $this->info("SJB Updated: {$sjbUpdated} | Skipped/Sama: {$sjbSkipped}");

        if (count($sjbNotFound) > 0) {
            $this->warn("Supir tidak ditemukan di karyawan: " . implode(', ', $sjbNotFound));
        }

        // =============================================
        // SUMMARY
        // =============================================
        $this->newLine();
        $this->info("=== RINGKASAN ===");
        $this->info("Surat Jalan      : {$sjUpdated} di-update, {$sjSkipped} di-skip");
        $this->info("SJ Bongkaran     : {$sjbUpdated} di-update, {$sjbSkipped} di-skip");
        $this->info("Total di-update  : " . ($sjUpdated + $sjbUpdated));

        if ($isDryRun) {
            $this->newLine();
            $this->warn("⚠️  Ini adalah DRY RUN. Tidak ada perubahan yang disimpan.");
            $this->warn("Jalankan tanpa --dry-run untuk menyimpan perubahan:");
            $this->warn("   php artisan update:no-plat-from-karyawan");
        }

        return Command::SUCCESS;
    }
}
