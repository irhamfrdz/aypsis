<?php

namespace App\Http\Controllers;

use App\Models\ContainerTrip;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Vendor;

class ContainerTripController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContainerTrip::with('vendor');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_kontainer', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($v) use ($search) {
                      $v->where('nama_vendor', 'like', "%{$search}%");
                  });
            });
        }

        $containerTrips = $query->latest()->paginate(10);

        return view('container-trip.index', compact('containerTrips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::all();
        return view('container-trip.create', compact('vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'no_kontainer' => 'required|string|max:255',
            'ukuran' => 'required|in:20,40',
            'tgl_ambil' => 'required|date',
            'tgl_kembali' => 'nullable|date|after_or_equal:tgl_ambil',
            'harga_sewa' => 'required|numeric|min:0',
        ]);

        try {
            ContainerTrip::create($request->all());

            return redirect()->route('container-trip.index')
                           ->with('success', 'Container trip berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContainerTrip $containerTrip)
    {
        $containerTrip->load('vendor');
        return view('container-trip.show', compact('containerTrip'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContainerTrip $containerTrip)
    {
        $vendors = Vendor::all();
        return view('container-trip.edit', compact('containerTrip', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContainerTrip $containerTrip)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'no_kontainer' => 'required|string|max:255',
            'ukuran' => 'required|in:20,40',
            'tgl_ambil' => 'required|date',
            'tgl_kembali' => 'nullable|date|after_or_equal:tgl_ambil',
            'harga_sewa' => 'required|numeric|min:0',
        ]);

        try {
            $containerTrip->update($request->all());

            return redirect()->route('container-trip.index')
                           ->with('success', 'Container trip berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContainerTrip $containerTrip)
    {
        try {
            $containerTrip->delete();
            return redirect()->route('container-trip.index')
                           ->with('success', 'Container trip berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
