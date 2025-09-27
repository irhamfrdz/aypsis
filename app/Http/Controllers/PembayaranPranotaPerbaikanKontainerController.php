<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaPerbaikanKontainer;
use App\Models\PranotaPerbaikanKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PembayaranPranotaPerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PembayaranPranotaPerbaikanKontainer::with(['pranotaPerbaikanKontainer.perbaikanKontainers.kontainer', 'creator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_pembayaran', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_pembayaran', '<=', $request->tanggal_sampai);
        }

        // Search by kontainer number or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('pranotaPerbaikanKontainer.perbaikanKontainers.kontainer', function($kontainer) use ($search) {
                    $kontainer->where('nomor_kontainer', 'like', "%{$search}%");
                })
                ->orWhere('nomor_invoice', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pembayaranPranotaPerbaikanKontainers = $query->orderBy('tanggal_pembayaran', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('pembayaran-pranota-perbaikan-kontainer.index', compact('pembayaranPranotaPerbaikanKontainers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check permission manually to provide better error message
        if (!Gate::allows('pembayaran-pranota-perbaikan-kontainer-create')) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota perbaikan kontainer. Silakan hubungi administrator.');
        }

        $pranotaPerbaikanKontainers = PranotaPerbaikanKontainer::whereDoesntHave('pembayaranPranotaPerbaikanKontainers')
            ->where('status', 'belum_dibayar')
            ->with(['perbaikanKontainers.kontainer'])
            ->get();

        $akunCoa = \App\Models\Coa::all();

        return view('pembayaran-pranota-perbaikan-kontainer.create', compact('pranotaPerbaikanKontainers', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission manually to provide better error message
        if (!Gate::allows('pembayaran-pranota-perbaikan-kontainer-create')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota perbaikan kontainer. Silakan hubungi administrator.');
        }

        $request->validate([
            'pranota_perbaikan_kontainer_ids' => 'required|array|min:1',
            'pranota_perbaikan_kontainer_ids.*' => 'exists:pranota_perbaikan_kontainers,id',
            'tanggal_pembayaran' => 'required|date',
            'nomor_pembayaran' => 'required|string',
            'nomor_cetakan' => 'required|integer|min:1|max:9',
            'bank' => 'required|string',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
        ]);

        try {
            $pranotaIds = $request->pranota_perbaikan_kontainer_ids;
            $totalPembayaran = 0;
            $pembayaranRecords = [];

            // Hitung total pembayaran dari semua pranota yang dipilih
            foreach ($pranotaIds as $pranotaId) {
                $pranota = PranotaPerbaikanKontainer::findOrFail($pranotaId);
                $totalPembayaran += $pranota->total_biaya ?? 0;
            }

            // Buat record pembayaran untuk setiap pranota
            foreach ($pranotaIds as $pranotaId) {
                $pranota = PranotaPerbaikanKontainer::findOrFail($pranotaId);

                $pembayaranRecords[] = PembayaranPranotaPerbaikanKontainer::create([
                    'pranota_perbaikan_kontainer_id' => $pranotaId,
                    'tanggal_pembayaran' => $request->tanggal_pembayaran,
                    'nominal_pembayaran' => $pranota->total_biaya ?? 0,
                    'nomor_invoice' => $request->nomor_pembayaran,
                    'metode_pembayaran' => 'transfer', // Gunakan enum yang valid
                    'keterangan' => "Pembayaran pranota {$pranota->nomor_pranota} - {$request->jenis_transaksi} via {$request->bank}",
                    'status_pembayaran' => 'paid',
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                // Update status pranota menjadi sudah dibayar
                $pranota->update(['status' => 'sudah_dibayar']);

                // Update status perbaikan kontainer yang terkait
                foreach ($pranota->perbaikanKontainers as $perbaikan) {
                    $perbaikan->update(['status_perbaikan' => 'completed']);
                }
            }

            return redirect()->route('pembayaran-pranota-perbaikan-kontainer.index')
                ->with('success', "Pembayaran berhasil dibuat untuk " . count($pranotaIds) . " pranota dengan total Rp " . number_format($totalPembayaran, 0, ',', '.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        $pembayaran->load(['pranotaPerbaikanKontainer.perbaikanKontainers.kontainer', 'creator']);

        return view('pembayaran-pranota-perbaikan-kontainer.show', compact('pembayaran'));
    }

    /**
     * Display the specified resource for printing.
     */
    public function print(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        return view('pembayaran-pranota-perbaikan-kontainer.print', compact('pembayaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        $pranotaPerbaikanKontainers = PranotaPerbaikanKontainer::whereDoesntHave('pembayaranPranotaPerbaikanKontainers')
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->with(['perbaikanKontainers.kontainer'])
            ->get();

        return view('pembayaran-pranota-perbaikan-kontainer.edit', compact('pembayaran', 'pranotaPerbaikanKontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        $request->validate([
            'pranota_perbaikan_kontainer_id' => 'required|exists:pranota_perbaikan_kontainers,id',
            'tanggal_pembayaran' => 'required|date',
            'nominal_pembayaran' => 'required|numeric|min:0',
            'nomor_invoice' => 'nullable|string',
            'metode_pembayaran' => 'required|string',
            'keterangan' => 'nullable|string',
            'status_pembayaran' => 'required|in:pending,completed,cancelled',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $pembayaran->update($data);

        return redirect()->route('pembayaran-pranota-perbaikan-kontainer.index')
            ->with('success', 'Pembayaran pranota perbaikan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        try {
            // Reset status pranota menjadi belum_dibayar
            $pembayaran->pranotaPerbaikanKontainer->update(['status' => 'belum_dibayar']);

            // Reset status perbaikan kontainer yang terkait
            foreach ($pembayaran->pranotaPerbaikanKontainer->perbaikanKontainers as $perbaikan) {
                $perbaikan->update(['status_perbaikan' => 'belum_masuk_pranota']);
            }

            $pembayaran->delete();

            return redirect()->route('pembayaran-pranota-perbaikan-kontainer.index')
                ->with('success', 'Pembayaran pranota perbaikan kontainer berhasil dihapus dan status dikembalikan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus pembayaran: ' . $e->getMessage());
        }
    }
}
