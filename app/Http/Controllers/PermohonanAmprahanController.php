<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\PermohonanAmprahan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PermohonanAmprahanController extends Controller
{
    public function index(Request $request)
    {
        $kapals = MasterKapal::orderBy('nama')->get();
        $selectedKapal = $request->input('kapal_id');
        $selectedVoyage = $request->input('nomor_voyage');

        $query = PermohonanAmprahan::with(['kapal', 'user'])->latest();

        if ($selectedKapal) {
            $query->where('kapal_id', $selectedKapal);
        }

        if ($selectedVoyage) {
            $query->where('nomor_voyage', 'like', "%{$selectedVoyage}%");
        }

        // if neither is selected, maybe return empty? The user said "halaman pertamanya adalah halaman untuk pilih kapal dan voyage -> muncul semua permohonan yang sudah diinput"
        // We'll show list only if at least one filter is applied, or we can just paginate all.
        // Let's paginate all but they are filterable.
        $permohonans = $query->paginate(15)->withQueryString();

        return view('permohonan-amprahan.index', compact('kapals', 'permohonans', 'selectedKapal', 'selectedVoyage'));
    }

    public function show($id)
    {
        $permohonan = PermohonanAmprahan::with(['kapal', 'user', 'items'])->findOrFail($id);
        
        return view('permohonan-amprahan.show', compact('permohonan'));
    }

    public function print($id)
    {
        $permohonan = PermohonanAmprahan::with(['kapal', 'user', 'items'])->findOrFail($id);
        
        // Since AYPSIS usually uses DOMPDF for printing, or just a printable view.
        // I will just return a view with window.print()
        return view('permohonan-amprahan.print', compact('permohonan'));
    }
}
