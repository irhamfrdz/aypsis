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
        $query = PembayaranPranotaPerbaikanKontainer::with(['pranotaPerbaikanKontainers.perbaikanKontainers.kontainer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_kas', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_kas', '<=', $request->tanggal_sampai);
        }

        // Search by kontainer number or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('pranotaPerbaikanKontainers.perbaikanKontainers.kontainer', function($kontainer) use ($search) {
                    $kontainer->where('nomor_kontainer', 'like', "%{$search}%");
                })
                ->orWhere('nomor_pembayaran', 'like', "%{$search}%")
                ->orWhere('alasan_penyesuaian', 'like', "%{$search}%");
            });
        }

        $pembayaranPranotaPerbaikanKontainers = $query->orderBy('tanggal_kas', 'desc')
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

        // Generate unique nomor pembayaran if not provided or if it's a default value
        if (!$request->filled('nomor_pembayaran') || str_contains($request->nomor_pembayaran, '-000001')) {
            $request->merge(['nomor_pembayaran' => $this->generateUniqueNomorPembayaran($request->bank)]);
        }

        $request->validate([
            'pranota_perbaikan_kontainer_ids' => 'required|array|min:1',
            'pranota_perbaikan_kontainer_ids.*' => 'exists:pranota_perbaikan_kontainers,id',
            'tanggal_kas' => 'required|date',
            'nomor_pembayaran' => 'required|string|unique:pembayaran_pranota_perbaikan_kontainers,nomor_pembayaran',
            'nomor_cetakan' => 'nullable|integer|min:1|max:9',
            'bank' => 'required|string',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
            'penyesuaian' => 'nullable|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
        ]);

        try {
            $pranotaIds = $request->pranota_perbaikan_kontainer_ids;
            $totalPembayaran = 0;
            $pembayaranData = [];

            // Hitung total pembayaran dari semua pranota yang dipilih
            foreach ($pranotaIds as $pranotaId) {
                $pranota = PranotaPerbaikanKontainer::findOrFail($pranotaId);
                $totalPembayaran += $pranota->total_biaya ?? 0;
                $pembayaranData[$pranotaId] = $pranota->total_biaya ?? 0;
            }

            // Hitung total setelah penyesuaian
            $penyesuaian = $request->penyesuaian ?? 0;
            $totalSetelahPenyesuaian = $totalPembayaran + $penyesuaian;

            // Buat satu record pembayaran
            $pembayaran = PembayaranPranotaPerbaikanKontainer::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'nomor_cetakan' => $request->nomor_cetakan,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalSetelahPenyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'status' => 'approved',
            ]);

            // Associate pranota dengan pembayaran melalui pivot table
            foreach ($pembayaranData as $pranotaId => $amount) {
                $pembayaran->pranotaPerbaikanKontainers()->attach($pranotaId, [
                    'amount' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update status pranota menjadi sudah dibayar
                $pranota = PranotaPerbaikanKontainer::findOrFail($pranotaId);
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
        $pembayaran->load(['pranotaPerbaikanKontainers.perbaikanKontainers.kontainer']);

        return view('pembayaran-pranota-perbaikan-kontainer.show', compact('pembayaran'));
    }

    /**
     * Display the specified resource for printing.
     */
    public function print(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        $pembayaran->load(['pranotaPerbaikanKontainers.perbaikanKontainers']);

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
            'pranota_perbaikan_kontainer_ids' => 'required|array|min:1',
            'pranota_perbaikan_kontainer_ids.*' => 'exists:pranota_perbaikan_kontainers,id',
            'tanggal_kas' => 'required|date',
            'nomor_pembayaran' => 'required|string|unique:pembayaran_pranota_perbaikan_kontainers,nomor_pembayaran,' . $pembayaran->id,
            'nomor_cetakan' => 'nullable|integer|min:1|max:9',
            'bank' => 'required|string',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
            'penyesuaian' => 'nullable|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        try {
            $pranotaIds = $request->pranota_perbaikan_kontainer_ids;
            $totalPembayaran = 0;
            $pembayaranData = [];

            // Hitung total pembayaran dari semua pranota yang dipilih
            foreach ($pranotaIds as $pranotaId) {
                $pranota = PranotaPerbaikanKontainer::findOrFail($pranotaId);
                $totalPembayaran += $pranota->total_biaya ?? 0;
                $pembayaranData[$pranotaId] = $pranota->total_biaya ?? 0;
            }

            // Hitung total setelah penyesuaian
            $penyesuaian = $request->penyesuaian ?? 0;
            $totalSetelahPenyesuaian = $totalPembayaran + $penyesuaian;

            // Update pembayaran
            $pembayaran->update([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'nomor_cetakan' => $request->nomor_cetakan,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalSetelahPenyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'status' => $request->status,
            ]);

            // Sync pranota associations
            $currentPranotaIds = $pembayaran->pranotaPerbaikanKontainers->pluck('id')->toArray();
            $newPranotaIds = $pranotaIds;

            // Detach pranota yang tidak lagi dipilih
            $pranotaToDetach = array_diff($currentPranotaIds, $newPranotaIds);
            foreach ($pranotaToDetach as $pranotaId) {
                $pranota = PranotaPerbaikanKontainer::findOrFail($pranotaId);
                $pranota->update(['status' => 'belum_dibayar']);
                foreach ($pranota->perbaikanKontainers as $perbaikan) {
                    $perbaikan->update(['status_perbaikan' => 'belum_masuk_pranota']);
                }
            }

            // Sync pivot table
            $syncData = [];
            foreach ($pembayaranData as $pranotaId => $amount) {
                $syncData[$pranotaId] = [
                    'amount' => $amount,
                    'updated_at' => now(),
                ];
            }
            $pembayaran->pranotaPerbaikanKontainers()->sync($syncData);

            // Update status pranota yang baru dipilih
            $pranotaToAttach = array_diff($newPranotaIds, $currentPranotaIds);
            foreach ($pranotaToAttach as $pranotaId) {
                $pranota = PranotaPerbaikanKontainer::findOrFail($pranotaId);
                $pranota->update(['status' => 'sudah_dibayar']);
                foreach ($pranota->perbaikanKontainers as $perbaikan) {
                    $perbaikan->update(['status_perbaikan' => 'completed']);
                }
            }

            return redirect()->route('pembayaran-pranota-perbaikan-kontainer.index')
                ->with('success', 'Pembayaran pranota perbaikan kontainer berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique nomor pembayaran
     */
    private function generateUniqueNomorPembayaran($bankName)
    {
        // Get kode from CoA based on bank name
        $coa = \App\Models\Coa::where('nama_akun', $bankName)->first();
        $kode = $coa ? ($coa->kode_nomor ?: 'PPK') : 'PPK';

        $tahun = now()->format('y');
        $bulan = now()->format('m');
        $baseNomor = $kode . $tahun . $bulan;

        // Find the next available number
        $counter = 1;
        do {
            $nomor = $baseNomor . '-' . str_pad($counter, 6, '0', STR_PAD_LEFT);
            $exists = PembayaranPranotaPerbaikanKontainer::where('nomor_pembayaran', $nomor)->exists();
            $counter++;
        } while ($exists && $counter <= 999999);

        return $nomor;
    }
    public function destroy(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        try {
            // Reset status semua pranota yang terkait
            foreach ($pembayaran->pranotaPerbaikanKontainers as $pranota) {
                $pranota->update(['status' => 'belum_dibayar']);

                // Reset status perbaikan kontainer yang terkait
                foreach ($pranota->perbaikanKontainers as $perbaikan) {
                    $perbaikan->update(['status_perbaikan' => 'belum_masuk_pranota']);
                }
            }

            // Detach semua pranota dari pivot table
            $pembayaran->pranotaPerbaikanKontainers()->detach();

            $pembayaran->delete();

            return redirect()->route('pembayaran-pranota-perbaikan-kontainer.index')
                ->with('success', 'Pembayaran pranota perbaikan kontainer berhasil dihapus dan status dikembalikan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus pembayaran: ' . $e->getMessage());
        }
    }
}
