<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobil;
use App\Models\OngkosTruck;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\TandaTerima;
use App\Models\TandaTerimaBongkaran;
use Illuminate\Support\Facades\Auth;

class OngkosTruckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all mobils for filter dropdown
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        
        return view('ongkos-truck.index', compact('mobils', 'user'));
    }

    /**
     * Show filtered data
     */
    public function showData(Request $request)
    {
        $user = Auth::user();
        
        // Validate request
        $request->validate([
            'tanggal_dari' => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
            'mobil_id' => 'required|array|min:1',
            'mobil_id.*' => 'exists:mobils,id'
        ]);
        
        // Get filter parameters
        $tanggalDari = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $mobilIds = $request->mobil_id;
        
        // Get selected mobil nomor plat
        $selectedMobils = Mobil::whereIn('id', $mobilIds)->get();
        $nomorPlatList = $selectedMobils->pluck('nomor_polisi')->toArray();
        
        // Get Surat Jalan data where tanda terima date is in range
        // Join with TandaTerima and filter by tanggal (tanda terima date)
        $suratJalans = SuratJalan::whereIn('no_plat', $nomorPlatList)
            ->whereHas('tandaTerima', function($query) use ($tanggalDari, $tanggalSampai) {
                $query->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
            })
            ->with(['tandaTerima' => function($query) use ($tanggalDari, $tanggalSampai) {
                $query->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
            }, 'tujuanPengambilanRelation'])
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->get();
        
        // Get Surat Jalan Bongkaran data where tanda terima date is in range
        $suratJalanBongkarans = SuratJalanBongkaran::whereIn('no_plat', $nomorPlatList)
            ->whereHas('tandaTerima', function($query) use ($tanggalDari, $tanggalSampai) {
                $query->whereBetween('tanggal_tanda_terima', [$tanggalDari, $tanggalSampai]);
            })
            ->with(['tandaTerima' => function($query) use ($tanggalDari, $tanggalSampai) {
                $query->whereBetween('tanggal_tanda_terima', [$tanggalDari, $tanggalSampai]);
            }, 'tujuanPengambilanRelation'])
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->get();
        
        // Get all mobils for filter dropdown
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        
        return view('ongkos-truck.index', compact(
            'mobils', 
            'suratJalans', 
            'suratJalanBongkarans', 
            'selectedMobils',
            'nomorPlatList',
            'user'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
