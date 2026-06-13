<?php

namespace App\Http\Controllers;

use App\Models\PranotaPerbaikanKontainer;
use Illuminate\Http\Request;

class PranotaPerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the pranota perbaikan kontainers.
     */
    public function index(Request $request)
    {
        $query = PranotaPerbaikanKontainer::with('creator')->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pranota', '>=', $request->input('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pranota', '<=', $request->input('tanggal_sampai'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pranotaPerbaikanKontainers = $query->paginate(15)->appends($request->query());

        return view('pranota-perbaikan-kontainer.index', compact('pranotaPerbaikanKontainers'));
    }

    /**
     * Display the specified pranota perbaikan kontainer details.
     */
    public function show($id)
    {
        $pranota = PranotaPerbaikanKontainer::with('creator')->findOrFail($id);

        return view('pranota-perbaikan-kontainer.show', compact('pranota'));
    }

    /**
     * Print the specified pranota perbaikan kontainer.
     */
    public function print($id, Request $request)
    {
        $pranota = PranotaPerbaikanKontainer::findOrFail($id);
        $printType = $request->query('type');

        return view('pranota-perbaikan-kontainer.print', compact('pranota', 'printType'));
    }

    /**
     * Remove the specified pranota perbaikan kontainer from storage.
     */
    public function destroy($id)
    {
        try {
            $pranota = PranotaPerbaikanKontainer::findOrFail($id);

            // Revert status_pranota on related container repair records
            if (is_array($pranota->items)) {
                foreach ($pranota->items as $item) {
                    if (isset($item['id'])) {
                        \App\Models\PerbaikanKontainer::where('id', $item['id'])->update(['status_pranota' => 'Belum']);
                    }
                }
            }

            $pranota->delete();

            return redirect()->route('pranota-perbaikan-kontainer.index')
                ->with('success', 'Pranota perbaikan kontainer berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('pranota-perbaikan-kontainer.index')
                ->with('error', 'Gagal menghapus pranota perbaikan kontainer: '.$e->getMessage());
        }
    }
}
