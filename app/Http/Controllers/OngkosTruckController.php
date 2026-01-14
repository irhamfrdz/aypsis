<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobil;
use App\Models\OngkosTruck;
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
            'mobil_id' => 'required|exists:mobils,id'
        ]);
        
        // Get filter parameters
        $tanggalDari = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $mobilId = $request->mobil_id;
        
        // Get filtered data (assuming OngkosTruck model exists)
        $ongkosTrucks = collect(); // Temporary empty collection
        
        // Get selected mobil info
        $selectedMobil = Mobil::find($mobilId);
        
        // Get all mobils for filter dropdown
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        
        return view('ongkos-truck.index', compact('mobils', 'ongkosTrucks', 'selectedMobil', 'user'));
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
