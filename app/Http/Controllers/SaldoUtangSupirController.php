<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\RiwayatUtangSupir;
use App\Models\SaldoUtangSupir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaldoUtangSupirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Karyawan::query()
            ->with(['saldoUtang'])
            ->where(function ($q) {
                $q->where('pekerjaan', 'like', '%supir%')
                    ->orWhere('pekerjaan', 'like', '%sopir%')
                    ->orWhere('pekerjaan', 'like', '%driver%')
                    ->orWhere('divisi', 'like', '%supir%')
                    ->orWhere('divisi', 'like', '%sopir%')
                    ->orWhere('divisi', 'like', '%driver%')
                    ->orWhereHas('saldoUtang');
            });

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nama_panggilan', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $supirs = $query->orderBy('nama_lengkap', 'asc')->paginate(20);

        return view('saldo-utang-supir.index', compact('supirs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supirs = Karyawan::where(function ($q) {
            $q->where('pekerjaan', 'like', '%supir%')
                ->orWhere('pekerjaan', 'like', '%sopir%')
                ->orWhere('pekerjaan', 'like', '%driver%')
                ->orWhere('divisi', 'like', '%supir%')
                ->orWhere('divisi', 'like', '%sopir%')
                ->orWhere('divisi', 'like', '%driver%')
                ->orWhereHas('saldoUtang');
        })->orderBy('nama_lengkap', 'asc')->get();

        return view('saldo-utang-supir.create', compact('supirs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'tipe' => 'required|in:penambahan,pengurangan',
            'nominal' => 'required|numeric|min:0.01',
            'referensi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $saldoUtang = SaldoUtangSupir::firstOrCreate(
                ['karyawan_id' => $request->karyawan_id],
                ['saldo' => 0.00]
            );

            if ($request->tipe === 'penambahan') {
                $saldoUtang->increment('saldo', floatval($request->nominal));
            } else {
                $saldoUtang->decrement('saldo', floatval($request->nominal));
            }

            RiwayatUtangSupir::create([
                'karyawan_id' => $request->karyawan_id,
                'tanggal' => $request->tanggal,
                'tipe' => $request->tipe,
                'nominal' => floatval($request->nominal),
                'referensi' => $request->referensi,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();
            return redirect()->route('saldo-utang-supir.index')
                ->with('success', 'Transaksi utang supir berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $supir = Karyawan::with(['saldoUtang'])->findOrFail($id);
        
        $riwayat = RiwayatUtangSupir::where('karyawan_id', $id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('saldo-utang-supir.show', compact('supir', 'riwayat'));
    }
}
