<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use Illuminate\Console\Command;

class FixAbsensiMesin extends Command
{
    protected $signature = 'absensi:fix-mesin {from} {to}';

    protected $description = 'Memindahkan data absensi yang salah kamar akibat bug sync lokal';

    public function handle()
    {
        $from = $this->argument('from');
        $to = $this->argument('to');

        $count = Absensi::where('mesin_id', $from)
            ->where(function ($q) {
                $q->where('keterangan', 'like', '%Sinkronisasi database lokal%')
                    ->orWhere('keterangan', 'like', '%Auto-sync database lokal%');
            })
            ->update([
                'mesin_id' => $to,
                'keterangan' => 'Pindahan dari mesin ID '.$from,
            ]);

        $this->info("Berhasil memindahkan {$count} data absensi dari Mesin {$from} ke Mesin {$to}!");
    }
}
