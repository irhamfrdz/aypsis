<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\HistoryKontainer;
use App\Models\Gudang;
use App\Models\Kontainer;
use App\Models\StockKontainer;

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

    public function destroy($id)
    {
        $history = HistoryKontainer::findOrFail($id);
        $nomorKontainer = $history->nomor_kontainer;

        // Cari record terakhir untuk kontainer ini
        $latest = HistoryKontainer::where('nomor_kontainer', $nomorKontainer)
            ->orderBy('id', 'desc')
            ->first();

        // Jika rute ini menghapus history terakhir, kembalikan posisi kontainer
        if ($latest && $latest->id == $history->id) {
            // Cari record sebelumnya
            $previous = HistoryKontainer::where('nomor_kontainer', $nomorKontainer)
                ->where('id', '<', $history->id)
                ->orderBy('id', 'desc')
                ->first();

            // Update posisi kontainer ke gudang sebelumnya
            // Coba update di tabel kontainers
            $kontainer = Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
            if ($kontainer) {
                $kontainer->update(['gudangs_id' => $previous ? $previous->gudang_id : null]);
            }
            
            // Juga coba update di tabel stock_kontainers jika ini adalah tipe stock
            $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
            if ($stockKontainer) {
                $stockKontainer->update(['gudangs_id' => $previous ? $previous->gudang_id : null]);
            }
        }

        $history->delete();

        return redirect()->back()->with('success', 'History pergerakan berhasil dihapus.');
    }
}

