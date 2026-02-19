<?php

namespace App\Http\Controllers;

use App\Models\CekKendaraan;
use Illuminate\Http\Request;

class CekKendaraanController extends Controller
{
    public function index()
    {
        $cekKendaraans = CekKendaraan::with(['mobil', 'karyawan'])
            ->latest()
            ->paginate(15);
            
        return view('cek-kendaraan.index', compact('cekKendaraans'));
    }

    public function show(CekKendaraan $cekKendaraan)
    {
        $cekKendaraan->load(['mobil', 'karyawan']);
        return view('cek-kendaraan.show', compact('cekKendaraan'));
    }
}
