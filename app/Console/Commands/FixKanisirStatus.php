<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InvoiceKanisirBan;

class FixKanisirStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kanisir:fix-status {invoice_number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status of bans in a Kanisir Invoice to "Sedang Dimasak"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceNumber = $this->argument('invoice_number');
        $invoice = InvoiceKanisirBan::with('items.stockBan')->where('nomor_invoice', $invoiceNumber)->first();

        if (!$invoice) {
            $this->error("Invoice {$invoiceNumber} not found.");
            return 1;
        }

        $this->info("Processing Invoice: {$invoice->nomor_invoice}");
        
        $count = 0;
        foreach ($invoice->items as $item) {
            if ($item->stockBan) {
                $currentStatus = $item->stockBan->status;
                $item->stockBan->update(['status' => 'Sedang Dimasak']);
                $this->line("- Ban ID {$item->stockBan->id} ({$item->stockBan->nomor_seri}): {$currentStatus} -> Sedang Dimasak");
                $count++;
            }
        }

        $this->info("Successfully updated {$count} bans to status 'Sedang Dimasak'.");
        return 0;
    }
}
