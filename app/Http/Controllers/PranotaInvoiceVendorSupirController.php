<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PranotaInvoiceVendorSupir;
use App\Models\InvoiceTagihanVendor;
use App\Models\VendorSupir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PranotaInvoiceVendorSupirController extends Controller
{
    public function index(Request $request)
    {
        $query = PranotaInvoiceVendorSupir::with(['vendor']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('no_pranota', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($q) use ($search) {
                      $q->where('nama_vendor', 'like', "%{$search}%");
                  });
        }
        
        $pranotas = $query->latest()->paginate(10)->withQueryString();
        
        return view('pranota-invoice-vendor-supir.index', compact('pranotas'));
    }

    public function create(Request $request)
    {
        $vendors = VendorSupir::orderBy('nama_vendor')->get();
        $selectedVendor = $request->vendor_id;
        $invoices = collect();
        
        if ($selectedVendor) {
            $invoices = InvoiceTagihanVendor::where('vendor_id', $selectedVendor)
                ->whereNull('pranota_invoice_vendor_supir_id')
                ->orderBy('tanggal_invoice', 'desc')
                ->get();
        }

        // Generate default pranota number: PRANOTA-VS-YYYYMMDD-XXXX
        $today = now()->format('Ymd');
        $countToday = PranotaInvoiceVendorSupir::whereDate('created_at', now()->toDateString())->count();
        $defaultPranotaNo = 'PRANOTA-VS-' . $today . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return view('pranota-invoice-vendor-supir.create', compact('vendors', 'selectedVendor', 'invoices', 'defaultPranotaNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_pranota' => 'required|unique:pranota_invoice_vendor_supirs,no_pranota',
            'vendor_id' => 'required|exists:vendor_supirs,id',
            'tanggal_pranota' => 'required|date',
            'invoice_id' => 'required|array|min:1',
            'invoice_id.*' => 'exists:invoice_tagihan_vendors,id',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            // Calculate total from selected invoices
            $invoices = InvoiceTagihanVendor::whereIn('id', $request->invoice_id)
                ->where('vendor_id', $request->vendor_id)
                ->whereNull('pranota_invoice_vendor_supir_id')
                ->get();
                
            if ($invoices->isEmpty()) {
                return back()->with('error', 'Tidak ada invoice valid yang dipilih.');
            }

            $totalInvoices = $invoices->sum('total_nominal');
            $pph = 0;
            
            if ($request->has('potong_pph')) {
                $pph = $totalInvoices * 0.02;
            }
            
            $grandTotal = $totalInvoices - $pph;

            $pranota = PranotaInvoiceVendorSupir::create([
                'no_pranota' => $request->no_pranota,
                'vendor_id' => $request->vendor_id,
                'tanggal_pranota' => $request->tanggal_pranota,
                'total_nominal' => $grandTotal, // Kurangi total_nominal asli dengan PPH agar masuk ke pembayaran dengan nilai terpotong
                'pph' => $pph,
                'grand_total' => $grandTotal,
                'status_pembayaran' => 'belum_dibayar',
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Update invoices
            InvoiceTagihanVendor::whereIn('id', $invoices->pluck('id'))
                ->update(['pranota_invoice_vendor_supir_id' => $pranota->id]);

            DB::commit();

            return redirect()->route('pranota-invoice-vendor-supir.index')->with('success', 'Pranota berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pranota = PranotaInvoiceVendorSupir::with(['vendor', 'invoiceTagihanVendors.tagihanSupirVendors.suratJalan', 'creator', 'updater'])->findOrFail($id);
        return view('pranota-invoice-vendor-supir.show', compact('pranota'));
    }

    public function edit($id)
    {
        $pranota = PranotaInvoiceVendorSupir::with(['vendor', 'invoiceTagihanVendors'])->findOrFail($id);
        return view('pranota-invoice-vendor-supir.edit', compact('pranota'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:belum_dibayar,sebagian,lunas',
            'keterangan' => 'nullable|string'
        ]);

        $pranota = PranotaInvoiceVendorSupir::findOrFail($id);
        $pranota->update([
            'status_pembayaran' => $request->status_pembayaran,
            'keterangan' => $request->keterangan,
            'updated_by' => Auth::id()
        ]);
        
        // Also update the status of related invoices and tagihans
        InvoiceTagihanVendor::where('pranota_invoice_vendor_supir_id', $pranota->id)
            ->update(['status_pembayaran' => $request->status_pembayaran]);
            
        // Get all invoice IDs to update tagihans
        $invoiceIds = InvoiceTagihanVendor::where('pranota_invoice_vendor_supir_id', $pranota->id)->pluck('id');
        \App\Models\TagihanSupirVendor::whereIn('invoice_tagihan_vendor_id', $invoiceIds)
            ->update(['status_pembayaran' => $request->status_pembayaran]);

        return redirect()->route('pranota-invoice-vendor-supir.index')->with('success', 'Status Pranota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $pranota = PranotaInvoiceVendorSupir::findOrFail($id);
            
            // Release invoices
            InvoiceTagihanVendor::where('pranota_invoice_vendor_supir_id', $pranota->id)
                ->update(['pranota_invoice_vendor_supir_id' => null]);
                
            $pranota->delete();
            DB::commit();
            return redirect()->route('pranota-invoice-vendor-supir.index')->with('success', 'Pranota berhasil dihapus dan invoice dilepaskan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $pranota = PranotaInvoiceVendorSupir::with(['vendor', 'invoiceTagihanVendors.tagihanSupirVendors.suratJalan.prospeks'])->findOrFail($id);
        return view('pranota-invoice-vendor-supir.print', compact('pranota'));
    }

    public function addPph($id)
    {
        try {
            DB::beginTransaction();
            $pranota = PranotaInvoiceVendorSupir::findOrFail($id);
            
            // Calculate 2% of total_nominal (yang pada saat belum di PPH adalah subtotal utuh)
            $pph = $pranota->total_nominal * 0.02;
            $grandTotal = $pranota->total_nominal - $pph;
            
            $pranota->update([
                'pph' => $pph,
                'total_nominal' => $grandTotal, // Simpan total_nominal baru yang sudah dipotong PPH
                'grand_total' => $grandTotal,
                'updated_by' => Auth::id()
            ]);
            
            DB::commit();
            return redirect()->route('pranota-invoice-vendor-supir.index')->with('success', 'PPH 2% berhasil ditambahkan ke Pranota.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
