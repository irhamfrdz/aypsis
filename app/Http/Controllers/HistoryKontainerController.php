<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\HistoryKontainer;
use App\Models\SuratJalan;
use App\Models\TandaTerima;
use Illuminate\Http\Request;

class HistoryKontainerController extends Controller
{
    public function index(Request $request)
    {
        $query = HistoryKontainer::select('history_kontainers.*')
            ->selectSub(function ($q) {
                // Derive the origin from the previous chronological movement first.
                $q->selectRaw('
                    COALESCE(
                        (SELECT g2.nama_gudang FROM history_kontainers hk
                         JOIN gudangs g2 ON hk.gudang_id = g2.id
                         WHERE hk.nomor_kontainer = history_kontainers.nomor_kontainer
                         AND (
                            hk.tanggal_kegiatan < history_kontainers.tanggal_kegiatan
                            OR (hk.tanggal_kegiatan = history_kontainers.tanggal_kegiatan AND hk.created_at < history_kontainers.created_at)
                            OR (hk.tanggal_kegiatan = history_kontainers.tanggal_kegiatan AND hk.created_at = history_kontainers.created_at AND hk.id < history_kontainers.id)
                         )
                         ORDER BY hk.tanggal_kegiatan DESC, hk.created_at DESC, hk.id DESC LIMIT 1),
                        (SELECT g1.nama_gudang FROM gudangs g1 WHERE g1.id = history_kontainers.asal_gudang_id LIMIT 1)
                    )
                ');
            }, 'asal_gudang_nama')
            ->with(['gudang', 'creator'])
            ->orderBy('tanggal_kegiatan', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('nomor_kontainer', 'like', '%'.$request->search.'%');
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

        $nomorSuratJalans = $histories->getCollection()
            ->map(function (HistoryKontainer $history) {
                if (preg_match('/^Masuk via Tanda Terima:\s*(.+)$/i', trim((string) $history->keterangan), $matches)) {
                    return trim($matches[1]);
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values();

        $tandaTerimas = TandaTerima::whereIn('no_surat_jalan', $nomorSuratJalans)
            ->orderByDesc('id')
            ->get(['id', 'no_surat_jalan', 'supir', 'supir_pengganti'])
            ->unique('no_surat_jalan')
            ->keyBy('no_surat_jalan');

        $suratJalans = SuratJalan::whereIn('no_surat_jalan', $nomorSuratJalans)
            ->get(['no_surat_jalan', 'supir'])
            ->keyBy('no_surat_jalan');

        $histories->getCollection()->each(function (HistoryKontainer $history) use ($tandaTerimas, $suratJalans) {
            if (! preg_match('/^Masuk via Tanda Terima:\s*(.+)$/i', trim((string) $history->keterangan), $matches)) {
                return;
            }

            $nomorSuratJalan = trim($matches[1]);
            $tandaTerima = $tandaTerimas->get($nomorSuratJalan);
            $supir = $tandaTerima?->supir_pengganti
                ?: $tandaTerima?->supir
                ?: $suratJalans->get($nomorSuratJalan)?->supir;

            $history->setAttribute('supir_tanda_terima', $supir);
            $history->setAttribute('is_masuk_via_tanda_terima', true);
        });

        return view('history-kontainer.index', compact('histories'));
    }

    public function destroy($id)
    {
        $history = HistoryKontainer::findOrFail($id);
        $nomorKontainer = $history->nomor_kontainer;

        $history->delete();
        HistoryKontainer::syncCurrentLocation($nomorKontainer);

        return redirect()->back()->with('success', 'History pergerakan berhasil dihapus.');
    }

    public function updateTanggal(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_kegiatan' => ['required', 'date_format:Y-m-d'],
        ]);

        $history = HistoryKontainer::findOrFail($id);
        $history->update($validated);

        return redirect()->back()->with('success', 'Tanggal history pergerakan berhasil diperbarui.');
    }
}
