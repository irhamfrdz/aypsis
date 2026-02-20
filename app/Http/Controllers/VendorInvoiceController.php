<?php

namespace App\Http\Controllers;

use App\Models\VendorInvoice;
use App\Models\VendorKontainerSewa;
use Illuminate\Http\Request;

class VendorInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = VendorInvoice::with('vendor');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_invoice', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($v) use ($search) {
                      $v->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest()->paginate(10);

        return view('vendor-invoice.index', compact('invoices'));
    }

    public function create()
    {
        $vendors = VendorKontainerSewa::where('status', 'ACTIVE')->get();
        return view('vendor-invoice.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendor_kontainer_sewas,id',
            'no_invoice' => 'required|string|unique:vendor_invoices,no_invoice',
            'tgl_invoice' => 'required|date',
            'total_dpp' => 'required|numeric|min:0',
            'total_ppn' => 'required|numeric|min:0',
            'total_pph23' => 'required|numeric|min:0',
            'total_materai' => 'nullable|numeric|min:0',
            'total_netto' => 'required|numeric|min:0',
        ]);

        try {
            VendorInvoice::create($validated);
            return redirect()->route('vendor-invoice.index')->with('success', 'Invoice berhasil dicatat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mencatat invoice: ' . $e->getMessage());
        }
    }

    public function show(VendorInvoice $vendorInvoice)
    {
        $vendorInvoice->load('vendor');
        return view('vendor-invoice.show', compact('vendorInvoice'));
    }

    public function edit(VendorInvoice $vendorInvoice)
    {
        $vendors = VendorKontainerSewa::where('status', 'ACTIVE')->get();
        return view('vendor-invoice.edit', compact('vendorInvoice', 'vendors'));
    }

    public function update(Request $request, VendorInvoice $vendorInvoice)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendor_kontainer_sewas,id',
            'no_invoice' => 'required|string|unique:vendor_invoices,no_invoice,' . $vendorInvoice->id,
            'tgl_invoice' => 'required|date',
            'total_dpp' => 'required|numeric|min:0',
            'total_ppn' => 'required|numeric|min:0',
            'total_pph23' => 'required|numeric|min:0',
            'total_materai' => 'nullable|numeric|min:0',
            'total_netto' => 'required|numeric|min:0',
        ]);

        try {
            $vendorInvoice->update($validated);
            return redirect()->route('vendor-invoice.index')->with('success', 'Invoice berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui invoice: ' . $e->getMessage());
        }
    }

    public function destroy(VendorInvoice $vendorInvoice)
    {
        try {
            $vendorInvoice->delete();
            return redirect()->route('vendor-invoice.index')->with('success', 'Invoice berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus invoice: ' . $e->getMessage());
        }
    }
}
