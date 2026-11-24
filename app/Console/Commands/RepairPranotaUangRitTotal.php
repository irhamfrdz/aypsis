<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairPranotaUangRitTotal extends Command
{
    protected $signature = 'pranota-uang-rit:repair-total
        {no_pranota=PUR-11-26-000049 : Nomor pranota yang akan diperiksa}
        {--apply : Simpan hasil perhitungan ke database}';

    protected $description = 'Samakan total header pranota uang rit dengan rincian per supir';

    public function handle(): int
    {
        $nomor = (string) $this->argument('no_pranota');

        return DB::transaction(function () use ($nomor) {
            $pranota = DB::table('pranota_uang_rits')
                ->where('no_pranota', $nomor)
                ->lockForUpdate()
                ->first();

            if (! $pranota) {
                $this->error("Pranota {$nomor} tidak ditemukan.");

                return self::FAILURE;
            }

            if ($pranota->status !== 'draft') {
                $this->error("Pranota {$nomor} berstatus {$pranota->status}. Perintah ini hanya memperbaiki pranota draft.");

                return self::FAILURE;
            }

            $details = DB::table('pranota_uang_rit_supir_details')
                ->where('no_pranota', $nomor)
                ->lockForUpdate()
                ->get();

            if ($details->isEmpty()) {
                $this->error("Rincian supir untuk {$nomor} tidak ditemukan.");

                return self::FAILURE;
            }

            $totals = [
                'total_uang' => $details->sum('total_uang_supir'),
                'total_hutang' => $details->sum('hutang'),
                'total_tabungan' => $details->sum('tabungan'),
                'total_bpjs' => $details->sum('bpjs'),
                'total_adjustment' => $details->sum('adjustment'),
            ];
            $totals['grand_total_bersih'] = $totals['total_uang'] - $totals['total_hutang']
                - $totals['total_tabungan'] - $totals['total_bpjs'] + $totals['total_adjustment'];

            $this->table(['Komponen', 'Header saat ini', 'Hasil rincian'], collect($totals)
                ->map(fn ($value, $field) => [
                    $field,
                    number_format((float) $pranota->{$field}, 2, ',', '.'),
                    number_format((float) $value, 2, ',', '.'),
                ])->values()->all());

            if (! $this->option('apply')) {
                $this->info('Pratinjau saja. Jalankan lagi dengan --apply untuk menyimpan.');

                return self::SUCCESS;
            }

            DB::table('pranota_uang_rits')->where('id', $pranota->id)->update([
                ...$totals,
                'updated_at' => now(),
            ]);

            $this->info("Total header {$nomor} berhasil disamakan dengan rincian supir.");

            return self::SUCCESS;
        });
    }
}
