<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PembayaranAktivitasLain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranAktivitasLainController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranAktivitasLain::with(['creator', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
                  ->orWhere('jenis_aktivitas', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pembayaran-aktivitas-lain.index', compact('pembayarans'));
    }

    public function create()
    {
        $nomor = PembayaranAktivitasLain::generateNomor();
        $akunBiaya = DB::table('akun_coa')
            ->where(function($q) {
                $q->where('tipe_akun', 'like', '%biaya%')
                  ->orWhere('tipe_akun', 'like', '%beban%');
            })
            ->orderBy('kode_nomor')
            ->get();
        return view('pembayaran-aktivitas-lain.create', compact('nomor', 'akunBiaya'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis_aktivitas' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'jumlah' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|in:cash,transfer,cek,giro',
            'debit_kredit' => 'required|in:debit,kredit',
            'akun_coa_id' => 'required|exists:akun_coa,id',
        ]); 'debit_kredit' => 'required|in:debit,kredit',
            'akun_coa_id' => 'required|exists:akun_coa,id',
        ]);

        try {
            DB::beginTransaction();

            $validated['nomor'] = PembayaranAktivitasLain::generateNomor();
            $validated['created_by'] = Auth::id();
            $validated['status'] = 'pending';

            PembayaranAktivitasLain::create($validated);

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lain.index')
                ->with('success', 'Data pembayaran berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        $pembayaranAktivitasLain->load(['creator', 'approver']);
        return view('pembayaran-aktivitas-lain.show', compact('pembayaranAktivitasLain'));
    }

    public function edit(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        if ($pembayaranAktivitasLain->status !== 'pending') {
            return redirect()->route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain)
                ->with('error', 'Hanya pembayaran dengan status pending yang dapat diedit.');
        }

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
        if ($pembayaranAktivitasLain->status !== 'pending') {
            return redirect()->route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain)
                ->with('error', 'Hanya data dengan status pending yang dapat diedit');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis_aktivitas' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'jumlah' => 'required|numeric|min:0',
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
        if ($pembayaranAktivitasLain->status === 'paid') {
            return back()->with('error', 'Data yang sudah dibayar tidak dapat dihapus');
        }

        try {
            $pembayaranAktivitasLain->delete();
            return redirect()->route('pembayaran-aktivitas-lain.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function approve(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        if ($pembayaranAktivitasLain->status !== 'pending') {
            return back()->with('error', 'Data sudah diproses');
        }

        try {
            $pembayaranAktivitasLain->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return back()->with('success', 'Pembayaran berhasil disetujui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui pembayaran: ' . $e->getMessage());
        }
    }

    public function markAsPaid(PembayaranAktivitasLain $pembayaranAktivitasLain)
    {
        if ($pembayaranAktivitasLain->status !== 'approved') {
            return back()->with('error', 'Hanya data yang sudah approved yang dapat ditandai sebagai paid');
        }

        try {
            $pembayaranAktivitasLain->update(['status' => 'paid']);
            return back()->with('success', 'Status berhasil diubah menjadi paid');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}
