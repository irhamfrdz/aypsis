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
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%')
                  ->orWhere('no_invoice_vendor', 'like', '%' . $request->search . '%');
            });
        }

        $pranotaList = $query->paginate(15);

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
        $tagihanItems = $pranota->tagihanKontainerSewaItems();
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
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id'
        ]);

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

            // Create pranota
            $pranota = PranotaTagihanKontainerSewa::create([
                'no_invoice' => $noInvoice,
                'total_amount' => $tagihanItems->sum('grand_total'),
                'keterangan' => 'Pranota bulk kontainer sewa untuk ' . count($request->selected_ids) . ' tagihan',
                'status' => 'unpaid',
                'tagihan_kontainer_sewa_ids' => $request->selected_ids,
                'jumlah_tagihan' => count($request->selected_ids),
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            // Update tagihan kontainer sewa items status and pranota relationship
            DaftarTagihanKontainerSewa::whereIn('id', $request->selected_ids)
                ->update([
                    'status_pranota' => 'included',
                    'pranota_id' => $pranota->id
                ]);

            DB::commit();

            return redirect()->back()->with('success',
                'Pranota kontainer sewa bulk berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                ' untuk ' . count($request->selected_ids) . ' tagihan kontainer sewa (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')'
            );

        } catch (\Exception $e) {
            DB::rollback();
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

            // Validate header - support both formats
            $requiredColumns = ['group', 'periode'];
            $alternateColumns = ['Group', 'Periode', 'Nomor Kontainer'];

            $hasRequiredFormat = true;
            foreach ($requiredColumns as $col) {
                if (!in_array($col, $header)) {
                    $hasRequiredFormat = false;
                    break;
                }
            }

            // If not found, check alternate format (exported format)
            if (!$hasRequiredFormat) {
                $hasAlternateFormat = true;
                foreach ($alternateColumns as $col) {
                    if (!in_array($col, $header)) {
                        $hasAlternateFormat = false;
                        break;
                    }
                }

                if (!$hasAlternateFormat) {
                    return redirect()->back()->with('error',
                        "Format CSV tidak valid. Pastikan file memiliki kolom: 'Group', 'Periode', dan 'Nomor Kontainer' atau gunakan template yang disediakan.");
                }
            }

            // Map header to index
            $colMap = array_flip($header);

            // Group data by group and periode
            $groupedData = [];
            $notFound = [];
            $errors = [];
            $skippedRows = [];

            Log::info('Import CSV started', [
                'total_rows' => count($csv),
                'columns' => $header
            ]);

            foreach ($csv as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Support both lowercase and capitalized column names
                    $group = isset($colMap['group']) ? trim($row[$colMap['group']] ?? '') :
                             (isset($colMap['Group']) ? trim($row[$colMap['Group']] ?? '') : '');
                    $periode = isset($colMap['periode']) ? trim($row[$colMap['periode']] ?? '') :
                               (isset($colMap['Periode']) ? trim($row[$colMap['Periode']] ?? '') : '');
                    $nomorKontainer = isset($colMap['nomor_kontainer']) ? trim($row[$colMap['nomor_kontainer']] ?? '') :
                                     (isset($colMap['Nomor Kontainer']) ? trim($row[$colMap['Nomor Kontainer']] ?? '') : '');
                    $keterangan = isset($colMap['keterangan']) ? trim($row[$colMap['keterangan']] ?? '') : '';
                    $dueDate = isset($colMap['due_date']) ? trim($row[$colMap['due_date']] ?? '') : '';

                    // Validate required fields
                    if (empty($group) || empty($periode)) {
                        $errors[] = "Baris $rowNumber: Group dan Periode tidak boleh kosong";
                        continue;
                    }

                    if (empty($nomorKontainer)) {
                        $errors[] = "Baris $rowNumber: Nomor kontainer tidak boleh kosong";
                        continue;
                    }

                    // Find tagihan by nomor kontainer, group, and periode
                    // More flexible search - handle both string and integer types
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

                    // Group by group and periode
                    $key = "{$group}_{$periode}";
                    if (!isset($groupedData[$key])) {
                        $groupedData[$key] = [
                            'group' => $group,
                            'periode' => $periode,
                            'tagihan_ids' => [],
                            'keterangan' => $keterangan,
                            'due_date' => $dueDate,
                            'kontainers' => []
                        ];
                    }

                    $groupedData[$key]['tagihan_ids'][] = $tagihan->id;
                    $groupedData[$key]['kontainers'][] = $nomorKontainer;

                    // Use first non-empty keterangan and due_date
                    if (empty($groupedData[$key]['keterangan']) && !empty($keterangan)) {
                        $groupedData[$key]['keterangan'] = $keterangan;
                    }
                    if (empty($groupedData[$key]['due_date']) && !empty($dueDate)) {
                        $groupedData[$key]['due_date'] = $dueDate;
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

                    // Default keterangan
                    $keterangan = !empty($data['keterangan'])
                        ? $data['keterangan']
                        : "Pranota Group {$data['group']} Periode {$data['periode']} - " . count($tagihanIds) . " kontainer (Import)";

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

                    $pranotaDetails[] = [
                        'no_invoice' => $noInvoice,
                        'group' => $data['group'],
                        'periode' => $data['periode'],
                        'jumlah_kontainer' => count($tagihanIds),
                        'total_amount' => $totalAmount,
                        'kontainers' => implode(', ', array_slice($data['kontainers'], 0, 5)) . (count($data['kontainers']) > 5 ? '...' : '')
                    ];

                } catch (\Exception $e) {
                    $errors[] = "Group {$data['group']} Periode {$data['periode']}: " . $e->getMessage();
                    Log::error("Import error for group", [
                        'group' => $data['group'],
                        'periode' => $data['periode'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            // Prepare result message
            $message = "Import selesai: $imported pranota berhasil dibuat untuk $totalKontainers kontainer.";

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
                    'errors' => $errors
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
}
