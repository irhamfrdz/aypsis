<?php

namespace App\Http\Controllers;

use App\Models\PembayaranAktivitasLainnya;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PembayaranAktivitasLainnyaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PembayaranAktivitasLainnya::with(['creator', 'approver', 'akunBank', 'akunBiaya']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('tanggal_pembayaran', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->where('tanggal_pembayaran', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->where('tanggal_pembayaran', '<=', $request->date_to);
        }

        // Filter berdasarkan nomor voyage
        if ($request->filled('nomor_voyage')) {
            $query->where('nomor_voyage', 'like', '%' . $request->nomor_voyage . '%');
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_pembayaran', 'like', '%' . $request->search . '%')
                  ->orWhere('aktivitas_pembayaran', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_voyage', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_kapal', 'like', '%' . $request->search . '%');
            });
        }

        $pembayaran = $query->latest()->paginate(20);

        return view('pembayaran-aktivitas-lainnya.index', compact('pembayaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Fetch bank/kas accounts from master COA
        $bankAccounts = Coa::where('tipe_akun', 'Kas/Bank')
            ->orWhere('tipe_akun', 'Bank/Kas')
            ->orderBy('nomor_akun')
            ->get();

        // Fetch COA biaya
        $coaBiaya = Coa::where('tipe_akun', 'Biaya')
            ->orWhere('tipe_akun', 'aktiva lainnya')
            ->orderBy('nomor_akun')
            ->get();

        // Fetch master supir (karyawan) untuk dropdown nama supir
        $masterSupir = \App\Models\Karyawan::whereNotNull('nama_lengkap')
            ->where('nama_lengkap', '!=', '')
            ->where(function($query) {
                $query->where('divisi', 'LIKE', '%supir%')
                      ->orWhere('divisi', 'LIKE', '%Supir%')
                      ->orWhere('divisi', 'LIKE', '%SUPIR%')
                      ->orWhere('pekerjaan', 'LIKE', '%supir%')
                      ->orWhere('pekerjaan', 'LIKE', '%Supir%')
                      ->orWhere('pekerjaan', 'LIKE', '%SUPIR%');
            })
            ->orderBy('nama_lengkap')
            ->get();

        // Fetch voyage list dari tabel Naik Kapal dan BLS
        $voyageFromNaikKapal = DB::table('naik_kapal')
            ->select('no_voyage as voyage', 'nama_kapal')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->groupBy('no_voyage', 'nama_kapal')
            ->get();

        $voyageFromBls = DB::table('bls')
            ->select('no_voyage as voyage', 'nama_kapal')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->groupBy('no_voyage', 'nama_kapal')
            ->get();

        // Gabungkan dan deduplikasi berdasarkan voyage
        $voyageList = $voyageFromNaikKapal->merge($voyageFromBls)
            ->unique('voyage')
            ->sortBy('voyage')
            ->values();

        return view('pembayaran-aktivitas-lainnya.create', compact('bankAccounts', 'coaBiaya', 'voyageList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'nomor_accurate' => 'nullable|string|max:50',
            'pilih_bank' => 'required|exists:akun_coa,id',
            'akun_biaya_id' => 'required|exists:akun_coa,id',
            'jenis_transaksi' => 'required|string|in:debit,kredit',
            'aktivitas_pembayaran' => 'required|string|min:5|max:1000',
            'total_pembayaran' => 'required|numeric|min:0',
        ], [
            'aktivitas_pembayaran.required' => 'Aktivitas pembayaran wajib diisi.',
            'aktivitas_pembayaran.min' => 'Aktivitas pembayaran minimal 5 karakter.',
            'aktivitas_pembayaran.max' => 'Aktivitas pembayaran maksimal 1000 karakter.',
            'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi.',
            'pilih_bank.required' => 'Pilihan bank wajib dipilih.',
            'akun_biaya_id.required' => 'Akun biaya wajib dipilih.',
            'total_pembayaran.required' => 'Total pembayaran wajib diisi.',
            'total_pembayaran.min' => 'Total pembayaran harus lebih dari 0.'
        ]);

        try {
            // Generate nomor pembayaran
            $nomorPembayaran = PembayaranAktivitasLainnya::generateNomor();

            // Clean total pembayaran
            $totalPembayaran = is_numeric($request->total_pembayaran)
                ? $request->total_pembayaran
                : (float) str_replace(['.', ','], ['', '.'], $request->total_pembayaran);

            // Start database transaction
            DB::beginTransaction();

            // Simpan pembayaran
            $pembayaran = PembayaranAktivitasLainnya::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'nomor_voyage' => $request->nomor_voyage,
                'nama_kapal' => $request->nama_kapal,
                'total_pembayaran' => $totalPembayaran,
                'aktivitas_pembayaran' => $request->aktivitas_pembayaran,
                'plat_nomor' => $request->plat_nomor,
                'pilih_bank' => $request->pilih_bank,
                'akun_biaya_id' => $request->akun_biaya_id,
                'jenis_transaksi' => $request->jenis_transaksi,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Update saldo bank
            $bankCoa = Coa::find($request->pilih_bank);
            if ($bankCoa && $request->jenis_transaksi == 'kredit') {
                $bankCoa->decrement('saldo', $totalPembayaran);
            } elseif ($bankCoa && $request->jenis_transaksi == 'debit') {
                $bankCoa->increment('saldo', $totalPembayaran);
            }

            Log::info('Pembayaran aktivitas lainnya berhasil dibuat', [
                'nomor_pembayaran' => $nomorPembayaran,
                'amount' => $totalPembayaran,
                'jenis_transaksi' => $request->jenis_transaksi
            ]);

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lainnya.index')
                ->with('success', 'Pembayaran berhasil disimpan dengan nomor: ' . $pembayaran->nomor_pembayaran);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create pembayaran aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        $pembayaranAktivitasLainnya->load(['creator', 'approver', 'akunBank', 'akunBiaya', 'supirList.supir']);
        return view('pembayaran-aktivitas-lainnya.show', compact('pembayaranAktivitasLainnya'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        // Cek apakah bisa diedit (hanya status draft)
        if ($pembayaranAktivitasLainnya->status !== 'draft') {
            return redirect()->route('pembayaran-aktivitas-lainnya.show', $pembayaranAktivitasLainnya)
                ->with('error', 'Pembayaran yang sudah diproses tidak dapat diedit.');
        }

        // Get bank accounts for dropdown
        $bankAccounts = Coa::where('tipe_akun', 'Kas/Bank')
            ->orWhere('tipe_akun', 'Bank/Kas')
            ->orderBy('nomor_akun')
            ->get();

        // Fetch COA biaya
        $coaBiaya = Coa::where('tipe_akun', 'Biaya')
            ->orWhere('tipe_akun', 'aktiva lainnya')
            ->orderBy('nomor_akun')
            ->get();

        // Fetch voyage list
        $voyageFromNaikKapal = DB::table('naik_kapal')
            ->select('no_voyage as voyage', 'nama_kapal')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->groupBy('no_voyage', 'nama_kapal')
            ->get();

        $voyageFromBls = DB::table('bls')
            ->select('no_voyage as voyage', 'nama_kapal')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->groupBy('no_voyage', 'nama_kapal')
            ->get();

        $voyageList = $voyageFromNaikKapal->merge($voyageFromBls)
            ->unique('voyage')
            ->sortBy('voyage')
            ->values();

        return view('pembayaran-aktivitas-lainnya.edit', compact(
            'pembayaranAktivitasLainnya',
            'bankAccounts',
            'coaBiaya',
            'voyageList'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        // Cek apakah bisa diupdate
        if ($pembayaranAktivitasLainnya->status !== 'draft') {
            return redirect()->route('pembayaran-aktivitas-lainnya.show', $pembayaranAktivitasLainnya)
                ->with('error', 'Pembayaran yang sudah diproses tidak dapat diupdate.');
        }

        $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'nomor_accurate' => 'nullable|string|max:50',
            'pilih_bank' => 'required|exists:akun_coa,id',
            'akun_biaya_id' => 'required|exists:akun_coa,id',
            'jenis_transaksi' => 'required|string|in:debit,kredit',
            'aktivitas_pembayaran' => 'required|string|min:5|max:1000',
            'total_pembayaran' => 'required|numeric|min:0',
        ]);

        try {
            // Get old and new bank info
            $oldBankCoa = Coa::find($pembayaranAktivitasLainnya->pilih_bank);
            $newBankCoa = Coa::find($request->pilih_bank);

            // Clean total pembayaran
            $totalPembayaran = is_numeric($request->total_pembayaran)
                ? $request->total_pembayaran
                : (float) str_replace(['.', ','], ['', '.'], $request->total_pembayaran);

            $oldTotalPembayaran = (float) $pembayaranAktivitasLainnya->total_pembayaran;

            // Start database transaction
            DB::beginTransaction();

            // Reverse old bank transaction
            if ($oldBankCoa && $pembayaranAktivitasLainnya->jenis_transaksi == 'kredit') {
                $oldBankCoa->increment('saldo', $oldTotalPembayaran);
            } elseif ($oldBankCoa && $pembayaranAktivitasLainnya->jenis_transaksi == 'debit') {
                $oldBankCoa->decrement('saldo', $oldTotalPembayaran);
            }

            // Apply new bank transaction
            if ($newBankCoa && $request->jenis_transaksi == 'kredit') {
                $newBankCoa->decrement('saldo', $totalPembayaran);
            } elseif ($newBankCoa && $request->jenis_transaksi == 'debit') {
                $newBankCoa->increment('saldo', $totalPembayaran);
            }

            // Update pembayaran record
            $pembayaranAktivitasLainnya->update([
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'nomor_voyage' => $request->nomor_voyage,
                'nama_kapal' => $request->nama_kapal,
                'total_pembayaran' => $totalPembayaran,
                'aktivitas_pembayaran' => $request->aktivitas_pembayaran,
                'plat_nomor' => $request->plat_nomor,
                'pilih_bank' => $request->pilih_bank,
                'akun_biaya_id' => $request->akun_biaya_id,
                'jenis_transaksi' => $request->jenis_transaksi,
            ]);

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lainnya.index')
                ->with('success', 'Pembayaran berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update pembayaran aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal mengupdate pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        // Cek apakah bisa dihapus
        if ($pembayaranAktivitasLainnya->status !== 'draft') {
            return redirect()->route('pembayaran-aktivitas-lainnya.show', $pembayaranAktivitasLainnya)
                ->with('error', 'Pembayaran yang sudah diproses tidak dapat dihapus.');
        }

        try {
            // Get bank info for reversal
            $bankCoa = Coa::find($pembayaranAktivitasLainnya->pilih_bank);
            $totalPembayaran = (float) $pembayaranAktivitasLainnya->total_pembayaran;

            // Start database transaction
            DB::beginTransaction();

            // Reverse bank transaction
            if ($bankCoa && $pembayaranAktivitasLainnya->jenis_transaksi == 'kredit') {
                $bankCoa->increment('saldo', $totalPembayaran);
            } elseif ($bankCoa && $pembayaranAktivitasLainnya->jenis_transaksi == 'debit') {
                $bankCoa->decrement('saldo', $totalPembayaran);
            }

            // Delete the payment record
            $pembayaranAktivitasLainnya->delete();

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lainnya.index')
                ->with('success', 'Pembayaran berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete pembayaran aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal menghapus pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Approve pembayaran
     */
    public function approve(Request $request, PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        if ($pembayaranAktivitasLainnya->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pembayaran dengan status pending yang dapat diapprove.');
        }

        try {
            DB::beginTransaction();

            $pembayaranAktivitasLainnya->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil diapprove.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal approve pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(Request $request, PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        if ($pembayaranAktivitasLainnya->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya pembayaran yang sudah diapprove yang dapat ditandai sebagai paid.');
        }

        try {
            DB::beginTransaction();

            $pembayaranAktivitasLainnya->update([
                'status' => 'paid',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil ditandai sebagai paid.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menandai sebagai paid: ' . $e->getMessage());
        }
    }
}