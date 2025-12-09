<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PranotaOb;

class DumpPranotaOb extends Command
{
    protected $signature = 'pranota-ob:dump {identifier}';
    protected $description = 'Dump a Pranota OB with id or nomor_pranota; print raw and enriched items';

    public function handle()
    {
        $idOrNomor = $this->argument('identifier');

        $pranota = PranotaOb::where('id', $idOrNomor)->orWhere('nomor_pranota', $idOrNomor)->first();
        if (!$pranota) {
            $this->error('Pranota not found: ' . $idOrNomor);
            return 1;
        }

        $this->line('Pranota: ' . $pranota->id . ' | ' . $pranota->nomor_pranota);
        $this->line('Kapal: ' . $pranota->nama_kapal . ' | Voyage: ' . $pranota->no_voyage . ' | Created by: ' . $pranota->created_by);
        $this->line('Raw items stored:');
        $this->line(json_encode($pranota->items, JSON_PRETTY_PRINT));
        $this->line('Enriched items from model:');
        $this->line(json_encode($pranota->getEnrichedItems(), JSON_PRETTY_PRINT));

        return 0;
    }
}
