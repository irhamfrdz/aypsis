<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PelamarKaryawan;

class PelamarKaryawanController extends Controller
{
    public function create()
    {
        return view('pelamar-karyawan.create');
    }

    public function index()
    {
        $pelamars = PelamarKaryawan::latest()->paginate(10);
        return view('pelamar-karyawan.index', compact('pelamars'));
    }

    public function show(PelamarKaryawan $pelamar)
    {
        return view('pelamar-karyawan.show', compact('pelamar'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'wearpack_size' => 'nullable|string|max:50',
            'no_safety_shoes' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:50',
            'no_nik' => 'required|string|max:50|unique:pelamar_karyawans,no_nik',
            'no_kartu_keluarga' => 'nullable|string|max:50',
            'no_bpjs_kesehatan' => 'nullable|string|max:50',
            'no_ketenagakerjaan' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'no_handphone' => 'required|string|max:20',
            'tanggungan_anak' => 'nullable|integer',
            'alamat_lengkap' => 'required|string',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kota_kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'kontak_darurat' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->store('recruitment/cvs', 'public');
            $validated['cv_path'] = $path;
        }

        PelamarKaryawan::create($validated);

        return redirect()->route('login')->with('success', 'Aplikasi lamaran Anda telah berhasil dikirim. Kami akan menghubungi Anda segera.');
    }
}
