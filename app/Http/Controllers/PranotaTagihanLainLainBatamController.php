<?php

namespace App\Http\Controllers;

use App\Models\PranotaTagihanLainLainBatam;

class PranotaTagihanLainLainBatamController extends Controller
{
    public function index()
    {
        $pranotas = PranotaTagihanLainLainBatam::withCount('items')
            ->latest('tanggal_pranota')
            ->latest('id')
            ->paginate(20);

        return view('pranota-tagihan-lain-lain-batam.index', compact('pranotas'));
    }

    public function show(PranotaTagihanLainLainBatam $pranotaTagihanLainLainBatam)
    {
        $pranotaTagihanLainLainBatam->load('items.tagihan.mobil');

        return view('pranota-tagihan-lain-lain-batam.show', [
            'pranota' => $pranotaTagihanLainLainBatam,
        ]);
    }
}
