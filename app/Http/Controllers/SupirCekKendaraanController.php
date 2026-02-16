<?php

namespace App\Http\Controllers;

use App\Models\CekKendaraan;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupirCekKendaraanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        $karyawanId = $user->karyawan_id;
        
        $history = CekKendaraan::with('mobil')
            ->where('karyawan_id', $karyawanId)
            ->latest()
            ->paginate(10);

        return view('supir.cek-kendaraan.index', compact('history'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        // Get all active mobils. In a real scenario, you might want to filter mobils assigned to the supir.
        $mobils = Mobil::orderBy('nomor_polisi')->get();

        return view('supir.cek-kendaraan.create', compact('mobils'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        $request->validate([
            'mobil_id' => 'required|exists:mobils,id',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'odometer' => 'nullable|integer',
            'bahan_bakar' => 'required|integer|min:0|max:100',
            'foto_sebelum' => 'nullable|image|max:2048',
            'foto_sesudah' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['karyawan_id'] = $user->karyawan_id;

            // Handle file uploads if any
            if ($request->hasFile('foto_sebelum')) {
                $data['foto_sebelum'] = $request->file('foto_sebelum')->store('cek-kendaraan', 'public');
            }
            if ($request->hasFile('foto_sesudah')) {
                $data['foto_sesudah'] = $request->file('foto_sesudah')->store('cek-kendaraan', 'public');
            }

            CekKendaraan::create($data);

            DB::commit();

            return redirect()->route('supir.cek-kendaraan.index')->with('success', 'Berhasil menyimpan pengecekan kendaraan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function show(CekKendaraan $cekKendaraan)
    {
        $user = Auth::user();
        if (!$user->isSupir() && $user->role !== 'admin') {
            abort(403);
        }

        $cekKendaraan->load(['mobil', 'karyawan']);
        return view('supir.cek-kendaraan.show', compact('cekKendaraan'));
    }
}
