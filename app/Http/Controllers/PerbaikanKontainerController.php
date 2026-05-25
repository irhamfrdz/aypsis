<?php

namespace App\Http\Controllers;

use App\Models\PerbaikanKontainer;
use App\Models\PranotaPerbaikanKontainer;
use App\Models\VendorBengkel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PerbaikanKontainer::with('bengkel');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('no_perbaikan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('vendor_bengkel_id')) {
            $query->where('vendor_bengkel_id', $request->input('vendor_bengkel_id'));
        }

        if ($request->filled('tanggal_masuk_start')) {
            $query->whereDate('tanggal_masuk', '>=', $request->input('tanggal_masuk_start'));
        }

        if ($request->filled('tanggal_masuk_end')) {
            $query->whereDate('tanggal_masuk', '<=', $request->input('tanggal_masuk_end'));
        }

        $statusPranota = $request->input('status_pranota', 'Belum');
        if ($statusPranota && $statusPranota !== 'all') {
            $query->where('status_pranota', $statusPranota);
        }

        $perbaikanKontainers = $query->orderBy('created_at', 'desc')->paginate(15);
        $bengkels = VendorBengkel::all();

        return view('perbaikan-kontainer.index', compact('perbaikanKontainers', 'bengkels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bengkels = VendorBengkel::all();

        return view('perbaikan-kontainer.create', compact('bengkels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_kontainer' => 'required|string|max:50',
            'ukuran' => 'nullable|string|max:10',
            'tipe_kontainer' => 'nullable|string|max:50',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required_if:status,selesai|nullable|date|after_or_equal:tanggal_masuk',
            'vendor_bengkel_id' => 'required|exists:vendor_bengkel,id',
            'keterangan_kerusakan' => 'required|string',
            'keterangan_perbaikan' => 'required_if:status,selesai|nullable|string',
            'estimasi_biaya' => 'required|numeric|min:0',
            'biaya_riil' => 'required_if:status,selesai|nullable|numeric|min:0',
            'status' => 'required|in:pending,proses,selesai,batal',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // If status is selesai, default real cost to estimate if not provided, and date out to today
        if ($data['status'] === 'selesai') {
            $data['tanggal_keluar'] = $data['tanggal_keluar'] ?? now()->format('Y-m-d');
            $data['biaya_riil'] = $data['biaya_riil'] ?? $data['estimasi_biaya'];
        }

        PerbaikanKontainer::create($data);

        return redirect()->route('perbaikan-kontainer.index')
            ->with('success', 'Data perbaikan kontainer berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $perbaikanKontainer = PerbaikanKontainer::with(['bengkel', 'creator', 'updater'])->findOrFail($id);

        return view('perbaikan-kontainer.show', compact('perbaikanKontainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $perbaikanKontainer = PerbaikanKontainer::findOrFail($id);
        $bengkels = VendorBengkel::all();

        return view('perbaikan-kontainer.edit', compact('perbaikanKontainer', 'bengkels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $perbaikan = PerbaikanKontainer::findOrFail($id);

        $request->validate([
            'no_kontainer' => 'required|string|max:50',
            'ukuran' => 'nullable|string|max:10',
            'tipe_kontainer' => 'nullable|string|max:50',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required_if:status,selesai|nullable|date|after_or_equal:tanggal_masuk',
            'vendor_bengkel_id' => 'required|exists:vendor_bengkel,id',
            'keterangan_kerusakan' => 'required|string',
            'keterangan_perbaikan' => 'required_if:status,selesai|nullable|string',
            'estimasi_biaya' => 'required|numeric|min:0',
            'biaya_riil' => 'required_if:status,selesai|nullable|numeric|min:0',
            'status' => 'required|in:pending,proses,selesai,batal',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        // Handle auto-completion of selesai status
        if ($data['status'] === 'selesai') {
            $data['tanggal_keluar'] = $data['tanggal_keluar'] ?? now()->format('Y-m-d');
            $data['biaya_riil'] = $data['biaya_riil'] ?? $perbaikan->estimasi_biaya;
        }

        $perbaikan->update($data);

        return redirect()->route('perbaikan-kontainer.index')
            ->with('success', 'Data perbaikan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $perbaikan = PerbaikanKontainer::findOrFail($id);
        $perbaikan->delete();

        return redirect()->route('perbaikan-kontainer.index')
            ->with('success', 'Data perbaikan kontainer berhasil dihapus.');
    }

    public function generateNomorPranota()
    {
        try {
            $kode = 'PTP';
            $bulan = now()->format('m');
            $tahun = now()->format('y');
            $prefix = "{$kode}-{$bulan}-{$tahun}-";
            $lastPranota = PranotaPerbaikanKontainer::where('nomor_pranota', 'like', $prefix.'%')
                ->orderBy('nomor_pranota', 'desc')
                ->first();

            if ($lastPranota) {
                $lastNumber = (int) substr($lastPranota->nomor_pranota, -6);
                $runningNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $runningNumber = '000001';
            }

            $nomorPranota = "{$prefix}{$runningNumber}";

            return response()->json([
                'success' => true,
                'nomor_pranota' => $nomorPranota,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor pranota: '.$e->getMessage(),
            ], 500);
        }
    }

    public function updateBiayaRiil(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|exists:perbaikan_kontainers,id',
                'biaya_riil' => 'required|numeric|min:0',
            ]);

            $perbaikan = PerbaikanKontainer::findOrFail($data['id']);
            $perbaikan->update([
                'biaya_riil' => $data['biaya_riil'],
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biaya riil berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui biaya riil: '.$e->getMessage(),
            ], 500);
        }
    }

    public function masukPranota(Request $request)
    {
        try {
            $data = $request->validate([
                'nomor_pranota' => 'required|string|unique:pranota_perbaikan_kontainers,nomor_pranota',
                'tanggal_pranota' => 'required|date',
                'vendor' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'adjustment' => 'nullable|numeric',
                'items' => 'required|array',
            ]);

            $totalBiaya = 0;
            foreach ($data['items'] as $item) {
                $biayaRiil = floatval($item['biaya_riil'] ?? 0);
                $estimasi = floatval($item['estimasi_biaya'] ?? 0);
                $totalBiaya += ($biayaRiil > 0) ? $biayaRiil : $estimasi;
            }

            $pranota = PranotaPerbaikanKontainer::create([
                'nomor_pranota' => $data['nomor_pranota'],
                'tanggal_pranota' => $data['tanggal_pranota'],
                'vendor' => $data['vendor'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'adjustment' => $data['adjustment'] ?? 0,
                'total_biaya' => $totalBiaya,
                'items' => $data['items'],
                'status' => 'approved',
                'created_by' => Auth::id(),
            ]);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (isset($item['id'])) {
                        PerbaikanKontainer::where('id', $item['id'])->update(['status_pranota' => 'Sudah']);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memasukkan ke pranota',
                'redirect' => route('perbaikan-kontainer.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memasukkan ke pranota: '.$e->getMessage(),
            ], 500);
        }
    }
}
