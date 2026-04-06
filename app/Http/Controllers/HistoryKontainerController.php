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
        $query = HistoryKontainer::select('history_kontainers.*')
            ->selectSub(function($q) {
                // Prefer asal_gudang_id if available, otherwise fallback to previous record's gudang
                $q->selectRaw("
                    COALESCE(
                        (SELECT g1.nama_gudang FROM gudangs g1 WHERE g1.id = history_kontainers.asal_gudang_id LIMIT 1),
                        (SELECT g2.nama_gudang FROM history_kontainers hk 
                         JOIN gudangs g2 ON hk.gudang_id = g2.id
                         WHERE hk.nomor_kontainer = history_kontainers.nomor_kontainer
                         AND hk.id < history_kontainers.id
                         ORDER BY hk.id DESC LIMIT 1)
                    )
                ");
            }, 'asal_gudang_nama')
            ->with(['gudang', 'creator'])
            ->orderBy('created_at', 'desc');


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
