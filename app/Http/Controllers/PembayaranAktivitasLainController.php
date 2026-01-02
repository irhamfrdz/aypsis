<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PembayaranAktivitasLain;
use App\Models\PembayaranInvoiceAktivitasLain;
use App\Models\InvoiceAktivitasLain;
use App\Models\Coa;
use App\Models\CoaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranAktivitasLainController extends Controller
{
    public function index(Request $request)
    {
        $tipePembayaran = $request->get('tipe_pembayaran', 'semua');
        
        if ($tipePembayaran === 'invoice') {
            // Only show invoice payments
            $query = PembayaranInvoiceAktivitasLain::with(['creator', 'approver']);
            
            // Filter by date range
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                      ->orWhere('nomor_accurate', 'like', "%{$search}%")
                      ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $pembayarans = $query->orderBy('created_at', 'desc')->paginate(20)->through(function($item) {
                $item->tipe_pembayaran = 'invoice';
                return $item;
            });
            
        } elseif ($tipePembayaran === 'langsung') {
            // Only show direct payments
            $query = PembayaranAktivitasLain::with(['creator', 'approver']);

            // Filter by date range
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                      ->orWhere('nomor_accurate', 'like', "%{$search}%")
                      ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $pembayarans = $query->orderBy('created_at', 'desc')->paginate(20)->through(function($item) {
                $item->tipe_pembayaran = 'langsung';
                return $item;
            });
        } else {
            // Show both types - merge collections
            $directQuery = PembayaranAktivitasLain::with(['creator', 'approver']);
            $invoiceQuery = PembayaranInvoiceAktivitasLain::with(['creator', 'approver']);

            // Apply filters to both queries
            if ($request->filled('tanggal_dari')) {
                $directQuery->whereDate('tanggal', '>=', $request->tanggal_dari);
                $invoiceQuery->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $directQuery->whereDate('tanggal', '<=', $request->tanggal_sampai);
                $invoiceQuery->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $directQuery->where(function($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                      ->orWhere('nomor_accurate', 'like', "%{$search}%")
                      ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
                $invoiceQuery->where(function($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                      ->orWhere('nomor_accurate', 'like', "%{$search}%")
                      ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $directPayments = $directQuery->get()->map(function($item) {
                $item->tipe_pembayaran = 'langsung';
                return $item;
            });
            
            $invoicePayments = $invoiceQuery->get()->map(function($item) {
                $item->tipe_pembayaran = 'invoice';
                $item->jumlah = $item->jumlah_dibayar; // Normalize field name
                return $item;
            });

            $allPayments = $directPayments->merge($invoicePayments)
                ->sortByDesc('created_at')
                ->values();

            // Manual pagination
            $perPage = 20;
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            
            $paginatedItems = $allPayments->slice($offset, $perPage)->values();
            
            $pembayarans = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedItems,
                $allPayments->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        // Get all unique akun_coa_ids and akun_bank_ids from the results
        $akunCoaIds = $pembayarans->pluck('akun_coa_id')->filter()->unique();
        $akunBankIds = $pembayarans->pluck('akun_bank_id')->filter()->unique();
        $allAkunIds = $akunCoaIds->merge($akunBankIds)->unique();
        
        $akunCoas = DB::table('akun_coa')
            ->whereIn('id', $allAkunIds)
            ->get()
            ->keyBy('id');

        return view('pembayaran-aktivitas-lain.index', compact('pembayarans', 'akunCoas'));
    }

    public function create(Request $request)
    {
        // Check if method parameter is 'invoice'
        if ($request->get('method') === 'invoice') {
            return $this->createFromInvoice();
        }

        // Default: Direct payment form
        $nomor = PembayaranAktivitasLain::generateNomor();
        $akunBiaya = DB::table('akun_coa')
            ->orderBy('kode_nomor')
            ->get();
        $mobils = DB::table('mobils')
            ->select('id', 'nomor_polisi', 'merek', 'jenis')
            ->orderBy('nomor_polisi')
            ->get();
        
        // Get voyages from both bls and pergerakan_kapal tables
        $voyagesBl = DB::table('bls')
            ->select('no_voyage as voyage', 'nama_kapal')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->distinct()
            ->orderBy('no_voyage')
            ->get();
            
        $voyagesPergerakan = DB::table('pergerakan_kapal')
            ->select('voyage', 'nama_kapal')
            ->whereNotNull('voyage')
            ->where('voyage', '!=', '')
            ->distinct()
            ->orderBy('voyage')
            ->get();
            
        // Combine and deduplicate voyages
        $allVoyages = collect();
        
        foreach ($voyagesBl as $voyage) {
            $allVoyages->push((object)[
                'voyage' => $voyage->voyage,
                'nama_kapal' => $voyage->nama_kapal,
                'source' => 'BL'
            ]);
        }
        
        foreach ($voyagesPergerakan as $voyage) {
            // Only add if not already exists
            $exists = $allVoyages->where('voyage', $voyage->voyage)
                                ->where('nama_kapal', $voyage->nama_kapal)
                                ->first();
            if (!$exists) {
                $allVoyages->push((object)[
                    'voyage' => $voyage->voyage,
                    'nama_kapal' => $voyage->nama_kapal,
                    'source' => 'Pergerakan Kapal'
                ]);
            }
        }
        
        $voyages = $allVoyages->sortBy('voyage')->values();
        
        $akunBank = DB::table('akun_coa')
            ->where(function($q) {
                $q->where('tipe_akun', 'like', '%kas%')
                  ->orWhere('tipe_akun', 'like', '%bank%');
            })
            ->orderBy('kode_nomor')
            ->get();
            
        $karyawans = DB::table('karyawans')
            ->select('id', 'nama_lengkap', 'pekerjaan')
            ->orderBy('nama_lengkap')
            ->get();
        
        $suratJalans = DB::table('surat_jalans')
            ->select('id', 'no_surat_jalan', 'tujuan_pengiriman', 'uang_jalan')
            ->whereNotNull('no_surat_jalan')
            ->where('no_surat_jalan', '!=', '')
            ->orderBy('no_surat_jalan')
            ->get();
        
        return view('pembayaran-aktivitas-lain.create', compact('nomor', 'akunBiaya', 'akunBank', 'mobils', 'voyages', 'karyawans', 'suratJalans'));
    }

    /**
     * Create payment from invoice selection
     */
    public function createFromInvoice()
    {
        // Get all invoices except paid ones
        $invoices = InvoiceAktivitasLain::where('status', '!=', 'paid')
            ->orderBy('tanggal_invoice', 'desc')
            ->get();

        // Get akun COA options
        $akunCoas = DB::table('akun_coa')
            ->orderBy('kode_nomor')
            ->get();

        // Get bank accounts
        $akunBanks = DB::table('akun_coa')
            ->where(function($q) {
                $q->where('tipe_akun', 'like', '%kas%')
                  ->orWhere('tipe_akun', 'like', '%bank%');
            })
            ->orderBy('kode_nomor')
            ->get();

        return view('pembayaran-aktivitas-lain.create-invoice', compact('invoices', 'akunCoas', 'akunBanks'));
    }

    /**
     * Store payment from invoice
     */
    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'selected_invoices' => 'required|array|min:1',
            'selected_invoices.*' => 'exists:invoice_aktivitas_lain,id',
            'tanggal' => 'required|date',
            'akun_coa_id' => 'required|exists:akun_coa,id',
            'akun_bank_id' => 'required|exists:akun_coa,id',
            'debit_kredit' => 'required|in:debit,kredit',
            'jumlah' => 'required|numeric|min:0',
            'invoice_ids' => 'required|string',
            'jenis_aktivitas' => 'nullable|string',
            'penerima' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'nomor_accurate' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor pembayaran
            $nomor = PembayaranAktivitasLain::generateNomor();

            // Get selected invoices
            $invoiceIds = $request->selected_invoices;
            $invoices = InvoiceAktivitasLain::whereIn('id', $invoiceIds)->get();
            
            // Calculate total from selected invoices
            $totalInvoice = $invoices->sum('total');

            // Create payment record - save to pembayaran_aktivitas_lains table
            $pembayaran = PembayaranAktivitasLain::create([
                'nomor' => $nomor,
                'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                'tanggal' => $validated['tanggal'],
                'jenis_aktivitas' => $validated['jenis_aktivitas'] ?? 'Pembayaran Multiple Invoice',
                'penerima' => $validated['penerima'] ?? 'Multiple',
                'jumlah' => $validated['jumlah'],
                'debit_kredit' => $validated['debit_kredit'],
                'akun_coa_id' => $validated['akun_coa_id'],
                'akun_bank_id' => $validated['akun_bank_id'],
                'keterangan' => $validated['keterangan'] ?? 'Pembayaran dari ' . count($invoiceIds) . ' invoice',
                'invoice_ids' => $validated['invoice_ids'],
                'created_by' => Auth::id(),
            ]);

            // Attach invoices to payment with amount (many-to-many relationship)
            foreach ($invoices as $invoice) {
                $pembayaran->invoices()->attach($invoice->id, [
                    'jumlah_dibayar' => $invoice->total,
                ]);

                // Update invoice status
                $invoice->update([
                    'status' => 'paid',
                ]);
            }

            // Create double book journal entries
            $this->createDoubleBookJournalFromInvoice($pembayaran, $validated);

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lain.index')
                ->with('success', 'Pembayaran dari ' . count($invoiceIds) . ' invoice berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'jenis_aktivitas' => 'required|string|max:255',
            'jenis_penyesuaian' => 'nullable|string|max:255',
            'tipe_penyesuaian' => 'nullable|array',
            'tipe_penyesuaian.*' => 'string|in:mel,parkir,pelancar,kawalan,krani',
            'tipe_penyesuaian_detail' => 'nullable|array',
            'tipe_penyesuaian_detail.*.tipe' => 'required|string|in:mel,parkir,pelancar,kawalan,krani',
            'tipe_penyesuaian_detail.*.nominal' => 'required|integer|min:0',
            'sub_jenis_kendaraan' => 'nullable|string|max:255',
            'nomor_polisi' => 'nullable|string|max:255',
            'nomor_voyage' => 'nullable|string|max:255',
            'no_surat_jalan' => 'nullable|string|max:255',
            'penerima' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'jumlah' => 'required|integer|min:0',
            'debit_kredit' => 'required|in:debit,kredit',
            'akun_coa_id' => 'required|exists:akun_coa,id',
            'akun_bank_id' => 'required|exists:akun_coa,id',
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor pembayaran
            $validated['nomor'] = PembayaranAktivitasLain::generateNomor();
            $validated['created_by'] = Auth::id();

            // Create main payment record
            $pembayaran = PembayaranAktivitasLain::create($validated);

            // Implement Double Book Accounting
            $this->createDoubleBookJournal($pembayaran, $validated);

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lain.index')
                ->with('success', 'Data pembayaran berhasil disimpan dengan jurnal double book accounting');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Create Double Book Accounting Journal Entries for Invoice Payment
     */
    private function createDoubleBookJournalInvoice($pembayaran, $validated)
    {
        // Get account information
        $akunCoa = Coa::find($validated['akun_coa_id']);
        $akunBank = Coa::find($validated['akun_bank_id']);
        
        $tanggal = $validated['tanggal'];
        $jumlah = $validated['jumlah'];
        $jenisTransaksi = $validated['debit_kredit'];
        $nomorReferensi = $pembayaran->nomor;
        $keterangan = "Pembayaran Invoice Aktivitas Lain - {$pembayaran->jenis_aktivitas}";
        $jenisTransaksiDesc = 'Pembayaran Invoice Aktivitas Lain';

        // Create journal entries based on debit/credit selection
        if ($jenisTransaksi === 'debit') {
            // DEBIT: Increase expense/cost account, decrease bank account
            // Dr. Expense Account (+)
            $this->createCoaTransaction([
                'coa_id' => $akunCoa->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan . ' - ' . ($validated['keterangan'] ?? ''),
                'debit' => $jumlah,
                'kredit' => 0,
                'saldo' => $akunCoa->saldo + $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunCoa->update(['saldo' => $akunCoa->saldo + $jumlah]);

            // Cr. Bank Account (-)
            $this->createCoaTransaction([
                'coa_id' => $akunBank->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan . ' - ' . ($validated['keterangan'] ?? ''),
                'debit' => 0,
                'kredit' => $jumlah,
                'saldo' => $akunBank->saldo - $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunBank->update(['saldo' => $akunBank->saldo - $jumlah]);
        } else {
            // KREDIT: Increase bank account, decrease expense/cost account
            // Dr. Bank Account (+)
            $this->createCoaTransaction([
                'coa_id' => $akunBank->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan . ' - ' . ($validated['keterangan'] ?? ''),
                'debit' => $jumlah,
                'kredit' => 0,
                'saldo' => $akunBank->saldo + $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunBank->update(['saldo' => $akunBank->saldo + $jumlah]);

            // Cr. Expense Account (-)
            $this->createCoaTransaction([
                'coa_id' => $akunCoa->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan . ' - ' . ($validated['keterangan'] ?? ''),
                'debit' => 0,
                'kredit' => $jumlah,
                'saldo' => $akunCoa->saldo - $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunCoa->update(['saldo' => $akunCoa->saldo - $jumlah]);
        }
    }

    /**
     * Create Double Book Accounting Journal Entries
     */
    private function createDoubleBookJournal($pembayaran, $validated)
    {
        // Get account information
        $akunCoa = Coa::find($validated['akun_coa_id']);
        $akunBank = Coa::find($validated['akun_bank_id']);
        
        $tanggal = $validated['tanggal'];
        $jumlah = $validated['jumlah'];
        $jenisTransaksi = $validated['debit_kredit'];
        $nomorReferensi = $pembayaran->nomor;
        $keterangan = "Pembayaran Aktivitas Lain - {$validated['jenis_aktivitas']}";
        $jenisTransaksiDesc = 'Pembayaran Aktivitas Lain';

        // Create journal entries based on debit/credit selection
        if ($jenisTransaksi === 'debit') {
            // DEBIT: Increase expense/cost account, decrease bank account
            // Dr. Expense Account (+)
            $this->createCoaTransaction([
                'coa_id' => $akunCoa->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan . ' - ' . $validated['keterangan'],
                'debit' => $jumlah,
                'kredit' => 0,
                'saldo' => $akunCoa->saldo + $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunCoa->update(['saldo' => $akunCoa->saldo + $jumlah]);

            // Cr. Bank Account (-)
            $this->createCoaTransaction([
                'coa_id' => $akunBank->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan . ' - ' . $validated['keterangan'],
                'debit' => 0,
                'kredit' => $jumlah,
                'saldo' => $akunBank->saldo - $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunBank->update(['saldo' => $akunBank->saldo - $jumlah]);
        } else {
            // KREDIT: Increase bank account, decrease expense/cost account
            // Dr. Bank Account (+)
            $this->createCoaTransaction([
                'coa_id' => $akunBank->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan . ' - ' . $validated['keterangan'],
                'debit' => $jumlah,
                'kredit' => 0,
                'saldo' => $akunBank->saldo + $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunBank->update(['saldo' => $akunBank->saldo + $jumlah]);

            // Cr. Expense Account (-)
            $this->createCoaTransaction([
                'coa_id' => $akunCoa->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan . ' - ' . $validated['keterangan'],
                'debit' => 0,
                'kredit' => $jumlah,
                'saldo' => $akunCoa->saldo - $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunCoa->update(['saldo' => $akunCoa->saldo - $jumlah]);
        }
    }

    /**
     * Create individual COA transaction entry
     */
    private function createCoaTransaction($data)
    {
        return CoaTransaction::create($data);
    }

    public function show(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        $pembayaranAktivitasLain->load(['creator', 'approver']);
        return view('pembayaran-aktivitas-lain.show', compact('pembayaranAktivitasLain'));
    }

    public function edit(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        $akunBiaya = DB::table('akun_coa')
            ->where(function($q) {
                $q->where('tipe_akun', 'like', '%biaya%')
                  ->orWhere('tipe_akun', 'like', '%beban%');
            })
            ->orderBy('kode_nomor')
            ->get();

        return view('pembayaran-aktivitas-lain.edit', compact('pembayaranAktivitasLain', 'akunBiaya'));
    }

    public function update(Request $request, PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis_aktivitas' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'jumlah' => 'required|integer|min:0',
            'metode_pembayaran' => 'required|string',
        ]);

        try {
            $pembayaranAktivitasLain->update($validated);

            return redirect()->route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain)
                ->with('success', 'Data pembayaran berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    public function destroy(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        try {
            $pembayaranAktivitasLain->delete();
            return redirect()->route('pembayaran-aktivitas-lain.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Print list (filtered) of Pembayaran Aktivitas Lain
     */
    public function printIndex(Request $request)
    {
        $query = PembayaranAktivitasLain::with(['creator', 'approver']);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                  ->orWhere('nomor_accurate', 'like', "%{$search}%")
                  ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->get();

        // Prepare akun map similar to index
        $akunIds = $pembayarans->pluck('akun_coa_id')->filter()->merge($pembayarans->pluck('akun_bank_id')->filter())->unique();
        $akunCoas = DB::table('akun_coa')->whereIn('id', $akunIds)->get()->keyBy('id');

        return view('pembayaran-aktivitas-lain.print', compact('pembayarans', 'akunCoas'));
    }

    /**
     * Print a single Pembayaran Aktivitas Lain
     */
    public function print(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        $pembayaranAktivitasLain->load(['creator', 'approver', 'invoices']);

        $akunCoas = DB::table('akun_coa')->whereIn('id', [$pembayaranAktivitasLain->akun_coa_id, $pembayaranAktivitasLain->akun_bank_id])->get()->keyBy('id');

        return view('pembayaran-aktivitas-lain.print-single', compact('pembayaranAktivitasLain', 'akunCoas'));
    }

    /**
     * Create double book journal from invoice payment
     */
    private function createDoubleBookJournalFromInvoice($pembayaran, $validated)
    {
        // Debit: Account (COA)
        CoaTransaction::create([
            'coa_id' => $validated['akun_coa_id'],
            'tanggal_transaksi' => $validated['tanggal'],
            'debit' => $validated['debit_kredit'] === 'debit' ? $validated['jumlah'] : 0,
            'kredit' => $validated['debit_kredit'] === 'kredit' ? $validated['jumlah'] : 0,
            'saldo' => 0, // Will be calculated by observer
            'keterangan' => $validated['keterangan'] ?? 'Pembayaran invoice: ' . $pembayaran->nomor,
            'nomor_referensi' => $pembayaran->nomor,
            'jenis_transaksi' => 'Pembayaran Aktivitas Lain - Invoice',
            'created_by' => Auth::id(),
        ]);

        // Credit: Bank Account
        CoaTransaction::create([
            'coa_id' => $validated['akun_bank_id'],
            'tanggal_transaksi' => $validated['tanggal'],
            'debit' => $validated['debit_kredit'] === 'kredit' ? $validated['jumlah'] : 0,
            'kredit' => $validated['debit_kredit'] === 'debit' ? $validated['jumlah'] : 0,
            'saldo' => 0, // Will be calculated by observer
            'keterangan' => $validated['keterangan'] ?? 'Pembayaran invoice: ' . $pembayaran->nomor,
            'nomor_referensi' => $pembayaran->nomor,
            'jenis_transaksi' => 'Pembayaran Aktivitas Lain - Invoice',
            'created_by' => Auth::id(),
        ]);
    }
}
