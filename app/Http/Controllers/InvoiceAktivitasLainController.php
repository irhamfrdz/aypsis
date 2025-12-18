<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceAktivitasLain;
use App\Models\Karyawan;
use App\Models\Mobil;

class InvoiceAktivitasLainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InvoiceAktivitasLain::query();

        // Filter by nomor_invoice
        if ($request->filled('nomor_invoice')) {
            $query->where('nomor_invoice', 'like', '%' . $request->nomor_invoice . '%');
        }

        // Filter by jenis_aktivitas
        if ($request->filled('jenis_aktivitas')) {
            $query->where('jenis_aktivitas', 'like', '%' . $request->jenis_aktivitas . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $invoices = $query->paginate(20)->withQueryString();

        return view('invoice-aktivitas-lain.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_lengkap', 'asc')->get();
        $mobils = Mobil::orderBy('nomor_polisi', 'asc')->get();
        return view('invoice-aktivitas-lain.create', compact('karyawans', 'mobils'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store logic
        return redirect()->route('invoice-aktivitas-lain.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('invoice-aktivitas-lain.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('invoice-aktivitas-lain.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implement update logic
        return redirect()->route('invoice-aktivitas-lain.index')
            ->with('success', 'Invoice berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implement delete logic
        return redirect()->route('invoice-aktivitas-lain.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    /**
     * Print invoice
     */
    public function print(string $id)
    {
        return view('invoice-aktivitas-lain.print', compact('id'));
    }
}
