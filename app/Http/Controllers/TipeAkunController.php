<?php

namespace App\Http\Controllers;

use App\Models\TipeAkun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipeAkunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipeAkun::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tipe_akun', 'LIKE', "%{$search}%")
                  ->orWhere('catatan', 'LIKE', "%{$search}%");
            });
        }

        $tipeAkuns = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('master-tipe-akun.index', compact('tipeAkuns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-tipe-akun.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe_akun' => 'required|string|max:255',
            'catatan' => 'nullable|string|max:500'
        ]);

        TipeAkun::create($request->all());

        return redirect()->route('master.tipe-akun.index')
            ->with('success', 'Tipe Akun berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipeAkun $tipeAkun)
    {
        return view('master-tipe-akun.show', compact('tipeAkun'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipeAkun $tipeAkun)
    {
        return view('master-tipe-akun.edit', compact('tipeAkun'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipeAkun $tipeAkun)
    {
        $request->validate([
            'tipe_akun' => 'required|string|max:255',
            'catatan' => 'nullable|string|max:500'
        ]);

        $tipeAkun->update($request->all());

        return redirect()->route('master.tipe-akun.index')
            ->with('success', 'Tipe Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipeAkun $tipeAkun)
    {
        $tipeAkun->delete();

        return redirect()->route('master.tipe-akun.index')
            ->with('success', 'Tipe Akun berhasil dihapus.');
    }
}
