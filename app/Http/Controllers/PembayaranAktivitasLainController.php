<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\InvoiceAktivitasLain;
use App\Models\PembayaranAktivitasLain;
use App\Models\PembayaranInvoiceAktivitasLain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
                $query->where(function ($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                        ->orWhere('nomor_accurate', 'like', "%{$search}%")
                        ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $pembayarans = $query->orderBy('created_at', 'desc')->paginate(20)->through(function ($item) {
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
                $query->where(function ($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                        ->orWhere('nomor_accurate', 'like', "%{$search}%")
                        ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $pembayarans = $query->orderBy('created_at', 'desc')->paginate(20)->through(function ($item) {
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
                $directQuery->where(function ($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                        ->orWhere('nomor_accurate', 'like', "%{$search}%")
                        ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
                $invoiceQuery->where(function ($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                        ->orWhere('nomor_accurate', 'like', "%{$search}%")
                        ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $directPayments = $directQuery->get()->map(function ($item) {
                $item->tipe_pembayaran = 'langsung';

                return $item;
            });

            $invoicePayments = $invoiceQuery->get()->map(function ($item) {
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
            ->select('id', 'nomor_polisi', 'merek', 'jenis', 'no_kir')
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
            $allVoyages->push((object) [
                'voyage' => $voyage->voyage,
                'nama_kapal' => $voyage->nama_kapal,
                'source' => 'BL',
            ]);
        }

        foreach ($voyagesPergerakan as $voyage) {
            // Only add if not already exists
            $exists = $allVoyages->where('voyage', $voyage->voyage)
                ->where('nama_kapal', $voyage->nama_kapal)
                ->first();
            if (! $exists) {
                $allVoyages->push((object) [
                    'voyage' => $voyage->voyage,
                    'nama_kapal' => $voyage->nama_kapal,
                    'source' => 'Pergerakan Kapal',
                ]);
            }
        }

        $voyages = $allVoyages->sortBy('voyage')->values();

        $akunBank = DB::table('akun_coa')
            ->where(function ($q) {
                $q->where('tipe_akun', 'like', '%kas%')
                    ->orWhere('tipe_akun', 'like', '%bank%');
            })
            ->orderBy('kode_nomor')
            ->get();

        $karyawans = DB::table('karyawans')
            ->select('id', 'nama_lengkap', 'pekerjaan')
            ->orderBy('nama_lengkap')
            ->get();

        // Get surat jalans for adjustment payments from surat_jalans table
        $suratJalansRegular = DB::table('surat_jalans')
            ->leftJoin('uang_jalans', 'surat_jalans.id', '=', 'uang_jalans.surat_jalan_id')
            ->select(
                'surat_jalans.id',
                'surat_jalans.no_surat_jalan',
                'surat_jalans.tujuan_pengiriman',
                DB::raw('COALESCE(uang_jalans.jumlah_total, 0) as uang_jalan'),
                DB::raw("'regular' as source")
            )
            ->whereNotNull('surat_jalans.no_surat_jalan')
            ->where('surat_jalans.no_surat_jalan', '!=', '')
            ->get();

        // Get surat jalans for adjustment payments from surat_jalan_bongkarans table
        $suratJalansBongkar = DB::table('surat_jalan_bongkarans')
            ->leftJoin('uang_jalans', 'surat_jalan_bongkarans.id', '=', 'uang_jalans.surat_jalan_bongkaran_id')
            ->select(
                'surat_jalan_bongkarans.id',
                DB::raw('surat_jalan_bongkarans.nomor_surat_jalan as no_surat_jalan'),
                'surat_jalan_bongkarans.tujuan_pengiriman',
                DB::raw('COALESCE(uang_jalans.jumlah_total, 0) as uang_jalan'),
                DB::raw("'bongkar' as source")
            )
            ->whereNotNull('surat_jalan_bongkarans.nomor_surat_jalan')
            ->where('surat_jalan_bongkarans.nomor_surat_jalan', '!=', '')
            ->get();

        // Combine both surat jalans
        $suratJalans = $suratJalansRegular->merge($suratJalansBongkar)
            ->sortBy('no_surat_jalan')
            ->values();

        $akunPph = DB::table('akun_coa')->orderBy('nomor_akun')->get();

        return view('pembayaran-aktivitas-lain.create', compact('nomor', 'akunBiaya', 'akunBank', 'akunPph', 'mobils', 'voyages', 'karyawans', 'suratJalans'));
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
            ->where(function ($q) {
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

        $maxAttempts = 3;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::beginTransaction();

                // Generate nomor pembayaran (uses lockForUpdate inside transaction)
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
                    'keterangan' => $validated['keterangan'] ?? 'Pembayaran dari '.count($invoiceIds).' invoice',
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
                    ->with('success', 'Pembayaran dari '.count($invoiceIds).' invoice berhasil disimpan');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                // Retry on duplicate nomor (race condition) up to $maxAttempts times
                if ($e->errorInfo[1] === 1062 && $attempt < $maxAttempts) {
                    continue;
                }

                return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: '.$e->getMessage());
            } catch (\Exception $e) {
                DB::rollBack();

                return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: '.$e->getMessage());
            }
        }

        return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: tidak dapat menghasilkan nomor unik setelah beberapa percobaan.');
    }

    public function store(Request $request)
    {
        $requiresDoubleBook = PembayaranAktivitasLain::requiresDoubleBook($request->input('jenis_aktivitas'), $request->input('lokasi'));
        $hasAkunDetails = $request->input('jenis_aktivitas') === 'Pembayaran Lain Lain';
        $isKirPayment = $request->input('jenis_aktivitas') === 'Pembayaran Kendaraan'
            && $request->input('sub_jenis_kendaraan') === 'KIR';
        if ($isKirPayment) {
            $request->merge([
                'jumlah' => collect($request->input('kendaraan_details', []))->sum('nominal'),
            ]);
        }
        $validVehicleNumbers = DB::table('mobils')
            ->get(['nomor_polisi', 'no_kir'])
            ->map(fn ($mobil) => $mobil->nomor_polisi ?: $mobil->no_kir)
            ->filter()
            ->values()
            ->all();
        $validated = $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'jenis_aktivitas' => 'required|string|max:255',
            'lokasi' => 'required_if:jenis_aktivitas,Pembayaran Kendaraan|nullable|in:jakarta,batam',
            'pph_persen' => 'required_if:jenis_aktivitas,Pembayaran Kendaraan|nullable|in:0,2,2.5',
            'pph_manual' => 'nullable|boolean',
            'pph_nominal' => 'required_if:pph_manual,1|nullable|integer|min:0|lte:jumlah',
            'jenis_penyesuaian' => 'nullable|string|max:255',
            'tipe_penyesuaian' => 'nullable|array',
            'tipe_penyesuaian.*' => 'string|in:mel,parkir,pelancar,kawalan,krani',
            'tipe_penyesuaian_detail' => 'nullable|array',
            'tipe_penyesuaian_detail.*.tipe' => 'required|string|in:mel,parkir,pelancar,kawalan,krani',
            'tipe_penyesuaian_detail.*.nominal' => 'required|integer|min:0',
            'sub_jenis_kendaraan' => 'nullable|string|max:255',
            'nomor_polisi' => 'nullable|string|max:255',
            'nomor_polisi_list' => [Rule::requiredIf($request->input('sub_jenis_kendaraan') === 'Lain Lain' || $isKirPayment), 'nullable', 'array', 'min:1'],
            'nomor_polisi_list.*' => 'required|string|max:255|distinct',
            'kendaraan_details' => [Rule::requiredIf($isKirPayment), 'nullable', 'array', 'min:1'],
            'kendaraan_details.*.nomor_polisi' => ['required', 'string', 'max:255', 'distinct', Rule::in($validVehicleNumbers)],
            'kendaraan_details.*.nominal' => 'required|integer|min:1',
            'nomor_voyage' => 'nullable|string|max:255',
            'no_surat_jalan' => 'nullable|string|max:255',
            'penerima' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'jumlah' => ($hasAkunDetails ? 'nullable' : 'required').'|integer|min:0',
            'debit_kredit' => ($requiresDoubleBook && ! $hasAkunDetails ? 'required' : 'nullable').'|in:debit,kredit',
            'akun_coa_id' => ($requiresDoubleBook && ! $hasAkunDetails ? 'required' : 'nullable').'|exists:akun_coa,id',
            'akun_details' => [Rule::requiredIf($hasAkunDetails), 'nullable', 'array', 'min:1'],
            'akun_details.*.akun_coa_id' => ['required', 'integer', 'exists:akun_coa,id', 'different:akun_bank_id'],
            'akun_details.*.debit_kredit' => [Rule::requiredIf($hasAkunDetails), 'in:debit,kredit'],
            'akun_details.*.nominal' => ['required', 'integer', 'min:1'],
            'akun_details.*.memo' => ['nullable', 'string', 'max:500'],
            'akun_bank_id' => ($requiresDoubleBook ? 'required' : 'nullable').'|exists:akun_coa,id',
            'akun_pph_id' => [Rule::requiredIf($request->input('jenis_aktivitas') === 'Pembayaran Kendaraan' && (float) $request->input('pph_persen') > 0), 'nullable', 'exists:akun_coa,id', 'different:akun_coa_id', 'different:akun_bank_id'],
        ]);

        $validated['lokasi'] = $validated['jenis_aktivitas'] === 'Pembayaran Kendaraan'
            ? $validated['lokasi']
            : null;
        $validated['pph_persen'] = $validated['jenis_aktivitas'] === 'Pembayaran Kendaraan'
            ? $validated['pph_persen']
            : null;
        $validated['pph_manual'] = $validated['jenis_aktivitas'] === 'Pembayaran Kendaraan'
            && (bool) ($validated['pph_manual'] ?? false);
        if ($validated['jenis_aktivitas'] !== 'Pembayaran Kendaraan' || (float) $validated['pph_persen'] <= 0) {
            $validated['akun_pph_id'] = null;
        }
        if (! $requiresDoubleBook) {
            $validated['debit_kredit'] = null;
            $validated['akun_coa_id'] = null;
            $validated['akun_bank_id'] = null;
        }
        if ($hasAkunDetails) {
            $validated['akun_details'] = array_values($validated['akun_details']);
            $net = $this->akunDetailsNet($validated['akun_details']);
            if ($net === 0) {
                throw ValidationException::withMessages(['akun_details' => 'Selisih debit dan kredit harus lebih dari nol agar Bank/Kas memiliki nilai transaksi.']);
            }
            $validated['jumlah'] = abs($net);
            $validated['debit_kredit'] = $net > 0 ? 'kredit' : 'debit';
            $validated['akun_coa_id'] = $validated['akun_details'][0]['akun_coa_id'];
        } else {
            $validated['akun_details'] = null;
        }
        $validated['kendaraan_details'] = $isKirPayment
            ? array_values($validated['kendaraan_details'])
            : null;
        if ($isKirPayment) {
            $validated['nomor_polisi_list'] = array_column($validated['kendaraan_details'], 'nomor_polisi');
            $validated['jumlah'] = array_sum(array_column($validated['kendaraan_details'], 'nominal'));
        } else {
            $validated['nomor_polisi_list'] = $validated['jenis_aktivitas'] === 'Pembayaran Kendaraan'
                && $validated['sub_jenis_kendaraan'] === 'Lain Lain'
                    ? array_values($validated['nomor_polisi_list'])
                    : null;
        }
        if ($validated['nomor_polisi_list']) {
            $validated['nomor_polisi'] = $validated['nomor_polisi_list'][0];
        }

        $maxAttempts = 3;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::beginTransaction();

                // Generate nomor pembayaran (uses lockForUpdate inside transaction)
                $validated['nomor'] = PembayaranAktivitasLain::generateNomor();
                $validated['created_by'] = Auth::id();

                // Create main payment record
                $pembayaran = PembayaranAktivitasLain::create($validated);

                // Implement Double Book Accounting
                if ($requiresDoubleBook) {
                    $this->createDoubleBookJournal($pembayaran, $validated);
                }

                // Logic to delete Prospek record if "Pembayaran Adjusment Uang Jalan" / "Pembayaran Adjustment Uang Jalan" and "pengembalian penuh"
                $isAdjustment = in_array($validated['jenis_aktivitas'], ['Pembayaran Adjusment Uang Jalan', 'Pembayaran Adjustment Uang Jalan']);
                if ($isAdjustment && ($validated['jenis_penyesuaian'] ?? null) === 'pengembalian penuh') {
                    if (! empty($validated['no_surat_jalan'])) {
                        \App\Models\Prospek::where('no_surat_jalan', $validated['no_surat_jalan'])->delete();
                    }
                }

                DB::commit();

                return redirect()->route('pembayaran-aktivitas-lain.index')
                    ->with('success', 'Data pembayaran berhasil disimpan'.($requiresDoubleBook ? ' dengan jurnal double book accounting' : '').
                        (($isAdjustment && ($validated['jenis_penyesuaian'] ?? null) === 'pengembalian penuh') ? ' dan Data Prospek dihapus' : ''));

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                // Retry on duplicate nomor (race condition) up to $maxAttempts times
                if ($e->errorInfo[1] === 1062 && $attempt < $maxAttempts) {
                    continue;
                }

                return back()->withInput()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
            } catch (\Exception $e) {
                DB::rollBack();

                return back()->withInput()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
            }
        }

        return back()->withInput()->with('error', 'Gagal menyimpan data: tidak dapat menghasilkan nomor unik setelah beberapa percobaan.');
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
        if ($jenisTransaksi === 'kredit') {
            // KREDIT: Increase expense/cost account, decrease bank account
            // Dr. Expense Account (+)
            $this->createCoaTransaction([
                'coa_id' => $akunCoa->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan.' - '.($validated['keterangan'] ?? ''),
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
                'keterangan' => $keterangan.' - '.($validated['keterangan'] ?? ''),
                'debit' => 0,
                'kredit' => $jumlah,
                'saldo' => $akunBank->saldo - $jumlah,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunBank->update(['saldo' => $akunBank->saldo - $jumlah]);
        } else {
            // DEBIT: Increase bank account, decrease expense/cost account
            // Dr. Bank Account (+)
            $this->createCoaTransaction([
                'coa_id' => $akunBank->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan.' - '.($validated['keterangan'] ?? ''),
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
                'keterangan' => $keterangan.' - '.($validated['keterangan'] ?? ''),
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
        $akunBank = Coa::find($validated['akun_bank_id']);
        $pphNominal = (int) $pembayaran->pph_nominal;
        $jumlahBank = (int) $pembayaran->grand_total;
        $akunPph = $pphNominal > 0 ? Coa::findOrFail($pembayaran->akun_pph_id) : null;

        $tanggal = $validated['tanggal'];
        $jumlah = $validated['jumlah'];
        $jenisTransaksi = $validated['debit_kredit'];
        $nomorReferensi = $pembayaran->nomor;
        $keterangan = "Pembayaran Aktivitas Lain - {$validated['jenis_aktivitas']}";
        $jenisTransaksiDesc = 'Pembayaran Aktivitas Lain';
        $akunDetails = $validated['akun_details'] ?? [[
            'akun_coa_id' => $validated['akun_coa_id'],
            'nominal' => $jumlah,
        ]];

        if (isset($akunDetails[0]['debit_kredit'])) {
            $net = $this->akunDetailsNet($akunDetails);
            $this->createExpenseJournalLines($akunDetails, $tanggal, $nomorReferensi, $jenisTransaksiDesc, $keterangan.' - '.$validated['keterangan'], true);
            $bankDebit = $net < 0;
            $bankAmount = abs($net);
            $bankSaldo = $akunBank->saldo + ($bankDebit ? $bankAmount : -$bankAmount);
            $this->createCoaTransaction([
                'coa_id' => $akunBank->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan.' - '.$validated['keterangan'],
                'debit' => $bankDebit ? $bankAmount : 0,
                'kredit' => $bankDebit ? 0 : $bankAmount,
                'saldo' => $bankSaldo,
                'created_by' => Auth::id(),
            ]);
            $akunBank->update(['saldo' => $bankSaldo]);

            return;
        }

        // Create journal entries based on debit/credit selection
        if ($jenisTransaksi === 'kredit') {
            // KREDIT: Increase expense/cost account, decrease bank account
            // Dr. Expense Account (+)
            $this->createExpenseJournalLines($akunDetails, $tanggal, $nomorReferensi, $jenisTransaksiDesc, $keterangan.' - '.$validated['keterangan'], true);

            // Cr. Bank Account (-)
            $this->createCoaTransaction([
                'coa_id' => $akunBank->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan.' - '.$validated['keterangan'],
                'debit' => 0,
                'kredit' => $jumlahBank,
                'saldo' => $akunBank->saldo - $jumlahBank,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunBank->update(['saldo' => $akunBank->saldo - $jumlahBank]);
            if ($akunPph) {
                $this->createCoaTransaction([
                    'coa_id' => $akunPph->id,
                    'tanggal_transaksi' => $tanggal,
                    'nomor_referensi' => $nomorReferensi,
                    'jenis_transaksi' => $jenisTransaksiDesc,
                    'keterangan' => $keterangan.' - Potongan PPh',
                    'debit' => 0,
                    'kredit' => $pphNominal,
                    'saldo' => $akunPph->saldo - $pphNominal,
                    'created_by' => Auth::id(),
                ]);
                $akunPph->update(['saldo' => $akunPph->saldo - $pphNominal]);
            }
        } else {
            // DEBIT: Increase bank account, decrease expense/cost account
            // Dr. Bank Account (+)
            $this->createCoaTransaction([
                'coa_id' => $akunBank->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksiDesc,
                'keterangan' => $keterangan.' - '.$validated['keterangan'],
                'debit' => $jumlahBank,
                'kredit' => 0,
                'saldo' => $akunBank->saldo + $jumlahBank,
                'created_by' => Auth::id(),
            ]);

            // Update account balance
            $akunBank->update(['saldo' => $akunBank->saldo + $jumlahBank]);

            // Cr. Expense Account (-)
            $this->createExpenseJournalLines($akunDetails, $tanggal, $nomorReferensi, $jenisTransaksiDesc, $keterangan.' - '.$validated['keterangan'], false);
            if ($akunPph) {
                $this->createCoaTransaction([
                    'coa_id' => $akunPph->id,
                    'tanggal_transaksi' => $tanggal,
                    'nomor_referensi' => $nomorReferensi,
                    'jenis_transaksi' => $jenisTransaksiDesc,
                    'keterangan' => $keterangan.' - Pengembalian PPh',
                    'debit' => $pphNominal,
                    'kredit' => 0,
                    'saldo' => $akunPph->saldo + $pphNominal,
                    'created_by' => Auth::id(),
                ]);
                $akunPph->update(['saldo' => $akunPph->saldo + $pphNominal]);
            }
        }
    }

    private function createExpenseJournalLines(array $details, $tanggal, string $nomorReferensi, string $jenisTransaksi, string $keterangan, bool $isDebit): void
    {
        foreach ($details as $detail) {
            $lineIsDebit = isset($detail['debit_kredit']) ? $detail['debit_kredit'] === 'debit' : $isDebit;
            $akun = Coa::findOrFail($detail['akun_coa_id']);
            $nominal = (int) $detail['nominal'];
            $saldo = $akun->saldo + ($lineIsDebit ? $nominal : -$nominal);
            $memo = trim((string) ($detail['memo'] ?? ''));
            $this->createCoaTransaction([
                'coa_id' => $akun->id,
                'tanggal_transaksi' => $tanggal,
                'nomor_referensi' => $nomorReferensi,
                'jenis_transaksi' => $jenisTransaksi,
                'keterangan' => $memo === '' ? $keterangan : $keterangan.' - '.$memo,
                'debit' => $lineIsDebit ? $nominal : 0,
                'kredit' => $lineIsDebit ? 0 : $nominal,
                'saldo' => $saldo,
                'created_by' => Auth::id(),
            ]);
            $akun->update(['saldo' => $saldo]);
        }
    }

    private function akunDetailsNet(array $details): int
    {
        return array_reduce($details, fn (int $net, array $detail) => $net
            + ($detail['debit_kredit'] === 'debit' ? 1 : -1) * (int) $detail['nominal'], 0);
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
        $pembayaranAktivitasLain->load(['creator', 'approver', 'akunPph']);

        return view('pembayaran-aktivitas-lain.show', compact('pembayaranAktivitasLain'));
    }

    public function edit(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        $akunBiaya = DB::table('akun_coa')
            ->orderBy('kode_nomor')
            ->get();

        $mobils = DB::table('mobils')
            ->select('id', 'nomor_polisi', 'merek', 'jenis', 'no_kir')
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
            $allVoyages->push((object) [
                'voyage' => $voyage->voyage,
                'nama_kapal' => $voyage->nama_kapal,
                'source' => 'BL',
            ]);
        }

        foreach ($voyagesPergerakan as $voyage) {
            // Only add if not already exists
            $exists = $allVoyages->where('voyage', $voyage->voyage)
                ->where('nama_kapal', $voyage->nama_kapal)
                ->first();
            if (! $exists) {
                $allVoyages->push((object) [
                    'voyage' => $voyage->voyage,
                    'nama_kapal' => $voyage->nama_kapal,
                    'source' => 'Pergerakan Kapal',
                ]);
            }
        }

        $voyages = $allVoyages->sortBy('voyage')->values();

        $akunBank = DB::table('akun_coa')
            ->where(function ($q) {
                $q->where('tipe_akun', 'like', '%kas%')
                    ->orWhere('tipe_akun', 'like', '%bank%');
            })
            ->orderBy('kode_nomor')
            ->get();

        $karyawans = DB::table('karyawans')
            ->select('id', 'nama_lengkap', 'pekerjaan')
            ->orderBy('nama_lengkap')
            ->get();

        // Get surat jalans for adjustment payments from surat_jalans table
        $suratJalansRegular = DB::table('surat_jalans')
            ->leftJoin('uang_jalans', 'surat_jalans.id', '=', 'uang_jalans.surat_jalan_id')
            ->select(
                'surat_jalans.id',
                'surat_jalans.no_surat_jalan',
                'surat_jalans.tujuan_pengiriman',
                DB::raw('COALESCE(uang_jalans.jumlah_total, 0) as uang_jalan'),
                DB::raw("'regular' as source")
            )
            ->whereNotNull('surat_jalans.no_surat_jalan')
            ->where('surat_jalans.no_surat_jalan', '!=', '')
            ->get();

        // Get surat jalans for adjustment payments from surat_jalan_bongkarans table
        $suratJalansBongkar = DB::table('surat_jalan_bongkarans')
            ->leftJoin('uang_jalans', 'surat_jalan_bongkarans.id', '=', 'uang_jalans.surat_jalan_bongkaran_id')
            ->select(
                'surat_jalan_bongkarans.id',
                DB::raw('surat_jalan_bongkarans.nomor_surat_jalan as no_surat_jalan'),
                'surat_jalan_bongkarans.tujuan_pengiriman',
                DB::raw('COALESCE(uang_jalans.jumlah_total, 0) as uang_jalan'),
                DB::raw("'bongkar' as source")
            )
            ->whereNotNull('surat_jalan_bongkarans.nomor_surat_jalan')
            ->where('surat_jalan_bongkarans.nomor_surat_jalan', '!=', '')
            ->get();

        // Combine both surat jalans
        $suratJalans = $suratJalansRegular->merge($suratJalansBongkar)
            ->sortBy('no_surat_jalan')
            ->values();

        $akunPph = DB::table('akun_coa')->orderBy('nomor_akun')->get();

        return view('pembayaran-aktivitas-lain.edit', compact(
            'pembayaranAktivitasLain',
            'akunBiaya',
            'akunBank',
            'akunPph',
            'mobils',
            'voyages',
            'karyawans',
            'suratJalans'
        ));
    }

    public function update(Request $request, PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        $requiresDoubleBook = PembayaranAktivitasLain::requiresDoubleBook($request->input('jenis_aktivitas'), $request->input('lokasi'));
        $hasAkunDetails = $request->input('jenis_aktivitas') === 'Pembayaran Lain Lain';
        $isKirPayment = $request->input('jenis_aktivitas') === 'Pembayaran Kendaraan'
            && $request->input('sub_jenis_kendaraan') === 'KIR';
        if ($isKirPayment) {
            $request->merge([
                'jumlah' => collect($request->input('kendaraan_details', []))->sum('nominal'),
            ]);
        }
        $validVehicleNumbers = DB::table('mobils')
            ->get(['nomor_polisi', 'no_kir'])
            ->map(fn ($mobil) => $mobil->nomor_polisi ?: $mobil->no_kir)
            ->filter()
            ->values()
            ->all();
        $validated = $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'jenis_aktivitas' => 'required|string|max:255',
            'lokasi' => 'required_if:jenis_aktivitas,Pembayaran Kendaraan|nullable|in:jakarta,batam',
            'pph_persen' => 'required_if:jenis_aktivitas,Pembayaran Kendaraan|nullable|in:0,2,2.5',
            'pph_manual' => 'nullable|boolean',
            'pph_nominal' => 'required_if:pph_manual,1|nullable|integer|min:0|lte:jumlah',
            'jenis_penyesuaian' => 'nullable|string|max:255',
            'tipe_penyesuaian' => 'nullable|array',
            'tipe_penyesuaian.*' => 'string|in:mel,parkir,pelancar,kawalan,krani',
            'tipe_penyesuaian_detail' => 'nullable|array',
            'tipe_penyesuaian_detail.*.tipe' => 'required|string|in:mel,parkir,pelancar,kawalan,krani',
            'tipe_penyesuaian_detail.*.nominal' => 'required|integer|min:0',
            'sub_jenis_kendaraan' => 'nullable|string|max:255',
            'nomor_polisi' => 'nullable|string|max:255',
            'nomor_polisi_list' => [Rule::requiredIf($request->input('sub_jenis_kendaraan') === 'Lain Lain' || $isKirPayment), 'nullable', 'array', 'min:1'],
            'nomor_polisi_list.*' => 'required|string|max:255|distinct',
            'kendaraan_details' => [Rule::requiredIf($isKirPayment), 'nullable', 'array', 'min:1'],
            'kendaraan_details.*.nomor_polisi' => ['required', 'string', 'max:255', 'distinct', Rule::in($validVehicleNumbers)],
            'kendaraan_details.*.nominal' => 'required|integer|min:1',
            'nomor_voyage' => 'nullable|string|max:255',
            'no_surat_jalan' => 'nullable|string|max:255',
            'penerima' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'jumlah' => ($hasAkunDetails ? 'nullable' : 'required').'|integer|min:0',
            'debit_kredit' => ($requiresDoubleBook && ! $hasAkunDetails ? 'required' : 'nullable').'|in:debit,kredit',
            'akun_coa_id' => ($requiresDoubleBook && ! $hasAkunDetails ? 'required' : 'nullable').'|exists:akun_coa,id',
            'akun_details' => [Rule::requiredIf($hasAkunDetails), 'nullable', 'array', 'min:1'],
            'akun_details.*.akun_coa_id' => ['required', 'integer', 'exists:akun_coa,id', 'different:akun_bank_id'],
            'akun_details.*.debit_kredit' => [Rule::requiredIf($hasAkunDetails), 'in:debit,kredit'],
            'akun_details.*.nominal' => ['required', 'integer', 'min:1'],
            'akun_details.*.memo' => ['nullable', 'string', 'max:500'],
            'akun_bank_id' => ($requiresDoubleBook ? 'required' : 'nullable').'|exists:akun_coa,id',
            'akun_pph_id' => [Rule::requiredIf($request->input('jenis_aktivitas') === 'Pembayaran Kendaraan' && (float) $request->input('pph_persen') > 0), 'nullable', 'exists:akun_coa,id', 'different:akun_coa_id', 'different:akun_bank_id'],
        ]);

        $validated['lokasi'] = $validated['jenis_aktivitas'] === 'Pembayaran Kendaraan'
            ? $validated['lokasi']
            : null;
        $validated['pph_persen'] = $validated['jenis_aktivitas'] === 'Pembayaran Kendaraan'
            ? $validated['pph_persen']
            : null;
        $validated['pph_manual'] = $validated['jenis_aktivitas'] === 'Pembayaran Kendaraan'
            && (bool) ($validated['pph_manual'] ?? false);
        if ($validated['jenis_aktivitas'] !== 'Pembayaran Kendaraan' || (float) $validated['pph_persen'] <= 0) {
            $validated['akun_pph_id'] = null;
        }
        if (! $requiresDoubleBook) {
            $validated['debit_kredit'] = null;
            $validated['akun_coa_id'] = null;
            $validated['akun_bank_id'] = null;
        }
        if ($hasAkunDetails) {
            $validated['akun_details'] = array_values($validated['akun_details']);
            $net = $this->akunDetailsNet($validated['akun_details']);
            if ($net === 0) {
                throw ValidationException::withMessages(['akun_details' => 'Selisih debit dan kredit harus lebih dari nol agar Bank/Kas memiliki nilai transaksi.']);
            }
            $validated['jumlah'] = abs($net);
            $validated['debit_kredit'] = $net > 0 ? 'kredit' : 'debit';
            $validated['akun_coa_id'] = $validated['akun_details'][0]['akun_coa_id'];
        } else {
            $validated['akun_details'] = null;
        }
        $validated['kendaraan_details'] = $isKirPayment
            ? array_values($validated['kendaraan_details'])
            : null;
        if ($isKirPayment) {
            $validated['nomor_polisi_list'] = array_column($validated['kendaraan_details'], 'nomor_polisi');
            $validated['jumlah'] = array_sum(array_column($validated['kendaraan_details'], 'nominal'));
        } else {
            $validated['nomor_polisi_list'] = $validated['jenis_aktivitas'] === 'Pembayaran Kendaraan'
                && $validated['sub_jenis_kendaraan'] === 'Lain Lain'
                    ? array_values($validated['nomor_polisi_list'])
                    : null;
        }
        if ($validated['nomor_polisi_list']) {
            $validated['nomor_polisi'] = $validated['nomor_polisi_list'][0];
        }

        try {
            DB::beginTransaction();

            // Reverse old journal entries balances
            $oldTransactions = CoaTransaction::where('nomor_referensi', $pembayaranAktivitasLain->nomor)->get();
            foreach ($oldTransactions as $transaction) {
                $account = Coa::find($transaction->coa_id);
                if ($account) {
                    if ($transaction->debit > 0) {
                        $account->update(['saldo' => $account->saldo - $transaction->debit]);
                    } elseif ($transaction->kredit > 0) {
                        $account->update(['saldo' => $account->saldo + $transaction->kredit]);
                    }
                }
                $transaction->delete();
            }

            // Update main record
            $pembayaranAktivitasLain->update($validated);

            // Re-create journal entries
            if ($requiresDoubleBook) {
                $this->createDoubleBookJournal($pembayaranAktivitasLain, $validated);
            }

            // Logic to delete Prospek record if "Pembayaran Adjusment Uang Jalan" / "Pembayaran Adjustment Uang Jalan" and "pengembalian penuh"
            $isAdjustment = in_array($validated['jenis_aktivitas'], ['Pembayaran Adjusment Uang Jalan', 'Pembayaran Adjustment Uang Jalan']);
            if ($isAdjustment && ($validated['jenis_penyesuaian'] ?? null) === 'pengembalian penuh') {
                if (! empty($validated['no_surat_jalan'])) {
                    \App\Models\Prospek::where('no_surat_jalan', $validated['no_surat_jalan'])->delete();
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data pembayaran berhasil diupdate',
                    'redirect' => route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain),
                ]);
            }

            return redirect()->route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain)
                ->with('success', 'Data pembayaran berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal update data: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Gagal update data: '.$e->getMessage());
        }
    }

    /**
     * Update only the amount of the payment
     */
    public function updateJumlah(Request $request, PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        if ($pembayaranAktivitasLain->status === 'paid') {
            return redirect()->back()->with('error', 'Tidak dapat mengubah jumlah untuk pembayaran yang sudah status Paid');
        }

        if ($pembayaranAktivitasLain->sub_jenis_kendaraan === 'KIR' && $pembayaranAktivitasLain->kendaraan_details) {
            return redirect()->back()->with('error', 'Jumlah pembayaran KIR harus diubah melalui rincian nominal setiap kendaraan.');
        }
        if ($pembayaranAktivitasLain->jenis_aktivitas === 'Pembayaran Lain Lain') {
            return redirect()->back()->with('error', 'Ubah nominal melalui rincian akun pada halaman edit pembayaran.');
        }

        $request->validate([
            'jumlah' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $newJumlah = (int) $request->jumlah;

            if ($newJumlah === (int) $pembayaranAktivitasLain->jumlah) {
                DB::rollBack();

                return redirect()->back()->with('info', 'Tidak ada perubahan jumlah');
            }

            $transactions = CoaTransaction::where('nomor_referensi', $pembayaranAktivitasLain->nomor)->get();
            foreach ($transactions as $transaction) {
                $account = Coa::find($transaction->coa_id);
                if ($account) {
                    $account->update(['saldo' => $account->saldo - $transaction->debit + $transaction->kredit]);
                }
                $transaction->delete();
            }

            $pembayaranAktivitasLain->update(['jumlah' => $newJumlah, 'pph_manual' => false]);
            if ($pembayaranAktivitasLain->usesDoubleBook()) {
                $this->createDoubleBookJournal($pembayaranAktivitasLain, [
                    'akun_coa_id' => $pembayaranAktivitasLain->akun_coa_id,
                    'akun_bank_id' => $pembayaranAktivitasLain->akun_bank_id,
                    'tanggal' => $pembayaranAktivitasLain->tanggal,
                    'jumlah' => $newJumlah,
                    'debit_kredit' => $pembayaranAktivitasLain->debit_kredit,
                    'jenis_aktivitas' => $pembayaranAktivitasLain->jenis_aktivitas,
                    'keterangan' => $pembayaranAktivitasLain->keterangan,
                ]);
            }

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain)
                ->with('success', 'Jumlah berhasil diperbarui dan jurnal telah disesuaikan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memperbarui jumlah: '.$e->getMessage());
        }
    }

    /**
     * Update only the accurate number of the payment
     */
    public function updateNomorAccurate(Request $request, PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
        ]);

        try {
            $pembayaranAktivitasLain->update([
                'nomor_accurate' => $request->nomor_accurate,
            ]);

            return redirect()->route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain)
                ->with('success', 'Nomor Accurate berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui Nomor Accurate: '.$e->getMessage());
        }
    }

    public function destroy(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        try {
            DB::transaction(function () use ($pembayaranAktivitasLain) {
                $transactions = CoaTransaction::where('nomor_referensi', $pembayaranAktivitasLain->nomor)->get();
                foreach ($transactions as $transaction) {
                    $account = Coa::find($transaction->coa_id);
                    if ($account) {
                        $account->update(['saldo' => $account->saldo - $transaction->debit + $transaction->kredit]);
                    }
                    $transaction->delete();
                }

                $pembayaranAktivitasLain->delete();
            });

            return redirect()->route('pembayaran-aktivitas-lain.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: '.$e->getMessage());
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
            $query->where(function ($q) use ($search) {
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
        $pembayaranAktivitasLain->load(['creator', 'approver', 'invoices.suratJalan']);

        // Jika direct payment (ada no_surat_jalan tapi tidak ada invoice), cari detail surat jalan manual
        if ($pembayaranAktivitasLain->no_surat_jalan && $pembayaranAktivitasLain->invoices->isEmpty()) {
            $sj = \App\Models\SuratJalan::where('no_surat_jalan', $pembayaranAktivitasLain->no_surat_jalan)->first();
            if (! $sj) {
                $sj = \App\Models\SuratJalanBongkaran::where('nomor_surat_jalan', $pembayaranAktivitasLain->no_surat_jalan)->first();
            }
            $pembayaranAktivitasLain->suratJalanDetail = $sj;
        }

        $akunCoas = DB::table('akun_coa')->whereIn('id', [$pembayaranAktivitasLain->akun_coa_id, $pembayaranAktivitasLain->akun_bank_id])->get()->keyBy('id');

        return view('pembayaran-aktivitas-lain.print-single', compact('pembayaranAktivitasLain', 'akunCoas'));
    }

    /**
     * Create double book journal from invoice payment
     */
    private function createDoubleBookJournalFromInvoice($pembayaran, $validated)
    {
        // Account (COA)
        CoaTransaction::create([
            'coa_id' => $validated['akun_coa_id'],
            'tanggal_transaksi' => $validated['tanggal'],
            'debit' => $validated['debit_kredit'] === 'kredit' ? $validated['jumlah'] : 0,
            'kredit' => $validated['debit_kredit'] === 'debit' ? $validated['jumlah'] : 0,
            'saldo' => 0, // Will be calculated by observer
            'keterangan' => $validated['keterangan'] ?? 'Pembayaran invoice: '.$pembayaran->nomor,
            'nomor_referensi' => $pembayaran->nomor,
            'jenis_transaksi' => 'Pembayaran Aktivitas Lain - Invoice',
            'created_by' => Auth::id(),
        ]);

        // Credit: Bank Account
        CoaTransaction::create([
            'coa_id' => $validated['akun_bank_id'],
            'tanggal_transaksi' => $validated['tanggal'],
            'debit' => $validated['debit_kredit'] === 'debit' ? $validated['jumlah'] : 0,
            'kredit' => $validated['debit_kredit'] === 'kredit' ? $validated['jumlah'] : 0,
            'saldo' => 0, // Will be calculated by observer
            'keterangan' => $validated['keterangan'] ?? 'Pembayaran invoice: '.$pembayaran->nomor,
            'nomor_referensi' => $pembayaran->nomor,
            'jenis_transaksi' => 'Pembayaran Aktivitas Lain - Invoice',
            'created_by' => Auth::id(),
        ]);
    }
}
