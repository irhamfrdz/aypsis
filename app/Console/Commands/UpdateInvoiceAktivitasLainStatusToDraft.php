<?php

namespace App\Console\Commands;

use App\Models\InvoiceAktivitasLain;
use Illuminate\Console\Command;

class UpdateInvoiceAktivitasLainStatusToDraft extends Command
{
    protected $signature = 'fix:ial-status-to-draft';

    protected $description = 'Update status dari paid menjadi draft untuk invoice IAL-09-26-000050, IAL-09-26-000051, IAL-09-26-000052';

    public function handle()
    {
        $nomorInvoices = [
            'IAL-09-26-000050',
            'IAL-09-26-000051',
            'IAL-09-26-000052',
        ];

        $this->info('Mencari invoice...');

        $invoices = InvoiceAktivitasLain::whereIn('nomor_invoice', $nomorInvoices)->get();

        if ($invoices->isEmpty()) {
            $this->error('Tidak ada invoice yang ditemukan!');
            return 1;
        }

        $this->table(
            ['ID', 'Nomor Invoice', 'Status Saat Ini'],
            $invoices->map(fn ($inv) => [$inv->id, $inv->nomor_invoice, $inv->status])->toArray()
        );

        $updated = 0;
        foreach ($invoices as $invoice) {
            if ($invoice->status !== 'paid') {
                $this->warn("Skip {$invoice->nomor_invoice} — status saat ini: {$invoice->status} (bukan paid)");
                continue;
            }

            $invoice->status = 'draft';
            $invoice->save();
            $updated++;
            $this->info("✓ {$invoice->nomor_invoice} berhasil diubah dari paid → draft");
        }

        $this->newLine();
        $this->info("Selesai. {$updated} invoice diupdate.");

        return 0;
    }
}
