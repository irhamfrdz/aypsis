<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillTagihanObMobilId extends Command
{
    protected $signature = 'tagihan-ob:backfill-mobil-id
        {--apply : Simpan hasil pencocokan; tanpa opsi ini hanya pratinjau}
        {--match-driver : Jika plat kosong, gunakan satu unit yang terhubung ke nama supir saat ini}';

    protected $description = 'Isi mobil_id tagihan OB yang kosong berdasarkan plat, dengan opsi pencocokan supir';

    public function handle(): int
    {
        $normalizePlate = fn ($value) => preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $value));
        $normalizeName = fn ($value) => mb_strtoupper(preg_replace('/\s+/u', ' ', trim((string) $value)));
        $vehicles = DB::table('mobils')->get(['id', 'nomor_polisi', 'karyawan_id']);
        $byPlate = $vehicles->filter(fn ($vehicle) => $normalizePlate($vehicle->nomor_polisi) !== '')
            ->groupBy(fn ($vehicle) => $normalizePlate($vehicle->nomor_polisi));
        $byDriver = [];
        if ($this->option('match-driver')) {
            $drivers = DB::table('karyawans')->get(['id', 'nama_lengkap', 'nama_panggilan'])->keyBy('id');
            foreach ($vehicles as $vehicle) {
                $driver = $drivers->get($vehicle->karyawan_id);
                foreach ([$driver?->nama_lengkap, $driver?->nama_panggilan] as $name) {
                    if ($key = $normalizeName($name)) {
                        $byDriver[$key][$vehicle->id] = $vehicle;
                    }
                }
            }
            $this->warn('Pencocokan supir memakai penugasan kendaraan saat ini, bukan riwayat penugasan. Tinjau pratinjau sebelum --apply.');
        }

        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'Mode SIMPAN — hanya mobil_id kosong yang diisi.' : 'Mode PRATINJAU — tidak ada data diubah.');
        $matched = $updated = $skipped = 0;
        DB::table('tagihan_ob')->whereNull('mobil_id')->orderBy('id')
            ->chunkById(500, function ($rows) use ($normalizePlate, $normalizeName, $byPlate, $byDriver, $apply, &$matched, &$updated, &$skipped) {
                foreach ($rows as $row) {
                    $plate = $normalizePlate($row->nomor_polisi);
                    $method = 'plat';
                    $candidates = $byPlate->get($plate, collect());
                    // Never override an unmatched or ambiguous recorded plate with the current driver assignment.
                    if ($plate === '' && $this->option('match-driver')) {
                        $method = 'supir';
                        $candidates = collect($byDriver[$normalizeName($row->nama_supir)] ?? []);
                    }
                    if ($candidates->count() !== 1) {
                        $skipped++;
                        $this->line('LEWATI #'.$row->id.': '.($candidates->count() > 1 ? 'lebih dari satu unit cocok' : 'unit tidak ditemukan').'.');
                        continue;
                    }
                    $vehicle = $candidates->first();
                    $matched++;
                    $this->line("COCOK #{$row->id} → mobil_id {$vehicle->id} ({$vehicle->nomor_polisi}), via {$method}");
                    if ($apply) {
                        // Conditional update also protects against another process filling this ID during the run.
                        $updated += DB::table('tagihan_ob')->where('id', $row->id)->whereNull('mobil_id')
                            ->update(['mobil_id' => $vehicle->id]);
                    }
                }
            });
        $this->table(['Cocok', 'Disimpan', 'Dilewati'], [[$matched, $updated, $skipped]]);
        if (! $apply) {
            $this->info('Tambahkan --apply untuk menyimpan setelah hasil pratinjau ditinjau.');
        }

        return self::SUCCESS;
    }
}
