<?php

namespace App\Http\Controllers;

use App\Models\InvoiceKontainerSewa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceKontainerSewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InvoiceKontainerSewa::with(['createdBy', 'approvedBy', 'items.tagihan']);

        // Filter by nomor invoice
        if ($request->filled('nomor_invoice')) {
            $query->where('nomor_invoice', 'like', '%' . $request->nomor_invoice . '%');
        }

        // Filter by vendor
        if ($request->filled('vendor')) {
            $query->where('vendor_name', 'like', '%' . $request->vendor . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('invoice-tagihan-sewa.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get available tagihan that haven't been invoiced yet
        $availableTagihan = \App\Models\DaftarTagihanKontainerSewa::whereNull('invoice_id')
            ->with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('invoice-tagihan-sewa.create', compact('availableTagihan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_invoice' => 'required|string|unique:invoices_kontainer_sewa,nomor_invoice',
            'tanggal_invoice' => 'required|date',
            'vendor_name' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pph' => 'nullable|numeric|min:0',
            'adjustment' => 'nullable|numeric',
            'total' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,submitted,approved,paid,cancelled',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'tagihan_ids' => 'nullable|array',
            'tagihan_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id',
        ]);

        \DB::beginTransaction();
        try {
            $invoice = InvoiceKontainerSewa::create([
                'nomor_invoice' => $request->nomor_invoice,
                'tanggal_invoice' => $request->tanggal_invoice,
                'vendor_name' => $request->vendor_name,
                'subtotal' => $request->subtotal ?? 0,
                'ppn' => $request->ppn ?? 0,
                'pph' => $request->pph ?? 0,
                'adjustment' => $request->adjustment ?? 0,
                'total' => $request->total,
                'status' => $request->status ?? 'draft',
                'keterangan' => $request->keterangan,
                'catatan' => $request->catatan,
                'created_by' => auth()->id(),
            ]);

            // Attach tagihan items if provided
            if ($request->has('tagihan_ids') && is_array($request->tagihan_ids)) {
                foreach ($request->tagihan_ids as $tagihanId) {
                    $tagihan = \App\Models\DaftarTagihanKontainerSewa::find($tagihanId);
                    if ($tagihan) {
                        $invoice->items()->create([
                            'tagihan_id' => $tagihanId,
                            'jumlah' => $tagihan->grand_total ?? 0,
                        ]);
                        
                        // Update tagihan with invoice_id
                        $tagihan->update(['invoice_id' => $invoice->id]);
                    }
                }
            }

            \DB::commit();
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice berhasil dibuat',
                    'invoice' => $invoice
                ]);
            }
            
            return redirect()->route('invoice-tagihan-sewa.index')
                ->with('success', 'Invoice berhasil dibuat.');
        } catch (\Exception $e) {
            \DB::rollback();
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat invoice: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = InvoiceKontainerSewa::with(['createdBy', 'approvedBy', 'items.tagihan'])
            ->findOrFail($id);

        return view('invoice-tagihan-sewa.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = InvoiceKontainerSewa::with(['items.tagihan'])
            ->findOrFail($id);

        $availableTagihan = \App\Models\DaftarTagihanKontainerSewa::whereNull('invoice_id')
            ->orWhere('invoice_id', $id)
            ->with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('invoice-tagihan-sewa.edit', compact('invoice', 'availableTagihan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = InvoiceKontainerSewa::findOrFail($id);

        $request->validate([
            'nomor_invoice' => 'required|string|unique:invoices_kontainer_sewa,nomor_invoice,' . $id,
            'tanggal_invoice' => 'required|date',
            'vendor_name' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pph' => 'nullable|numeric|min:0',
            'adjustment' => 'nullable|numeric',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:draft,submitted,approved,paid,cancelled',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        \DB::beginTransaction();
        try {
            $invoice->update([
                'nomor_invoice' => $request->nomor_invoice,
                'tanggal_invoice' => $request->tanggal_invoice,
                'vendor_name' => $request->vendor_name,
                'subtotal' => $request->subtotal ?? 0,
                'ppn' => $request->ppn ?? 0,
                'pph' => $request->pph ?? 0,
                'adjustment' => $request->adjustment ?? 0,
                'total' => $request->total,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'catatan' => $request->catatan,
            ]);

            \DB::commit();
            return redirect()->route('invoice-tagihan-sewa.index')
                ->with('success', 'Invoice berhasil diupdate.');
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Gagal update invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $invoice = InvoiceKontainerSewa::findOrFail($id);
            
            \DB::beginTransaction();
            
            // Remove invoice_id from related tagihan
            \App\Models\DaftarTagihanKontainerSewa::where('invoice_id', $id)
                ->update(['invoice_id' => null]);
            
            $invoice->delete();
            
            \DB::commit();
            
            return redirect()->route('invoice-tagihan-sewa.index')
                ->with('success', 'Invoice berhasil dihapus.');
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Gagal menghapus invoice: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete invoices
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:invoices_kontainer_sewa,id',
        ]);

        \DB::beginTransaction();
        try {
            $count = 0;
            foreach ($request->ids as $id) {
                $invoice = InvoiceKontainerSewa::find($id);
                if ($invoice) {
                    // Remove invoice_id from related tagihan
                    \App\Models\DaftarTagihanKontainerSewa::where('invoice_id', $id)
                        ->update(['invoice_id' => null]);
                    
                    $invoice->delete();
                    $count++;
                }
            }

            \DB::commit();

            return redirect()->route('invoice-tagihan-sewa.index')
                ->with('success', "Berhasil menghapus {$count} invoice.");
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Gagal menghapus invoice: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor pranota dengan format PMS-MMYY-XXXXXX
     */
    private function generateNomorPranota()
    {
        return \DB::transaction(function () {
            // Lock record untuk mencegah race condition
            $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'PMS')
                ->lockForUpdate()
                ->first();

            if (!$nomorTerakhir) {
                $nomorTerakhir = \App\Models\NomorTerakhir::create([
                    'modul' => 'PMS',
                    'nomor_terakhir' => 1,
                    'keterangan' => 'Pranota Kontainer Sewa'
                ]);
                $nomorBaru = 1;
            } else {
                $nomorBaru = $nomorTerakhir->nomor_terakhir + 1;
                $nomorTerakhir->update(['nomor_terakhir' => $nomorBaru]);
            }

            // Format: PMSMMYYXXXXXX
            $kodeModul = 'PMS'; // 3 digit kode
            $bulan = date('m'); // 2 digit bulan
            $tahun = date('y'); // 2 digit tahun
            $nomorUrut = str_pad($nomorBaru, 6, '0', STR_PAD_LEFT); // 6 digit nomor

            return $kodeModul . $bulan . $tahun . $nomorUrut;
        });
    }

    /**
     * Get invoice details for pranota modal
     */
    public function details(Request $request)
    {
        try {
            $request->validate([
                'invoice_ids' => 'required|array',
                'invoice_ids.*' => 'exists:invoices_kontainer_sewa,id',
            ]);

            $invoices = InvoiceKontainerSewa::with(['items.tagihan'])
                ->whereIn('id', $request->invoice_ids)
                ->get();

            $result = [];
            foreach ($invoices as $invoice) {
                $items = [];
                
                if ($invoice->items) {
                    foreach ($invoice->items as $item) {
                        $tagihan = $item->tagihan ?? null;
                        
                        if ($tagihan) {
                            // Use vendor_name from invoice or tagihan
                            $vendorName = $invoice->vendor_name ?? '-';
                            
                            $items[] = [
                                'id' => $item->id,
                                'tagihan_id' => $tagihan->id,
                                'vendor_name' => $vendorName,
                                'invoice_vendor' => $tagihan->invoice_vendor ?? '-',
                                'nomor_kontainer' => $tagihan->nomor_kontainer ?? '-',
                                'size' => $tagihan->size ?? '-',
                                'periode' => $tagihan->periode ?? '-',
                                'grand_total' => $tagihan->grand_total ?? 0,
                            ];
                        }
                    }
                }

                $result[] = [
                    'id' => $invoice->id,
                    'nomor_invoice' => $invoice->nomor_invoice ?? '-',
                    'tanggal_invoice' => $invoice->tanggal_invoice ? $invoice->tanggal_invoice->format('d/m/Y') : '-',
                    'vendor_name' => $invoice->vendor_name ?? '-',
                    'total' => $invoice->total ?? 0,
                    'items' => $items,
                ];
            }

            // Generate nomor pranota
            $nomorPranota = $this->generateNomorPranota();

            return response()->json([
                'success' => true,
                'invoices' => $result,
                'nomor_pranota' => $nomorPranota,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()['invoice_ids'] ?? ['Data tidak valid']),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in invoice details: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create pranota directly from selected invoices
     */
    public function storePranotaFromInvoice(Request $request)
    {
        try {
            $request->validate([
                'invoice_ids' => 'required|array',
                'invoice_ids.*' => 'exists:invoices_kontainer_sewa,id',
                'nomor_pranota' => 'required|string',
            ]);

            \DB::beginTransaction();

            // Get invoices with items
            $invoices = InvoiceKontainerSewa::with(['items.tagihan'])
                ->whereIn('id', $request->invoice_ids)
                ->get();

            if ($invoices->isEmpty()) {
                throw new \Exception('Invoice tidak ditemukan');
            }
            
            // Calculate totals and collect tagihan IDs
            $totalAmount = 0;
            $tagihanIds = [];
            
            foreach ($invoices as $invoice) {
                $totalAmount += $invoice->total ?? 0;
                
                if ($invoice->items) {
                    foreach ($invoice->items as $item) {
                        if ($item->tagihan) {
                            $tagihanIds[] = $item->tagihan->id;
                        }
                    }
                }
            }

            // Get first invoice number for reference
            $noInvoice = $request->nomor_pranota;

            // Calculate due date (30 days from now)
            $tanggalPranota = now();
            $dueDate = now()->addDays(30);

            // Create pranota using PranotaTagihanKontainerSewa model
            $pranota = \App\Models\PranotaTagihanKontainerSewa::create([
                'no_invoice' => $noInvoice,
                'total_amount' => $totalAmount,
                'tagihan_kontainer_sewa_ids' => $tagihanIds, // Will be cast to JSON automatically
                'jumlah_tagihan' => count($tagihanIds),
                'tanggal_pranota' => $tanggalPranota->format('Y-m-d'),
                'due_date' => $dueDate->format('Y-m-d'),
                'status' => 'unpaid',
                'keterangan' => 'Pranota dibuat dari ' . count($request->invoice_ids) . ' invoice',
            ]);

            // Update invoices with pranota_id
            InvoiceKontainerSewa::whereIn('id', $request->invoice_ids)
                ->update([
                    'pranota_id' => $pranota->id,
                ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pranota berhasil dibuat',
                'pranota' => [
                    'id' => $pranota->id,
                    'no_invoice' => $pranota->no_invoice,
                    'nomor_pranota' => $pranota->no_invoice,
                ],
                'redirect_url' => route('pranota-kontainer-sewa.show', $pranota->id),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_map(fn($errors) => implode(', ', $errors), $e->errors())),
            ], 422);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Error creating pranota from invoice: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pranota: ' . $e->getMessage(),
            ], 500);
        }
    }
}
