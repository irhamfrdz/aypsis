<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckDivisiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-divisi-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check divisi data and relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $divisis = \App\Models\Divisi::with('karyawans')->get();

        $this->info('Total divisis: ' . $divisis->count());
        $this->newLine();

        $this->info('Divisi details:');
        foreach($divisis as $divisi) {
            $this->line($divisi->nama_divisi . ' (' . $divisi->kode_divisi . '): ' . $divisi->karyawans->count() . ' karyawan');
        }

        $totalKaryawan = $divisis->sum(function($divisi) {
            return $divisi->karyawans->count();
        });

        $this->newLine();
        $this->info('Total karyawan terkait: ' . $totalKaryawan);
    }
}
