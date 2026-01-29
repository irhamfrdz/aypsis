<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\HistoryKontainer;
use App\Models\Gudang;

class HistoryKontainerController extends Controller
{
    public function index(Request $request)
    {
        $query = HistoryKontainer::with(['gudang', 'creator'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('nomor_kontainer', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_kegiatan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_kegiatan', '<=', $request->end_date);
        }

        if ($request->filled('jenis_kegiatan')) {
            $query->where('jenis_kegiatan', $request->jenis_kegiatan);
        }

        $histories = $query->paginate(20);

        return view('history-kontainer.index', compact('histories'));
    }
}
