<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceAktivitasLainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Untuk saat ini, tampilkan halaman kosong dengan informasi bahwa fitur sedang dikembangkan
        return view('invoice-aktivitas-lain.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('invoice-aktivitas-lain.create');
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
