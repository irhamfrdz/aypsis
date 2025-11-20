<?php

namespace App\Http\Controllers;

use App\Models\PranotaTagihanKontainerSewa;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PranotaTagihanKontainerSewaController extends Controller
{
    /**
     * Display a listing of pranota kontainer sewa
     */
    public function index(Request $request)
    {
        $query = PranotaTagihanKontainerSewa::orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('no_invoice', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }

        // Get per_page from request or use default
        $perPage = $request->get('per_page', 15);
        
        // Validate per_page value
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;

        // Handle AJAX request for existing pranota selection
        if ($request->ajax() || $request->wantsJson()) {
            $pranotaList = $query->paginate($perPage);
            
            // Enrich pranota data with calculated fields
            $enrichedPranota = $pranotaList->items();
            foreach ($enrichedPranota as $pranota) {
                // Generate nomor pranota if not set
                if (empty($pranota->no_invoice)) {
                    $pranota->generateNomorPranota();
                }
                
                // Calculate actual total amount if not set
                if (!$pranota->total_amount || $pranota->total_amount == 0) {
                    $pranota->total_amount = $pranota->calculateTotalAmount();
                }
                
                // Calculate number of items/containers
                if (is_array($pranota->tagihan_kontainer_sewa_ids)) {
                    $pranota->jumlah_tagihan = count($pranota->tagihan_kontainer_sewa_ids);
                } else {
                    $pranota->jumlah_tagihan = 0;
                }
            }
            
            return response()->json([
                'success' => true,
                'pranota' => $enrichedPranota,
                'pagination' => [
                    'current_page' => $pranotaList->currentPage(),
                    'last_page' => $pranotaList->lastPage(),
                    'per_page' => $pranotaList->perPage(),
                    'total' => $pranotaList->total()
                ]
            ]);
        }

        $pranotaList = $query->paginate($perPage);

        return view('pranota.index', compact('pranotaList'));
    }

    /**
     * Show the form for creating a new pranota kontainer sewa
     */
    public function create()
    {
        // For pranota kontainer sewa, creation is typically done via bulk operations
        // from the tagihan kontainer sewa page. This create form is for manual creation.
        return view('pranota.create', [
            'tagihanCat' => null,
            'nomorPranota' => 'PTKS' . date('ym') . '000001',
            'catatan' => 'Pranota kontainer sewa manual'
        ]);
    }

    /**
     * Store a newly created pranota kontainer sewa
     */
    public function store(Request $request)
    {
        $request->validate([
            'tagihan_kontainer_sewa_ids' => 'required|array|min:1',
            'tagihan_kontainer_sewa_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id',
            'keterangan' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'no_invoice_vendor' => 'nullable|string|max:255',
            'tgl_invoice_vendor' => 'nullable|date',
            'due_date' => 'nullable|date|after:today'
        ]);

        try {
            DB::beginTransaction();

            // Get selected tagihan items
            $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->tagihan_kontainer_sewa_ids)->get();

            if ($tagihanItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan kontainer sewa yang ditemukan dengan ID yang dipilih');
            }

            // Generate nomor pranota dengan format PMS dari master nomor terakhir
            $nomorCetakan = 1; // Default
            $tahun = Carbon::now()->format('y'); // 2 digit year
            $bulan = Carbon::now()->format('m'); // 2 digit month

            // Get next nomor pranota from master nomor terakhir dengan modul PMS
            $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->lockForUpdate()->first();
            if (!$nomorTerakhir) {
                throw new \Exception('Modul PMS tidak ditemukan di master nomor terakhir.');
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nomorTerakhir->nomor_terakhir = $nextNumber;
            $nomorTerakhir->save();

            // Create pranota
            $pranota = PranotaTagihanKontainerSewa::create([
                'no_invoice' => $noInvoice,
                'total_amount' => $tagihanItems->sum('grand_total'),
                'keterangan' => $request->keterangan ?? 'Pranota kontainer sewa untuk ' . count($request->tagihan_kontainer_sewa_ids) . ' tagihan',
                'supplier' => $request->supplier,
                'no_invoice_vendor' => $request->no_invoice_vendor,
                'tgl_invoice_vendor' => $request->tgl_invoice_vendor,
                'status' => 'unpaid',
                'tagihan_kontainer_sewa_ids' => $request->tagihan_kontainer_sewa_ids,
                'jumlah_tagihan' => count($request->tagihan_kontainer_sewa_ids),
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => $request->due_date ?? Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            // Update tagihan items status
            DaftarTagihanKontainerSewa::whereIn('id', $request->tagihan_kontainer_sewa_ids)
                ->update(['status' => 'paid']);

            DB::commit();

            return redirect()->route('pranota.index')->with('success',
                'Pranota kontainer sewa berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                ' (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pranota kontainer sewa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified pranota kontainer sewa
     */
    public function show(PranotaTagihanKontainerSewa $pranota)
    {
        $tagihanItems = $pranota->tagihanKontainerSewaItems();
        return view('pranota.show', compact('pranota', 'tagihanItems'));
    }

    /**
     * Print pranota kontainer sewa
     */
    public function print(PranotaTagihanKontainerSewa $pranota)
    {
        // Get tagihan items sorted by invoice_vendor
        $tagihanItems = $pranota->tagihanKontainerSewaItems()
            ->sortBy('invoice_vendor')
            ->values(); // Reset collection keys
        
        return view('pranota.print', compact('pranota', 'tagihanItems'));
    }

    /**
     * Show the form for editing pranota kontainer sewa
     */
    public function edit(PranotaTagihanKontainerSewa $pranota)
    {
        $tagihanItems = $pranota->tagihanKontainerSewaItems();
        return view('pranota.edit', compact('pranota', 'tagihanItems'));
    }

    /**
     * Update pranota kontainer sewa
     */
    public function update(Request $request, PranotaTagihanKontainerSewa $pranota)
    {
        $request->validate([
            'supplier' => 'nullable|string|max:255',
            'no_invoice_vendor' => 'nullable|string|max:255',
            'tgl_invoice_vendor' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $pranota->update([
            'supplier' => $request->supplier,
            'no_invoice_vendor' => $request->no_invoice_vendor,
            'tgl_invoice_vendor' => $request->tgl_invoice_vendor,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('pranota-kontainer-sewa.index')->with('success', 'Pranota berhasil diperbarui.');
    }

    /**
     * Bulk create from tagihan kontainer sewa
     */
    public function bulkCreateFromTagihanKontainerSewa(Request $request)
    {
        // Check if this is "masukan ke pranota" action
        $isMasukanKePranota = $request->input('action') === 'masukan_ke_pranota';

        // Base validation rules
        $validationRules = [
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id',
            'tanggal_pranota' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ];

        $validationMessages = [
            'tanggal_pranota.required' => 'Tanggal Pranota harus diisi'
        ];

        // Only require invoice vendor fields for "buat pranota baru", not for "masukan ke pranota"
        if (!$isMasukanKePranota) {
            $validationRules['no_invoice_vendor'] = 'required|string|max:255';
            $validationRules['tgl_invoice_vendor'] = 'required|date';
            $validationMessages['no_invoice_vendor.required'] = 'Invoice Vendor harus diisi';
            $validationMessages['tgl_invoice_vendor.required'] = 'Tanggal Invoice Vendor harus diisi';
        }

        $request->validate($validationRules, $validationMessages);

        try {
            DB::beginTransaction();

            // Get selected tagihan kontainer sewa items
            $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->selected_ids)->get();

            if ($tagihanItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan kontainer sewa yang ditemukan dengan ID yang dipilih');
            }

            // Generate nomor pranota dengan format PMS dari master nomor terakhir
            $nomorCetakan = 1; // Default
            $tahun = Carbon::now()->format('y'); // 2 digit year
            $bulan = Carbon::now()->format('m'); // 2 digit month

            // Get next nomor pranota from master nomor terakhir dengan modul PMS
            $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->lockForUpdate()->first();
            if (!$nomorTerakhir) {
                throw new \Exception('Modul PMS tidak ditemukan di master nomor terakhir.');
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nomorTerakhir->nomor_terakhir = $nextNumber;
            $nomorTerakhir->save();

            // Determine invoice vendor info based on action
            $invoiceVendorInfo = [];
            if ($isMasukanKePranota) {
                // For "masukan ke pranota", use invoice vendor from the first tagihan item
                // (assuming all items in same group have same vendor info)
                $firstTagihan = $tagihanItems->first();
                if ($firstTagihan && $firstTagihan->invoice_vendor && $firstTagihan->tanggal_vendor) {
                    $invoiceVendorInfo['no_invoice_vendor'] = $firstTagihan->invoice_vendor;
                    $invoiceVendorInfo['tgl_invoice_vendor'] = $firstTagihan->tanggal_vendor;
                } else {
                    throw new \Exception('Data invoice vendor tidak lengkap pada tagihan yang dipilih. Pastikan semua tagihan memiliki nomor dan tanggal invoice vendor.');
                }
            } else {
                // For "buat pranota baru", use form input
                $invoiceVendorInfo['no_invoice_vendor'] = $request->no_invoice_vendor;
                $invoiceVendorInfo['tgl_invoice_vendor'] = $request->tgl_invoice_vendor;
            }

            // Create pranota
            $pranota = PranotaTagihanKontainerSewa::create([
                'no_invoice' => $noInvoice,
                'total_amount' => $tagihanItems->sum('grand_total'),
                'keterangan' => $request->keterangan ?: 'Pranota bulk kontainer sewa untuk ' . count($request->selected_ids) . ' tagihan',
                'no_invoice_vendor' => $invoiceVendorInfo['no_invoice_vendor'],
                'tgl_invoice_vendor' => $invoiceVendorInfo['tgl_invoice_vendor'],
                'status' => 'unpaid',
                'tagihan_kontainer_sewa_ids' => $request->selected_ids,
                'jumlah_tagihan' => count($request->selected_ids),
                'tanggal_pranota' => $request->tanggal_pranota ?: Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            // Update tagihan kontainer sewa items status and pranota relationship
            DaftarTagihanKontainerSewa::whereIn('id', $request->selected_ids)
                ->update([
                    'status_pranota' => 'included',
                    'pranota_id' => $pranota->id
                ]);

            DB::commit();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pranota kontainer sewa bulk berhasil dibuat dengan nomor: ' . $pranota->no_invoice,
                    'nomor_pranota' => $pranota->no_invoice,
                    'total_amount' => $pranota->total_amount,
                    'jumlah_tagihan' => count($request->selected_ids),
                    'no_invoice_vendor' => $pranota->no_invoice_vendor,
                    'tgl_invoice_vendor' => $pranota->tgl_invoice_vendor
                ]);
            }

            return redirect()->back()->with('success',
                'Pranota kontainer sewa bulk berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                ' untuk ' . count($request->selected_ids) . ' tagihan kontainer sewa (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat pranota kontainer sewa bulk: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', 'Gagal membuat pranota kontainer sewa bulk: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update status for pranota kontainer sewa
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:pranota_tagihan_kontainer_sewas,id',
            'status' => 'required|string|in:unpaid,approved,in_progress,completed,cancelled'
        ]);

        try {
            PranotaTagihanKontainerSewa::whereIn('id', $request->ids)->update([
                'status' => $request->status,
                'updated_at' => Carbon::now()
            ]);

            $statusLabels = [
                'unpaid' => 'Belum Lunas',
                'approved' => 'Disetujui',
                'in_progress' => 'Dalam Proses',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            ];

            return redirect()->back()->with('success',
                count($request->ids) . ' pranota kontainer sewa berhasil diupdate status menjadi: ' . ($statusLabels[$request->status] ?? $request->status)
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update status pranota kontainer sewa: ' . $e->getMessage());
        }
    }

    /**
     * Bulk payment for pranota kontainer sewa
     */
    public function bulkPayment(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:pranota_tagihan_kontainer_sewas,id'
        ]);

        try {
            $pranotaList = PranotaTagihanKontainerSewa::whereIn('id', $request->ids)->get();

            if ($pranotaList->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada pranota yang ditemukan');
            }

            // Calculate total amount
            $totalAmount = $pranotaList->sum(function($pranota) {
                return $pranota->calculateTotalAmount();
            });

            // Store in session for payment form
            session([
                'bulk_payment_pranota_kontainer_sewa_ids' => $request->ids,
                'bulk_payment_pranota_kontainer_sewa_total' => $totalAmount,
                'bulk_payment_pranota_kontainer_sewa_count' => count($request->ids)
            ]);

            return redirect()->route('pembayaran-pranota-kontainer.create')->with('info',
                'Siap melakukan pembayaran untuk ' . count($request->ids) . ' pranota kontainer sewa dengan total Rp ' . number_format($totalAmount, 0, ',', '.')
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran bulk: ' . $e->getMessage());
        }
    }

    /**
     * Get the next pranota number for display in modal
     */
    public function getNextPranotaNumber(Request $request)
    {
        try {
            // Generate nomor pranota dengan format PMS dari master nomor terakhir
            $nomorCetakan = 1; // Default
            $tahun = Carbon::now()->format('y'); // 2 digit year
            $bulan = Carbon::now()->format('m'); // 2 digit month

            // Get next nomor pranota from master nomor terakhir dengan modul PMS
            $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->first();
            $nextNumber = $nomorTerakhir ? $nomorTerakhir->nomor_terakhir + 1 : 1;

            $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'nomor_pranota' => $noInvoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan nomor pranota: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lepas kontainer dari pranota
     */
    public function lepasKontainer(Request $request, $id)
    {
        Log::info('Lepas kontainer called', ['id' => $id, 'request' => $request->all()]);

        $request->validate([
            'tagihan_ids' => 'required|array',
            // 'tagihan_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id'
        ]);

        try {
            DB::beginTransaction();

            $pranota = PranotaTagihanKontainerSewa::findOrFail($id);
            $tagihanIds = $request->tagihan_ids;

            Log::info('Lepas kontainer validation', [
                'pranota_id' => $id,
                'tagihan_ids_request' => $tagihanIds,
                'pranota_tagihan_ids' => $pranota->tagihan_kontainer_sewa_ids
            ]);

            // Validasi bahwa tagihan IDs ada di pranota
            $currentTagihanIds = $pranota->tagihan_kontainer_sewa_ids ?? [];
            $validTagihanIds = array_intersect($tagihanIds, $currentTagihanIds);

            if (empty($validTagihanIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan yang dipilih tidak ditemukan di pranota ini.'
                ], 400);
            }

            // Get tagihan data to update kontainer status
            $tagihans = DaftarTagihanKontainerSewa::whereIn('id', $validTagihanIds)->get();

            // Update status tagihan menjadi belum masuk pranota
            // Update each tagihan individually to ensure proper handling
            DaftarTagihanKontainerSewa::whereIn('id', $validTagihanIds)->each(function ($tagihan) {
                $tagihan->update([
                    'status_pranota' => null,
                    'pranota_id' => null
                ]);
            });

            // Update status kontainer menjadi "belum masuk pranota"
            foreach ($tagihans as $tagihan) {
                if (!empty($tagihan->nomor_kontainer)) {
                    \App\Models\Kontainer::where('nomor_seri_gabungan', $tagihan->nomor_kontainer)
                        ->update(['status' => 'belum masuk pranota']);
                }
            }

            // Update pranota: hapus tagihan_ids yang dilepas
            $currentTagihanIds = $pranota->tagihan_kontainer_sewa_ids ?? [];
            // Ensure currentTagihanIds is an array
            if (!is_array($currentTagihanIds)) {
                $currentTagihanIds = [];
            }
            $remainingTagihanIds = array_diff($currentTagihanIds, $validTagihanIds);
            $pranota->tagihan_kontainer_sewa_ids = array_values($remainingTagihanIds);
            $pranota->jumlah_tagihan = count($remainingTagihanIds);

            if (!empty($remainingTagihanIds)) {
                $pranota->total_amount = DaftarTagihanKontainerSewa::whereIn('id', $remainingTagihanIds)->sum('grand_total');
            } else {
                $pranota->total_amount = 0;
            }

            $pranota->save();

            // Refresh model untuk memastikan data terbaru
            $pranota->refresh();

            DB::commit();

            // Log untuk debug
            Log::info('Lepas kontainer berhasil', [
                'pranota_id' => $id,
                'tagihan_ids_dilepas' => $validTagihanIds,
                'remaining_tagihan_ids' => $remainingTagihanIds,
                'jumlah_tagihan_baru' => $pranota->jumlah_tagihan,
                'total_amount_baru' => $pranota->total_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => count($validTagihanIds) . ' kontainer berhasil dilepas dari pranota.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            $validTagihanIds = $validTagihanIds ?? [];
            Log::error('Lepas kontainer gagal', [
                'pranota_id' => $id,
                'tagihan_ids' => $request->tagihan_ids,
                'valid_tagihan_ids' => $validTagihanIds,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melepas kontainer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show import page
     */
    public function importPage()
    {
        return view('pranota.import');
    }

    /**
     * Download template CSV for import
     */
    public function downloadTemplateCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_pranota_kontainer_sewa.csv"',
        ];

        $columns = [
            'group',
            'periode',
            'keterangan',
            'due_date'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            // Add sample data
            fputcsv($file, [
                '1',
                '1',
                'Pranota Group 1 Periode 1',
                '2025-11-01'
            ]);
            fputcsv($file, [
                '2',
                '1',
                'Pranota Group 2 Periode 1',
                '2025-11-15'
            ]);
            fputcsv($file, [
                '1',
                '2',
                'Pranota Group 1 Periode 2',
                '2025-12-01'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import pranota from CSV
     * 1 Pranota = Multiple kontainer dengan group dan periode yang sama
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();

            // Read CSV with semicolon delimiter (from export file format)
            $csv = array_map(function($line) {
                return str_getcsv($line, ';', '"', '\\');
            }, file($path));

            // Remove BOM if exists
            if (!empty($csv[0][0])) {
                $csv[0][0] = preg_replace('/^\x{FEFF}/u', '', $csv[0][0]);
            }

            // Get header
            $header = array_shift($csv);

            // Enhanced validation with flexible column name matching
            Log::info('CSV Header detected', ['header' => $header]);

            // Helper function to check if column exists (case-insensitive and flexible)
            $findColumn = function($possibleNames) use ($header) {
                foreach ($possibleNames as $name) {
                    foreach ($header as $col) {
                        if (strcasecmp(trim($col), trim($name)) === 0) {
                            return true;
                        }
                    }
                }
                return false;
            };

            // Define possible column variations
            $groupColumns = ['group', 'Group', 'GROUP'];
            $periodeColumns = ['periode', 'Periode', 'PERIODE'];
            $kontainerColumns = ['nomor_kontainer', 'Nomor Kontainer', 'Nomor_Kontainer', 'NOMOR_KONTAINER', 'kontainer', 'Kontainer'];
            $invoiceVendorColumns = ['No.InvoiceVendor', 'No InvoiceVendor', 'InvoiceVendor', 'Invoice Vendor', 'invoice_vendor'];
            $bankColumns = ['No.Bank', 'No Bank', 'Bank', 'NoBank', 'no_bank'];

            // Check for vendor invoice format first (higher priority)
            $hasInvoiceVendor = $findColumn($invoiceVendorColumns);
            $hasBank = $findColumn($bankColumns);
            $hasKontainer = $findColumn($kontainerColumns);

            $useVendorInvoiceGrouping = false;
            if ($hasInvoiceVendor && $hasBank && $hasKontainer) {
                $useVendorInvoiceGrouping = true;
                $groupingMode = 'vendor_invoice';
                Log::info('Using vendor invoice grouping mode');
            } else {
                // Check for standard group + periode format
                $hasGroup = $findColumn($groupColumns);
                $hasPeriode = $findColumn($periodeColumns);

                if ($hasGroup && $hasPeriode && $hasKontainer) {
                    $useVendorInvoiceGrouping = false;
                    $groupingMode = 'group_periode';
                    Log::info('Using standard group + periode mode');
                } else {
                    // Neither format found - show detailed error
                    $availableCols = implode(', ', $header);
                    return redirect()->back()->with('error',
                        "Format CSV tidak valid. Ditemukan kolom: [{$availableCols}]\n\n" .
                        "Pastikan file memiliki salah satu dari format berikut:\n" .
                        "1. Kolom: Group + Periode + Nomor Kontainer (atau variasi nama)\n" .
                        "2. Kolom: No.InvoiceVendor + No.Bank + Nomor Kontainer (untuk grouping otomatis)\n\n" .
                        "Kolom yang dicari:\n" .
                        "- Group: group, Group, GROUP\n" .
                        "- Periode: periode, Periode, PERIODE\n" .
                        "- Kontainer: nomor_kontainer, Nomor Kontainer, kontainer, dll\n" .
                        "- Invoice: No.InvoiceVendor, InvoiceVendor, dll\n" .
                        "- Bank: No.Bank, Bank, NoBank, dll");
                }
            }

            // Create flexible column mapper
            $getColumnValue = function($possibleNames, $row) use ($header) {
                foreach ($possibleNames as $name) {
                    foreach ($header as $index => $col) {
                        if (strcasecmp(trim($col), trim($name)) === 0) {
                            return isset($row[$index]) ? trim($row[$index]) : '';
                        }
                    }
                }
                return '';
            };

            // Group data based on detected format
            $groupedData = [];
            $notFound = [];
            $errors = [];
            $skippedRows = [];

            Log::info('Import CSV started', [
                'total_rows' => count($csv),
                'columns' => $header,
                'grouping_mode' => $groupingMode
            ]);

            foreach ($csv as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Get flexible column values
                    $keterangan = $getColumnValue(['keterangan', 'Keterangan', 'KETERANGAN'], $row);
                    $dueDate = $getColumnValue(['due_date', 'Due Date', 'DueDate', 'tanggal_jatuh_tempo'], $row);
                    $nomorKontainer = $getColumnValue($kontainerColumns, $row);

                    if ($useVendorInvoiceGrouping) {
                        // Vendor Invoice + Bank Number grouping mode
                        $invoiceVendor = $getColumnValue($invoiceVendorColumns, $row);
                        $bankNumber = $getColumnValue($bankColumns, $row);

                        // Validate required fields for vendor invoice mode
                        if (empty($invoiceVendor) || empty($bankNumber)) {
                            $errors[] = "Baris $rowNumber: No.InvoiceVendor dan No.Bank tidak boleh kosong";
                            continue;
                        }

                        if (empty($nomorKontainer)) {
                            $errors[] = "Baris $rowNumber: Nomor kontainer tidak boleh kosong";
                            continue;
                        }

                        // Use invoice vendor + bank as grouping key
                        $groupKey = $invoiceVendor . '_' . $bankNumber;
                        $groupLabel = "Invoice: {$invoiceVendor} | Bank: {$bankNumber}";

                    } else {
                        // Traditional Group + Periode mode
                        $group = $getColumnValue($groupColumns, $row);
                        $periode = $getColumnValue($periodeColumns, $row);

                        // Validate required fields for group/periode mode
                        if (empty($group) || empty($periode)) {
                            $errors[] = "Baris $rowNumber: Group dan Periode tidak boleh kosong (ditemukan: Group='$group', Periode='$periode')";
                            continue;
                        }

                        if (empty($nomorKontainer)) {
                            $errors[] = "Baris $rowNumber: Nomor kontainer tidak boleh kosong";
                            continue;
                        }

                        // Use group + periode as grouping key
                        $groupKey = "{$group}_{$periode}";
                        $groupLabel = "Group: {$group} | Periode: {$periode}";
                    }

                    // Common validation for kontainer
                    if (empty($nomorKontainer)) {
                        $errors[] = "Baris $rowNumber: Nomor kontainer tidak boleh kosong";
                        continue;
                    }

                    // Find tagihan by nomor kontainer
                    if ($useVendorInvoiceGrouping) {
                        // For vendor invoice mode, we only need the kontainer to exist
                        // The grouping is based on invoice + bank from CSV, not database fields
                        $tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
                            ->whereNull('status_pranota')
                            ->first();

                        if (!$tagihan) {
                            $anyTagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->first();
                            if (!$anyTagihan) {
                                $notFound[] = "Baris $rowNumber: Kontainer $nomorKontainer tidak ditemukan di database";
                            } else if ($anyTagihan->status_pranota !== null) {
                                $notFound[] = "Baris $rowNumber: Kontainer $nomorKontainer sudah masuk pranota";
                            }
                            continue;
                        }
                    } else {
                        // Traditional group + periode mode - more flexible search
                        $query = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
                            ->whereNull('status_pranota');

                        // Add group condition if not empty
                        if (!empty($group)) {
                            $query->where(function($q) use ($group) {
                                $q->where('group', $group)
                                  ->orWhere('group', (string)$group)
                                  ->orWhereRaw('CAST(`group` AS CHAR) = ?', [(string)$group]);
                            });
                        }

                        // Add periode condition if not empty
                        if (!empty($periode)) {
                            $query->where(function($q) use ($periode) {
                                $q->where('periode', $periode)
                                  ->orWhere('periode', (string)$periode)
                                  ->orWhereRaw('CAST(periode AS CHAR) = ?', [(string)$periode]);
                            });
                        }

                        $tagihan = $query->first();

                        if (!$tagihan) {
                            // Try searching without group/periode constraint for better error message
                            $anyTagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->first();
                            if (!$anyTagihan) {
                                $notFound[] = "Baris $rowNumber: Kontainer $nomorKontainer tidak ditemukan di database";
                            } else if ($anyTagihan->status_pranota !== null) {
                                $notFound[] = "Baris $rowNumber: Kontainer $nomorKontainer sudah masuk pranota (Group: {$anyTagihan->group}, Periode: {$anyTagihan->periode})";
                            } else {
                                $notFound[] = "Baris $rowNumber: Kontainer $nomorKontainer ditemukan tapi Group/Periode tidak cocok (DB: Group={$anyTagihan->group}, Periode={$anyTagihan->periode} vs CSV: Group={$group}, Periode={$periode})";
                            }
                            continue;
                        }
                    }

                    // Initialize group data structure
                    if (!isset($groupedData[$groupKey])) {
                        $groupedData[$groupKey] = [
                            'tagihan_ids' => [],
                            'keterangan' => $keterangan,
                            'due_date' => $dueDate,
                            'kontainers' => [],
                            'group_label' => $groupLabel
                        ];

                        // Add mode-specific fields
                        if ($useVendorInvoiceGrouping) {
                            $groupedData[$groupKey]['invoice_vendor'] = $invoiceVendor;
                            $groupedData[$groupKey]['bank_number'] = $bankNumber;
                        } else {
                            $groupedData[$groupKey]['group'] = $group;
                            $groupedData[$groupKey]['periode'] = $periode;
                        }
                    }

                    $groupedData[$groupKey]['tagihan_ids'][] = $tagihan->id;
                    $groupedData[$groupKey]['kontainers'][] = $nomorKontainer;

                    // Use first non-empty keterangan and due_date
                    if (empty($groupedData[$groupKey]['keterangan']) && !empty($keterangan)) {
                        $groupedData[$groupKey]['keterangan'] = $keterangan;
                    }
                    if (empty($groupedData[$groupKey]['due_date']) && !empty($dueDate)) {
                        $groupedData[$groupKey]['due_date'] = $dueDate;
                    }

                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber: " . $e->getMessage();
                    Log::error("Import parsing error at row $rowNumber", [
                        'error' => $e->getMessage(),
                        'row' => $row
                    ]);
                }
            }

            // Now create pranota for each group
            $imported = 0;
            $totalKontainers = 0;
            $pranotaDetails = [];

            DB::beginTransaction();

            foreach ($groupedData as $key => $data) {
                try {
                    $tagihanIds = $data['tagihan_ids'];

                    if (empty($tagihanIds)) {
                        continue;
                    }

                    // Get tagihan items
                    $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $tagihanIds)->get();

                    if ($tagihanItems->isEmpty()) {
                        continue;
                    }

                    // Generate nomor pranota dengan format PMS
                    $nomorCetakan = 1;
                    $tahun = Carbon::now()->format('y');
                    $bulan = Carbon::now()->format('m');

                    $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->lockForUpdate()->first();
                    if (!$nomorTerakhir) {
                        throw new \Exception('Modul PMS tidak ditemukan di master nomor terakhir.');
                    }
                    $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
                    $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                    $nomorTerakhir->nomor_terakhir = $nextNumber;
                    $nomorTerakhir->save();

                    // Parse due date
                    $dueDateParsed = null;
                    if (!empty($data['due_date'])) {
                        try {
                            $dueDateParsed = Carbon::parse($data['due_date'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $dueDateParsed = Carbon::now()->addDays(30)->format('Y-m-d');
                        }
                    } else {
                        $dueDateParsed = Carbon::now()->addDays(30)->format('Y-m-d');
                    }

                    // Calculate total amount
                    $totalAmount = $tagihanItems->sum('grand_total');

                    // Default keterangan based on grouping mode
                    if (!empty($data['keterangan'])) {
                        $keterangan = $data['keterangan'];
                    } else {
                        if ($useVendorInvoiceGrouping) {
                            $keterangan = "Pranota {$data['group_label']} - " . count($tagihanIds) . " kontainer (Auto Import)";
                        } else {
                            $keterangan = "Pranota Group {$data['group']} Periode {$data['periode']} - " . count($tagihanIds) . " kontainer (Import)";
                        }
                    }

                    // Create pranota
                    $pranota = PranotaTagihanKontainerSewa::create([
                        'no_invoice' => $noInvoice,
                        'total_amount' => $totalAmount,
                        'keterangan' => $keterangan,
                        'status' => 'unpaid',
                        'tagihan_kontainer_sewa_ids' => $tagihanIds,
                        'jumlah_tagihan' => count($tagihanIds),
                        'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                        'due_date' => $dueDateParsed
                    ]);

                    // Update tagihan status to "masuk pranota"
                    DaftarTagihanKontainerSewa::whereIn('id', $tagihanIds)->update([
                        'status_pranota' => 'included',
                        'pranota_id' => $pranota->id
                    ]);

                    $imported++;
                    $totalKontainers += count($tagihanIds);

                    // Build details based on grouping mode
                    $detailData = [
                        'no_invoice' => $noInvoice,
                        'jumlah_kontainer' => count($tagihanIds),
                        'total_amount' => $totalAmount,
                        'kontainers' => implode(', ', array_slice($data['kontainers'], 0, 5)) . (count($data['kontainers']) > 5 ? '...' : ''),
                        'grouping_mode' => $groupingMode,
                        'group_label' => $data['group_label']
                    ];

                    if ($useVendorInvoiceGrouping) {
                        $detailData['invoice_vendor'] = $data['invoice_vendor'];
                        $detailData['bank_number'] = $data['bank_number'];
                    } else {
                        $detailData['group'] = $data['group'];
                        $detailData['periode'] = $data['periode'];
                    }

                    $pranotaDetails[] = $detailData;

                } catch (\Exception $e) {
                    $groupLabel = isset($data['group_label']) ? $data['group_label'] : 'Unknown Group';
                    $errors[] = "Group {$groupLabel}: " . $e->getMessage();
                    Log::error("Import error for group", [
                        'group_data' => $data,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            // Prepare result message
            $groupingModeText = $useVendorInvoiceGrouping ? 'Invoice Vendor + Bank Number' : 'Group + Periode';
            $message = "Import selesai (Mode: {$groupingModeText}): $imported pranota berhasil dibuat untuk $totalKontainers kontainer.";

            if ($useVendorInvoiceGrouping) {
                // Calculate efficiency for vendor invoice grouping
                $totalRows = count($csv);
                $efficiency = $totalRows > 0 ? round((($totalRows - $imported) / $totalRows) * 100, 1) : 0;
                $message .= " Efisiensi grouping: {$efficiency}% (dari {$totalRows} kontainer menjadi {$imported} pranota).";
            }

            if (!empty($notFound)) {
                $message .= " " . count($notFound) . " kontainer tidak ditemukan atau sudah masuk pranota.";
            }

            if (!empty($errors)) {
                $message .= " " . count($errors) . " error terjadi.";
            }

            // Store details in session for display
            session([
                'import_result' => [
                    'imported' => $imported,
                    'total_kontainers' => $totalKontainers,
                    'pranota_details' => $pranotaDetails,
                    'not_found' => $notFound,
                    'errors' => $errors,
                    'grouping_mode' => $groupingMode,
                    'grouping_mode_text' => $groupingModeText,
                    'use_vendor_invoice_grouping' => $useVendorInvoiceGrouping
                ]
            ]);

            if ($imported > 0) {
                return redirect()->route('pranota-kontainer-sewa.index')->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'Tidak ada data yang berhasil diimport. ' . $message);
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Import CSV failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal import CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete a single pranota kontainer sewa
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pranota = PranotaTagihanKontainerSewa::findOrFail($id);
            $noInvoice = $pranota->no_invoice;

            // Get all related tagihan items to update their status
            // Use pranota_id instead of no_pranota_tagihan
            $tagihanItems = DaftarTagihanKontainerSewa::where('pranota_id', $id)->get();

            // Reset tagihan items status
            foreach ($tagihanItems as $tagihan) {
                $tagihan->status_pranota = null;
                $tagihan->pranota_id = null;
                $tagihan->save();
            }

            // Delete the pranota
            $pranota->delete();

            DB::commit();

            Log::info("Pranota deleted successfully", [
                'pranota_id' => $id,
                'no_invoice' => $noInvoice,
                'tagihan_count' => $tagihanItems->count(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('pranota-kontainer-sewa.index')
                ->with('success', "Pranota {$noInvoice} berhasil dihapus dan " . $tagihanItems->count() . " tagihan dikembalikan ke status belum masuk pranota.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete pranota', [
                'pranota_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal menghapus pranota: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete multiple pranota kontainer sewa
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'pranota_ids' => 'required|json'
        ]);

        try {
            DB::beginTransaction();

            $pranotaIds = json_decode($request->pranota_ids, true);

            if (empty($pranotaIds) || !is_array($pranotaIds)) {
                throw new \Exception('ID Pranota tidak valid');
            }

            $deletedCount = 0;
            $totalTagihanReset = 0;
            $errors = [];

            foreach ($pranotaIds as $pranotaId) {
                try {
                    $pranota = PranotaTagihanKontainerSewa::find($pranotaId);

                    if (!$pranota) {
                        $errors[] = "Pranota ID {$pranotaId} tidak ditemukan";
                        continue;
                    }

                    $noInvoice = $pranota->no_invoice;

                    // Get all related tagihan items
                    // Use pranota_id instead of no_pranota_tagihan
                    $tagihanItems = DaftarTagihanKontainerSewa::where('pranota_id', $pranotaId)->get();

                    // Reset tagihan items status
                    foreach ($tagihanItems as $tagihan) {
                        $tagihan->status_pranota = null;
                        $tagihan->pranota_id = null;
                        $tagihan->save();
                    }

                    $totalTagihanReset += $tagihanItems->count();

                    // Delete the pranota
                    $pranota->delete();
                    $deletedCount++;

                    Log::info("Pranota deleted in bulk operation", [
                        'pranota_id' => $pranotaId,
                        'no_invoice' => $noInvoice,
                        'tagihan_count' => $tagihanItems->count()
                    ]);

                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus pranota ID {$pranotaId}: " . $e->getMessage();
                    Log::error('Bulk delete failed for pranota', [
                        'pranota_id' => $pranotaId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            $message = "{$deletedCount} pranota berhasil dihapus";
            if ($totalTagihanReset > 0) {
                $message .= " dan {$totalTagihanReset} tagihan dikembalikan ke status belum masuk pranota";
            }

            if (!empty($errors)) {
                $message .= ". Namun ada " . count($errors) . " error: " . implode(', ', $errors);
            }

            Log::info("Bulk delete completed", [
                'deleted_count' => $deletedCount,
                'tagihan_reset_count' => $totalTagihanReset,
                'error_count' => count($errors),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('pranota-kontainer-sewa.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete operation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal menghapus pranota: ' . $e->getMessage());
        }
    }

    /**
     * Mengelompokkan kontainer berdasarkan nomor invoice vendor dan nomor bank yang sama
     * Untuk membuat pranota otomatis berdasarkan grouping
     */
    public function createPranotaByVendorInvoiceGroup(Request $request)
    {
        $request->validate([
            'tagihan_kontainer_sewa_ids' => 'required|array|min:1',
            'tagihan_kontainer_sewa_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id'
        ]);

        try {
            DB::beginTransaction();

            // Get selected tagihan items
            $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->tagihan_kontainer_sewa_ids)
                ->where('status_pranota', '!=', 'included') // Only include items not yet in pranota
                ->get();

            if ($tagihanItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan kontainer sewa yang tersedia untuk dibuatkan pranota');
            }

            // Group kontainer by nomor invoice vendor and nomor bank
            $groupedKontainer = $this->groupKontainerByVendorInvoiceAndBank($tagihanItems);

            if (empty($groupedKontainer)) {
                throw new \Exception('Tidak ada kontainer yang memiliki nomor invoice vendor dan nomor bank');
            }

            $createdPranota = [];
            $totalProcessed = 0;

            foreach ($groupedKontainer as $groupKey => $kontainerGroup) {
                // Skip groups without both invoice vendor and bank number
                if (empty($kontainerGroup['no_invoice_vendor']) || empty($kontainerGroup['no_bank'])) {
                    continue;
                }

                // Generate nomor pranota untuk setiap group
                $nomorCetakan = 1;
                $tahun = Carbon::now()->format('y');
                $bulan = Carbon::now()->format('m');

                $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->lockForUpdate()->first();
                if (!$nomorTerakhir) {
                    throw new \Exception('Modul PMS tidak ditemukan di master nomor terakhir.');
                }

                $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
                $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                $nomorTerakhir->nomor_terakhir = $nextNumber;
                $nomorTerakhir->save();

                // Calculate total amount for this group
                $totalAmount = collect($kontainerGroup['items'])->sum('grand_total');
                $itemCount = count($kontainerGroup['items']);

                // Create pranota for this group
                $pranota = PranotaTagihanKontainerSewa::create([
                    'no_invoice' => $noInvoice,
                    'total_amount' => $totalAmount,
                    'keterangan' => "Pranota kontainer sewa - Invoice Vendor: {$kontainerGroup['no_invoice_vendor']}, No Bank: {$kontainerGroup['no_bank']} ({$itemCount} kontainer)",
                    'status' => 'unpaid',
                    'supplier' => $kontainerGroup['supplier'] ?? 'ZONA',
                    'no_invoice_vendor' => $kontainerGroup['no_invoice_vendor'],
                    'tgl_invoice_vendor' => $kontainerGroup['tgl_invoice_vendor'] ?? null,
                    'no_bank' => $kontainerGroup['no_bank'],
                    'tgl_bank' => $kontainerGroup['tgl_bank'] ?? null,
                    'tagihan_kontainer_sewa_ids' => collect($kontainerGroup['items'])->pluck('id')->toArray(),
                    'jumlah_tagihan' => $itemCount,
                    'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                    'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
                ]);

                // Update tagihan kontainer sewa items
                $itemIds = collect($kontainerGroup['items'])->pluck('id')->toArray();
                DaftarTagihanKontainerSewa::whereIn('id', $itemIds)
                    ->update([
                        'status_pranota' => 'included',
                        'pranota_id' => $pranota->id
                    ]);

                $createdPranota[] = [
                    'no_invoice' => $noInvoice,
                    'no_invoice_vendor' => $kontainerGroup['no_invoice_vendor'],
                    'no_bank' => $kontainerGroup['no_bank'],
                    'item_count' => $itemCount,
                    'total_amount' => $totalAmount
                ];

                $totalProcessed += $itemCount;
            }

            DB::commit();

            if (empty($createdPranota)) {
                return redirect()->back()->with('warning', 'Tidak ada pranota yang dibuat. Pastikan kontainer memiliki nomor invoice vendor dan nomor bank yang lengkap.');
            }

            $message = count($createdPranota) . ' pranota berhasil dibuat untuk ' . $totalProcessed . ' kontainer berdasarkan grouping invoice vendor dan nomor bank:<br>';
            foreach ($createdPranota as $pranota) {
                $message .= "- {$pranota['no_invoice']}: Invoice Vendor {$pranota['no_invoice_vendor']}, Bank {$pranota['no_bank']} ({$pranota['item_count']} kontainer, Rp " . number_format($pranota['total_amount'], 2, ',', '.') . ")<br>";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating pranota by vendor invoice group', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal membuat pranota berdasarkan grouping: ' . $e->getMessage());
        }
    }

    /**
     * Group kontainer by nomor invoice vendor and nomor bank
     * Helper method for grouping logic
     */
    private function groupKontainerByVendorInvoiceAndBank($tagihanItems)
    {
        $groups = [];

        foreach ($tagihanItems as $item) {
            // Skip items without both invoice vendor and bank number
            if (empty($item->no_invoice_vendor) || empty($item->no_bank)) {
                continue;
            }

            // Create group key based on invoice vendor + bank number
            $groupKey = $item->no_invoice_vendor . '|' . $item->no_bank;

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'no_invoice_vendor' => $item->no_invoice_vendor,
                    'tgl_invoice_vendor' => $item->tgl_invoice_vendor,
                    'no_bank' => $item->no_bank,
                    'tgl_bank' => $item->tgl_bank,
                    'supplier' => $item->supplier ?? 'ZONA',
                    'items' => []
                ];
            }

            $groups[$groupKey]['items'][] = $item;
        }

        return $groups;
    }

    /**
     * Preview grouping hasil sebelum membuat pranota
     * Menampilkan preview bagaimana kontainer akan dikelompokkan
     */
    public function previewVendorInvoiceGrouping(Request $request)
    {
        $request->validate([
            'tagihan_kontainer_sewa_ids' => 'required|array|min:1',
            'tagihan_kontainer_sewa_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id'
        ]);

        // Get selected tagihan items
        $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->tagihan_kontainer_sewa_ids)
            ->where('status_pranota', '!=', 'included')
            ->get();

        if ($tagihanItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tagihan kontainer sewa yang tersedia'
            ]);
        }

        // Group kontainer
        $groupedKontainer = $this->groupKontainerByVendorInvoiceAndBank($tagihanItems);

        $previewData = [];
        $totalPranota = 0;
        $totalKontainer = 0;
        $kontainerTanpaGroup = [];

        foreach ($groupedKontainer as $groupKey => $kontainerGroup) {
            if (!empty($kontainerGroup['no_invoice_vendor']) && !empty($kontainerGroup['no_bank'])) {
                $totalAmount = collect($kontainerGroup['items'])->sum('grand_total');
                $itemCount = count($kontainerGroup['items']);

                $previewData[] = [
                    'group_key' => $groupKey,
                    'no_invoice_vendor' => $kontainerGroup['no_invoice_vendor'],
                    'tgl_invoice_vendor' => $kontainerGroup['tgl_invoice_vendor'],
                    'no_bank' => $kontainerGroup['no_bank'],
                    'tgl_bank' => $kontainerGroup['tgl_bank'],
                    'supplier' => $kontainerGroup['supplier'],
                    'item_count' => $itemCount,
                    'total_amount' => $totalAmount,
                    'kontainer_list' => collect($kontainerGroup['items'])->pluck('kontainer')->toArray()
                ];

                $totalPranota++;
                $totalKontainer += $itemCount;
            }
        }

        // Check for items without complete grouping info
        foreach ($tagihanItems as $item) {
            if (empty($item->no_invoice_vendor) || empty($item->no_bank)) {
                $kontainerTanpaGroup[] = [
                    'kontainer' => $item->kontainer,
                    'no_invoice_vendor' => $item->no_invoice_vendor,
                    'no_bank' => $item->no_bank,
                    'reason' => empty($item->no_invoice_vendor) ? 'Tidak ada nomor invoice vendor' : 'Tidak ada nomor bank'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'preview_data' => $previewData,
            'summary' => [
                'total_pranota_akan_dibuat' => $totalPranota,
                'total_kontainer_diproses' => $totalKontainer,
                'total_kontainer_dipilih' => $tagihanItems->count(),
                'kontainer_tanpa_group' => $kontainerTanpaGroup
            ]
        ]);
    }

    /**
     * Add items to existing pranota
     */
    public function addItemsToExisting(Request $request)
    {
        try {
            $request->validate([
                'pranota_id' => 'required|exists:pranota_tagihan_kontainer_sewa,id',
                'tagihan_ids' => 'required|array|min:1',
                'tagihan_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id'
            ]);

            DB::beginTransaction();

            // Get existing pranota
            $pranota = PranotaTagihanKontainerSewa::findOrFail($request->pranota_id);
            
            // Get tagihan items
            $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->tagihan_ids)->get();

            // Validate items
            foreach ($tagihanItems as $tagihan) {
                // Check if already in pranota
                if (!empty($tagihan->pranota_id)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Kontainer {$tagihan->no_kontainer} sudah masuk dalam pranota lain"
                    ], 400);
                }

                // Check if has group
                if (empty($tagihan->group)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Kontainer {$tagihan->no_kontainer} belum memiliki group"
                    ], 400);
                }

                // Check if has vendor info
                if (empty($tagihan->invoice_vendor)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Kontainer {$tagihan->no_kontainer} belum memiliki nomor vendor"
                    ], 400);
                }
            }

            // Update tagihan items to point to this pranota
            DaftarTagihanKontainerSewa::whereIn('id', $request->tagihan_ids)
                ->update([
                    'pranota_id' => $pranota->id,
                    'status_pranota' => 'Masuk Pranota',
                    'updated_at' => now()
                ]);

            // Recalculate pranota totals
            $this->recalculatePranotaTotals($pranota);

            DB::commit();

            Log::info('Items added to existing pranota', [
                'pranota_id' => $pranota->id,
                'tagihan_ids' => $request->tagihan_ids,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke pranota',
                'pranota_nomor' => $pranota->nomor_pranota,
                'items_added' => count($request->tagihan_ids)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding items to existing pranota: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateGrandTotal(Request $request)
    {
        try {
            $request->validate([
                'tagihan_id' => 'required|exists:daftar_tagihan_kontainer_sewa,id',
                'grand_total' => 'required|numeric|min:0',
                'last_3_digits' => 'required|integer|min:0|max:999'
            ]);

            DB::beginTransaction();

            // Get tagihan
            $tagihan = DaftarTagihanKontainerSewa::findOrFail($request->tagihan_id);
            
            // Check if tagihan is in a pranota and pranota is unpaid
            if ($tagihan->pranota_id) {
                $pranota = PranotaTagihanKontainerSewa::find($tagihan->pranota_id);
                if ($pranota && $pranota->status !== 'unpaid') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Grand Total tidak dapat diubah karena pranota sudah tidak berstatus unpaid'
                    ], 400);
                }
            }

            // Store old value for logging
            $oldGrandTotal = $tagihan->grand_total;

            // Update grand total
            $tagihan->grand_total = $request->grand_total;
            $tagihan->save();

            // If tagihan is in a pranota, recalculate pranota totals
            if ($tagihan->pranota_id) {
                $pranota = PranotaTagihanKontainerSewa::find($tagihan->pranota_id);
                if ($pranota) {
                    $this->recalculatePranotaTotals($pranota);
                }
            }

            DB::commit();

            Log::info('Grand Total updated for tagihan', [
                'tagihan_id' => $tagihan->id,
                'nomor_kontainer' => $tagihan->nomor_kontainer,
                'old_grand_total' => $oldGrandTotal,
                'new_grand_total' => $request->grand_total,
                'last_3_digits' => $request->last_3_digits,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Grand Total berhasil diperbarui',
                'old_value' => $oldGrandTotal,
                'new_value' => $request->grand_total
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating grand total: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculate pranota totals after adding/removing items
     */
    private function recalculatePranotaTotals(PranotaTagihanKontainerSewa $pranota)
    {
        $tagihanItems = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->get();
        
        $totalAmount = $tagihanItems->sum('grand_total'); // Use grand_total field
        $jumlahTagihan = $tagihanItems->count();
        
        // Update tagihan_kontainer_sewa_ids array
        $tagihanIds = $tagihanItems->pluck('id')->toArray();
        
        $pranota->update([
            'total_amount' => $totalAmount,
            'jumlah_tagihan' => $jumlahTagihan,
            'tagihan_kontainer_sewa_ids' => $tagihanIds, // Keep array updated
            'updated_at' => now()
        ]);
    }
}
