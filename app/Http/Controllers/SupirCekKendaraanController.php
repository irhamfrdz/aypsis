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
            'nomor_sim' => 'nullable|string|max:255',
            'masa_berlaku_sim_start' => 'nullable|date',
            'masa_berlaku_sim_end' => 'nullable|date',
            'kotak_p3k' => 'required|string',
            'racun_api' => 'required|string',
            'plat_no_depan' => 'required|string',
            'plat_no_belakang' => 'required|string',
            'lampu_jauh_kanan' => 'required|string',
            'lampu_jauh_kiri' => 'required|string',
            'lampu_dekat_kanan' => 'required|string',
            'lampu_dekat_kiri' => 'required|string',
            'lampu_sein_depan_kanan' => 'required|string',
            'lampu_sein_depan_kiri' => 'required|string',
            'lampu_sein_belakang_kanan' => 'required|string',
            'lampu_sein_belakang_kiri' => 'required|string',
            'lampu_rem_kanan' => 'required|string',
            'lampu_rem_kiri' => 'required|string',
            'lampu_mundur_kanan' => 'required|string',
            'lampu_mundur_kiri' => 'required|string',
            'sabuk_pengaman_kanan' => 'required|string',
            'sabuk_pengaman_kiri' => 'required|string',
            'kamvas_rem_depan_kanan' => 'required|string',
            'kamvas_rem_depan_kiri' => 'required|string',
            'kamvas_rem_belakang_kanan' => 'required|string',
            'kamvas_rem_belakang_kiri' => 'required|string',
            'spion_kanan' => 'required|string',
            'spion_kiri' => 'required|string',
            'tekanan_ban_depan_kanan' => 'required|string',
            'tekanan_ban_depan_kiri' => 'required|string',
            'tekanan_ban_belakang_kanan' => 'required|string',
            'tekanan_ban_belakang_kiri' => 'required|string',
            'ganjelan_ban' => 'required|string',
            'trakel_sabuk' => 'required|string',
            'twist_lock_kontainer' => 'required|string',
            'landing_buntut' => 'required|string',
            'patok_besi' => 'required|string',
            'tutup_tangki' => 'required|string',
            'lampu_no_plat' => 'required|string',
            'lampu_bahaya' => 'required|string',
            'klakson' => 'required|string',
            'radio' => 'required|string',
            'rem_tangan' => 'required|string',
            'pedal_gas' => 'required|string',
            'pedal_rem' => 'required|string',
            'porseneling' => 'required|string',
            'antena_radio' => 'required|string',
            'speaker' => 'required|string',
            'spion_dalam' => 'required|string',
            'dongkrak' => 'required|string',
            'tangkai_dongkrak' => 'required|string',
            'kunci_roda' => 'required|string',
            'dop_roda' => 'required|string',
            'wiper_depan' => 'required|string',
            'oli_mesin' => 'required|string',
            'air_radiator' => 'required|string',
            'minyak_rem' => 'required|string',
            'air_wiper' => 'required|string',
            'kondisi_aki' => 'required|string',
            'pengukur_tekanan_ban' => 'required|string',
            'segitiga_pengaman' => 'required|string',
            'jumlah_ban_serep' => 'required|string',
            'pernyataan' => 'required|string|in:layak,tidak_layak',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['karyawan_id'] = $user->karyawan_id;

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
