<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\KodeNomor;
use App\Exports\MasterCoaTemplateExport;
use App\Imports\MasterCoaImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterCoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Coa::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nomor_akun', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kode_nomor', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nama_akun', 'like', '%' . $searchTerm . '%')
                  ->orWhere('tipe_akun', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by tipe_akun
        if ($request->has('tipe_akun') && !empty($request->tipe_akun)) {
            $query->where('tipe_akun', $request->tipe_akun);
        }

        $coas = $query->orderBy('nomor_akun')->paginate(15)->appends($request->except('page'));

        // Get unique tipe_akun for filter dropdown
        $tipeAkuns = Coa::select('tipe_akun')->distinct()->orderBy('tipe_akun')->pluck('tipe_akun');

        return view('master-coa.index', compact('coas', 'tipeAkuns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipeAkuns = \App\Models\TipeAkun::orderBy('tipe_akun')->get();
        $kodeNomors = KodeNomor::orderBy('kode')->get();
        return view('master-coa.create', compact('tipeAkuns', 'kodeNomors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_akun' => 'required|string|max:20|unique:akun_coa,nomor_akun',
            'kode_nomor' => 'nullable|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string|max:50',
            'saldo' => 'nullable|numeric|min:0',
        ]);

        Coa::create([
            'nomor_akun' => $request->nomor_akun,
            'kode_nomor' => $request->kode_nomor,
            'nama_akun' => $request->nama_akun,
            'tipe_akun' => $request->tipe_akun,
            'saldo' => $request->saldo ?? 0,
        ]);

        return redirect()->route('master-coa-index')
            ->with('success', 'COA berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coa $coa)
    {
        return view('master-coa.show', compact('coa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coa $coa)
    {
        $tipeAkuns = \App\Models\TipeAkun::orderBy('tipe_akun')->get();
        $kodeNomors = KodeNomor::orderBy('kode')->get();
        return view('master-coa.edit', compact('coa', 'tipeAkuns', 'kodeNomors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coa $coa)
    {
        $request->validate([
            'nomor_akun' => ['required', 'string', 'max:20', Rule::unique('akun_coa')->ignore($coa->id)],
            'kode_nomor' => 'nullable|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string|max:50',
            'saldo' => 'nullable|numeric|min:0',
        ]);

        $coa->update([
            'nomor_akun' => $request->nomor_akun,
            'kode_nomor' => $request->kode_nomor,
            'nama_akun' => $request->nama_akun,
            'tipe_akun' => $request->tipe_akun,
            'saldo' => $request->saldo ?? 0,
        ]);

        return redirect()->route('master-coa-index')
            ->with('success', 'COA berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coa $coa)
    {
        $coa->delete();

        return redirect()->route('master-coa-index')
            ->with('success', 'COA berhasil dihapus.');
    }

    /**
     * Download template Excel untuk import COA
     */
    public function downloadTemplate()
    {
        $export = new MasterCoaTemplateExport();
        return $export->download();
    }

    /**
     * Import data COA dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048'
        ]);

        try {
            $import = new MasterCoaImport();
            $result = $import->import($request->file('file'));

            if ($result['success_count'] > 0) {
                $message = "Berhasil mengimport {$result['success_count']} data COA";
                if (!empty($result['errors'])) {
                    $message .= ". Namun ada " . count($result['errors']) . " error: " . implode('; ', $result['errors']);
                }
                return redirect()->route('master-coa-index')->with('success', $message);
            } else {
                return redirect()->route('master-coa-index')->with('error', 'Gagal mengimport data: ' . implode('; ', $result['errors']));
            }
        } catch (\Exception $e) {
            return redirect()->route('master-coa-index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan buku besar (ledger) untuk akun COA tertentu
     */
    public function ledger(Request $request, Coa $coa)
    {
        $query = $coa->transactions()->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc');

        // Filter by date range
        if ($request->has('dari_tanggal') && !empty($request->dari_tanggal)) {
            $query->where('tanggal_transaksi', '>=', $request->dari_tanggal);
        }

        if ($request->has('sampai_tanggal') && !empty($request->sampai_tanggal)) {
            $query->where('tanggal_transaksi', '<=', $request->sampai_tanggal);
        }

        $transactions = $query->paginate(50);

        // Calculate saldo awal (sebelum filter tanggal)
        $saldoAwal = 0;
        if ($request->has('dari_tanggal') && !empty($request->dari_tanggal)) {
            $transaksiSebelumnya = $coa->transactions()
                ->where('tanggal_transaksi', '<', $request->dari_tanggal)
                ->orderBy('tanggal_transaksi', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $saldoAwal = $transaksiSebelumnya ? $transaksiSebelumnya->saldo : 0;
        }

        // Calculate totals for filtered period
        $totalDebit = $transactions->sum('debit');
        $totalKredit = $transactions->sum('kredit');

        return view('master-coa.ledger', compact('coa', 'transactions', 'saldoAwal', 'totalDebit', 'totalKredit'));
    }

    /**
     * Print buku besar (ledger) untuk akun COA tertentu
     */
    public function ledgerPrint(Request $request, Coa $coa)
    {
        $query = $coa->transactions()->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc');

        // Filter by date range
        if ($request->has('dari_tanggal') && !empty($request->dari_tanggal)) {
            $query->where('tanggal_transaksi', '>=', $request->dari_tanggal);
        }

        if ($request->has('sampai_tanggal') && !empty($request->sampai_tanggal)) {
            $query->where('tanggal_transaksi', '<=', $request->sampai_tanggal);
        }

        // Get all transactions for print (no pagination)
        $transactions = $query->get();

        // Calculate saldo awal (sebelum filter tanggal)
        $saldoAwal = 0;
        if ($request->has('dari_tanggal') && !empty($request->dari_tanggal)) {
            $transaksiSebelumnya = $coa->transactions()
                ->where('tanggal_transaksi', '<', $request->dari_tanggal)
                ->orderBy('tanggal_transaksi', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $saldoAwal = $transaksiSebelumnya ? $transaksiSebelumnya->saldo : 0;
        }

        // Calculate totals for filtered period
        $totalDebit = $transactions->sum('debit');
        $totalKredit = $transactions->sum('kredit');

        return view('master-coa.ledger-print', compact('coa', 'transactions', 'saldoAwal', 'totalDebit', 'totalKredit'));
    }
}
