<?php

namespace App\Console\Commands;

use App\Models\Mobil;
use Illuminate\Console\Command;

class UpdateRodaMobil extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mobil:update-roda {--all : Update semua kendaraan meskipun kolom roda sudah terisi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update nilai kolom roda pada mobil berdasarkan jenis kendaraan (Sepeda Motor: 2, Tractor Head: 6, Buntut 20 Feet: 8, Buntut 40 Feet: 12)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembaruan data Roda kendaraan...');

        $updateAll = $this->option('all');

        $rules = [
            [
                'label' => 'SEPEDA MOTOR',
                'roda' => 2,
                'condition' => function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('jenis', 'SEPEDA MOTOR')
                            ->orWhere('jenis', 'LIKE', 'MOTOR%');
                    });
                },
            ],
            [
                'label' => 'TRACTOR HEAD',
                'roda' => 6,
                'condition' => function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('jenis', 'TRACTOR HEAD')
                            ->orWhere('jenis', 'TRACKTOR HEAD')
                            ->orWhere('jenis', 'LIKE', '%TRACTOR HEAD%')
                            ->orWhere('jenis', 'LIKE', '%TRACKTOR HEAD%');
                    });
                },
            ],
            [
                'label' => 'BUNTUT 20 FEET',
                'roda' => 8,
                'condition' => function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('jenis', 'BUNTUT 20 FEET')
                            ->orWhere('jenis', 'LIKE', '%BUNTUT 20%');
                    });
                },
            ],
            [
                'label' => 'BUNTUT 40 FEET',
                'roda' => 12,
                'condition' => function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('jenis', 'BUNTUT 40 FEET')
                            ->orWhere('jenis', 'LIKE', '%BUNTUT 40%');
                    });
                },
            ],
        ];

        $totalUpdated = 0;

        foreach ($rules as $rule) {
            $query = Mobil::query();

            // Apply jenis condition
            $rule['condition']($query);

            if (! $updateAll) {
                // Hanya update yang belum memiliki data roda
                $query->where(function ($sub) {
                    $sub->whereNull('roda')->orWhere('roda', 0);
                });
            }

            $count = $query->count();

            if ($count > 0) {
                $affected = $query->update(['roda' => $rule['roda']]);
                $totalUpdated += $affected;
                $this->line("  ✓ <comment>{$rule['label']}</comment> -> Diatur ke <info>{$rule['roda']} RODA</info> ({$affected} kendaraan)");
            } else {
                $this->line("  - <comment>{$rule['label']}</comment> -> Tidak ada data yang perlu diupdate (sudah terisi atau data kosong)");
            }
        }

        $this->newLine();
        $this->info("Pembaruan selesai! Total kendaraan diperbarui: {$totalUpdated}");

        return Command::SUCCESS;
    }
}
