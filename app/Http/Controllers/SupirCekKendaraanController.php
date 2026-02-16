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

        $mobils = Mobil::orderBy('nomor_polisi')->get();
        
        // Find default mobil for this supir
        $defaultMobilId = null;
        if ($user->karyawan) {
            // Priority 1: Check mobils table for karyawan_id link
            $assignedMobil = Mobil::where('karyawan_id', $user->karyawan_id)->first();
            if ($assignedMobil) {
                $defaultMobilId = $assignedMobil->id;
            } else if ($user->karyawan->plat) {
                // Priority 2: Check plat column in karyawans table matching nomor_polisi
                $matchedMobil = Mobil::where('nomor_polisi', $user->karyawan->plat)->first();
                if ($matchedMobil) {
                    $defaultMobilId = $matchedMobil->id;
                }
            }
        }

        return view('supir.cek-kendaraan.create', compact('mobils', 'defaultMobilId'));
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
            'masa_berlaku_stnk' => 'nullable|date',
            'masa_berlaku_kir' => 'nullable|date',
            'kotak_p3k' => 'required|string',
            'plat_no_belakang' => 'required|string',
            'lampu_besar_dekat_kanan' => 'required|string',
            'lampu_besar_dekat_kiri' => 'required|string',
            'lampu_rem_kanan' => 'required|string',
            'lampu_rem_kiri' => 'required|string',
            'lampu_alarm_mundur' => 'required|string',
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
