<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InvoiceKanisirBan;
use App\Models\InvoiceKanisirBanItem;
use Illuminate\Support\Facades\DB;

class FixKanisirInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kanisir:fix-price {invoice_number} {correct_unit_price}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix price for Kanisir Invoice items and update total';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceNumber = $this->argument('invoice_number');
        $unitPrice = (float) $this->argument('correct_unit_price');

        $invoice = InvoiceKanisirBan::where('nomor_invoice', $invoiceNumber)->first();

        if (!$invoice) {
            $this->error("Invoice with number {$invoiceNumber} not found.");
            return 1;
        }

        $this->info("Found Invoice: {$invoice->nomor_invoice}");
        $this->info("Current Total: " . number_format($invoice->total_biaya, 0, ',', '.'));
        $this->info("Items Count: " . ($invoice->jumlah_ban ?? $invoice->items()->count()));

        DB::transaction(function () use ($invoice, $unitPrice) {
            $items = InvoiceKanisirBanItem::with('stockBan')->where('invoice_kanisir_ban_id', $invoice->id)->get();
            $count = $items->count();
            
            $newTotal = $count * $unitPrice;

            foreach ($items as $item) {
                // Update item price
                $item->harga = $unitPrice;
                $item->save();

                // Update stock ban price if linked
                if ($item->stockBan) {
                    $item->stockBan->harga_beli = $unitPrice;
                    $item->stockBan->save();
                    $this->info("Updated Stock Ban ID {$item->stockBan->id} price to " . number_format($unitPrice, 0, ',', '.'));
                }
            }

            // Update Invoice Header
            $invoice->total_biaya = $newTotal;
            $invoice->save();

            $this->info("Updated {$count} items to unit price: " . number_format($unitPrice, 0, ',', '.'));
            $this->info("Updated Invoice Total to: " . number_format($newTotal, 0, ',', '.'));
        });

        return 0;
    }
}
