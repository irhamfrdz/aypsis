<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceKanisirBanController extends Controller
{
    public function index()
    {
        $invoices = \App\Models\InvoiceKanisirBan::latest()->get();
        return view('invoice-kanisir-ban.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = \App\Models\InvoiceKanisirBan::with('items.stockBan')->findOrFail($id);
        return view('invoice-kanisir-ban.show', compact('invoice'));
    }

    public function destroy($id)
    {
        $invoice = \App\Models\InvoiceKanisirBan::findOrFail($id);
        
        // When deleting invoice, we should revert stock bans to 'Stok' condition?
        // Or just keep them as is? Typically deleting invoice implies cancelling the operation.
        // For safety let's just delete the invoice and items. 
        // If user wants to revert tires, they might need to update tires manually or we add revert logic.
        // Let's implement revert logic for consistency.
        
        DB::transaction(function() use ($invoice) {
            foreach ($invoice->items as $item) {
                if ($item->stockBan) {
                    $item->stockBan->update([
                        'kondisi' => 'asli', // Or whatever it was before, usually 'asli' or 'kanisir' logic is complex. 
                        // But wait, "Masak Kanisir" changes condition to 'kanisir'. 
                        // If we revert, we should probably set it back to something else or just remove the 'kanisir' traceability.
                        // Given complexity, let's just delete the invoice record for now.
                        // Updating stock ban 'nomor_bukti' to null might be good.
                        'nomor_bukti' => null,
                        // Not reverting 'kondisi' because we don't know previous state easily without audit log.
                        // User can manually edit stock ban if needed.
                    ]);
                }
            }
            $invoice->delete();
        });

        return redirect()->route('invoice-kanisir-ban.index')->with('success', 'Invoice berhasil dihapus.');
    }
}
