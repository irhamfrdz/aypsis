<?php

namespace App\Console\Commands;

use App\Services\OvertimeCheckoutClassifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairOvertimeCheckouts extends Command
{
    protected $signature = 'absensi:repair-overtime-checkouts {--apply : Simpan perbaikan tipe yang terbukti sebagai pulang lembur} {--nik=} {--start=} {--end=}';

    protected $description = 'Periksa scan Pulang setelah sesi lembur terbuka tanpa mengubah timestamp atau pulang normal';

    public function handle(OvertimeCheckoutClassifier $classifier): int
    {
        $earliest = DB::connection()->getDriverName() === 'sqlite'
            ? "DATETIME(a.waktu, '-24 hours')" : 'DATE_SUB(a.waktu, INTERVAL 24 HOUR)';
        $query = DB::table('absensis as a')->select('a.id', 'a.nik', 'a.karyawan_id', 'a.waktu', 'a.tipe')
            ->whereRaw("LOWER(a.tipe) IN ('pulang', 'keluar', 'check out')")
            ->whereExists(function ($query) use ($earliest) {
                $query->selectRaw('1')->from('absensis as s')->whereColumn('s.nik', 'a.nik')
                    ->whereRaw("LOWER(REPLACE(s.tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur')")
                    ->whereColumn('s.waktu', '<', 'a.waktu')->whereRaw('s.waktu >= '.$earliest);
            });
        if ($this->option('nik')) {
            $query->where('a.nik', $this->option('nik'));
        }
        if ($this->option('start')) {
            $query->where('a.waktu', '>=', \Carbon\Carbon::parse($this->option('start'))->startOfDay());
        }
        if ($this->option('end')) {
            $query->where('a.waktu', '<=', \Carbon\Carbon::parse($this->option('end'))->endOfDay());
        }
        $count = 0;
        $query->orderBy('a.id')->chunkById(200, function ($rows) use ($classifier, &$count) {
            foreach ($rows as $row) {
                if ($classifier->resolve($row->nik ?? '', $row->waktu, $row->tipe, $row->karyawan_id) !== 'lembur_pulang') {
                    continue;
                }
                $count++;
                if ($this->option('apply')) {
                    DB::transaction(function () use ($row, $classifier) {
                        $current = DB::table('absensis')->where('id', $row->id)->lockForUpdate()->first();
                        if ($current && \App\Helpers\AttendanceEventType::normalize($current->tipe) === 'Pulang'
                            && $classifier->resolve($current->nik ?? '', $current->waktu, $current->tipe, $current->karyawan_id) === 'lembur_pulang') {
                            // Old imports may contain both Pulang and Lembur_Pulang
                            // at the exact same timestamp. Keep both raw records and
                            // use a supported overtime alias to respect the unique key.
                            $type = collect(['lembur_pulang', 'lembur pulang', 'selesai lembur', 'lembur keluar'])
                                ->first(fn ($type) => ! DB::table('absensis')->where('nik', $current->nik)
                                    ->where('waktu', $current->waktu)->where('tipe', $type)->exists());
                            if ($type === null) {
                                throw new \RuntimeException('Tidak dapat mengoreksi log #'.$row->id.': seluruh alias lembur sudah digunakan pada waktu yang sama.');
                            }
                            \App\Models\Absensi::findOrFail($row->id)->update(['tipe' => $type]);
                        }
                    });
                }
            }
        }, 'a.id', 'id');
        $this->info($count.' scan '.($this->option('apply') ? 'diperbaiki' : 'teridentifikasi (pratinjau)').' sebagai pulang lembur.');

        return self::SUCCESS;
    }
}
