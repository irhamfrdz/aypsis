<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\InvoiceTagihanVendor;
use App\Models\TagihanSupirVendor;
use App\Models\VendorSupir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceTagihanVendorController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceTagihanVendor::with(['vendor']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('no_invoice', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($q) use ($search) {
                      $q->where('nama_vendor', 'like', "%{$search}%");
                  });
        }
        
        $invoices = $query->latest()->paginate(10)->withQueryString();
        
        return view('invoice-tagihan-vendor.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $vendors = VendorSupir::orderBy('nama_vendor')->get();
        $selectedVendor = $request->vendor_id;
        $tagihans = collect();
        
        if ($selectedVendor) {
            $tagihans = TagihanSupirVendor::with('suratJalan')
                ->where('vendor_id', $selectedVendor)
                ->whereNull('invoice_tagihan_vendor_id')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Generate default invoice number: INV-TSV-YYYYMMDD-XXXX
        $today = now()->format('Ymd');
        $countToday = InvoiceTagihanVendor::whereDate('created_at', now()->toDateString())->count();
        $defaultInvoiceNo = 'INV-TSV-' . $today . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return view('invoice-tagihan-vendor.create', compact('vendors', 'selectedVendor', 'tagihans', 'defaultInvoiceNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_invoice' => 'required|unique:invoice_tagihan_vendors,no_invoice',
            'vendor_id' => 'required|exists:vendor_supirs,id',
            'tanggal_invoice' => 'required|date',
            'tagihan_id' => 'required|array|min:1',
            'tagihan_id.*' => 'exists:tagihan_supir_vendors,id',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            // Calculate total from selected tagihans
            $tagihans = TagihanSupirVendor::whereIn('id', $request->tagihan_id)
                ->where('vendor_id', $request->vendor_id)
                ->whereNull('invoice_tagihan_vendor_id')
                ->get();
                
            if ($tagihans->isEmpty()) {
                return back()->with('error', 'Tidak ada tagihan valid yang dipilih.');
            }

            $totalNominal = $tagihans->sum(function($t) {
                return $t->nominal + ($t->adjustment ?? 0);
            });

            $invoice = InvoiceTagihanVendor::create([
                'no_invoice' => $request->no_invoice,
                'vendor_id' => $request->vendor_id,
                'tanggal_invoice' => $request->tanggal_invoice,
                'total_nominal' => $totalNominal,
                'status_pembayaran' => 'belum_dibayar',
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Update tagihans
            TagihanSupirVendor::whereIn('id', $tagihans->pluck('id'))
                ->update(['invoice_tagihan_vendor_id' => $invoice->id]);

            DB::commit();

            return redirect()->route('invoice-tagihan-vendor.index')->with('success', 'Invoice berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = InvoiceTagihanVendor::with(['vendor', 'tagihanSupirVendors.suratJalan', 'creator', 'updater'])->findOrFail($id);
        return view('invoice-tagihan-vendor.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = InvoiceTagihanVendor::with(['vendor', 'tagihanSupirVendors'])->findOrFail($id);
        return view('invoice-tagihan-vendor.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:belum_dibayar,sebagian,lunas',
            'keterangan' => 'nullable|string'
        ]);

        $invoice = InvoiceTagihanVendor::findOrFail($id);
        $invoice->update([
            'status_pembayaran' => $request->status_pembayaran,
            'keterangan' => $request->keterangan,
            'updated_by' => Auth::id()
        ]);
        
        // Also update the status of related tagihans based on invoice
        TagihanSupirVendor::where('invoice_tagihan_vendor_id', $invoice->id)
            ->update(['status_pembayaran' => $request->status_pembayaran]);

        return redirect()->route('invoice-tagihan-vendor.index')->with('success', 'Status Invoice berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $invoice = InvoiceTagihanVendor::findOrFail($id);
            
            // Release tagihans
            TagihanSupirVendor::where('invoice_tagihan_vendor_id', $invoice->id)
                ->update(['invoice_tagihan_vendor_id' => null, 'status_pembayaran' => 'belum_dibayar']);
                
            $invoice->delete();
            DB::commit();
            return redirect()->route('invoice-tagihan-vendor.index')->with('success', 'Invoice berhasil dihapus dan tagihan dilepaskan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
