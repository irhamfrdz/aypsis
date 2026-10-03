<?php

namespace App\Console\Commands;

use App\Helpers\AttendanceWorkDate;
use App\Models\PranotaLemburKaryawanHeader;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillPranotaLemburDates extends Command
{
    protected $signature = 'pranota-lembur:backfill-dates {header_id?}';

    protected $description = 'Backfill tanggal_lembur for Pranota Lembur Karyawan';

    public function handle()
    {
        $headerId = $this->argument('header_id');
        $query = PranotaLemburKaryawanHeader::with('karyawans');

        if ($headerId) {
            $query->where('id', $headerId);
        } else {
            $query->whereHas('karyawans', function ($q) {
                $q->whereNull('tanggal_lembur');
            });
        }

        $headers = $query->get();
        $this->info("Found {$headers->count()} pranota headers to check.");

        $driver = DB::connection()->getDriverName();
        $workDateExprAlias = AttendanceWorkDate::sql($driver, 'a');

        $lemburStartsSub = "LOWER(REPLACE(a.tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur')";
        $lemburEndsSub = "LOWER(REPLACE(a.tipe, '_', ' ')) IN ('lembur pulang', 'selesai lembur', 'lembur keluar')";
        $lemburStartsOut = "LOWER(REPLACE(sub.tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur')";
        $lemburEndsOut = "LOWER(REPLACE(sub.tipe, '_', ' ')) IN ('lembur pulang', 'selesai lembur', 'lembur keluar')";

        foreach ($headers as $header) {
            $this->info("Processing Pranota: {$header->nomor_pranota} (ID: {$header->id})");

            $startDate = $header->periode_mulai
                ? Carbon::parse($header->periode_mulai)
                : Carbon::parse($header->tanggal_pranota)->startOfMonth();

            $endDate = $header->periode_selesai
                ? Carbon::parse($header->periode_selesai)
                : Carbon::parse($header->tanggal_pranota)->endOfMonth();

            $karyawanIds = $header->karyawans->pluck('karyawan_id')->unique()->toArray();

            $workStart = $startDate->copy()->startOfDay();
            $workEnd = $endDate->copy()->addDays(2)->startOfDay();

            $inner = DB::table(DB::raw('absensis a'))
                ->selectRaw("a.karyawan_id, a.tipe, a.waktu, ($workDateExprAlias) as tanggal")
                ->whereIn('a.karyawan_id', $karyawanIds)
                ->where('a.waktu', '>=', $workStart)
                ->where('a.waktu', '<', $workEnd)
                ->whereRaw("($lemburStartsSub OR $lemburEndsSub)");

            $attendance = DB::table(DB::raw("({$inner->toSql()}) as sub"))
                ->mergeBindings($inner)
                ->selectRaw("
                    sub.karyawan_id,
                    sub.tanggal,
                    MIN(CASE WHEN $lemburStartsOut THEN sub.waktu ELSE NULL END) as waktu_lembur_masuk,
                    MAX(CASE WHEN $lemburEndsOut THEN sub.waktu ELSE NULL END) as waktu_lembur_pulang
                ")
                ->whereBetween('sub.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('sub.karyawan_id', 'sub.tanggal')
                ->get()
                ->groupBy('karyawan_id');

            $updatedCount = 0;
            foreach ($header->karyawans as $item) {
                if (empty($item->tanggal_lembur)) {
                    $logs = $attendance->get($item->karyawan_id) ?? collect();
                    $dates = $logs->pluck('tanggal')->unique()->filter()->values()->toArray();
                    sort($dates);

                    $item->tanggal_lembur = $dates;
                    $item->save();
                    $updatedCount++;
                }
            }

            $this->info("Updated {$updatedCount} karyawans for header {$header->nomor_pranota}.");
        }

        $this->info('Backfill complete!');

        return 0;
    }
}
