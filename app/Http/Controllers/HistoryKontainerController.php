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
        $containerNumbers = HistoryKontainer::query()
            ->whereNotNull('nomor_kontainer')
            ->where('nomor_kontainer', '<>', '')
            ->distinct()
            ->orderBy('nomor_kontainer')
            ->pluck('nomor_kontainer');

        $hasSelection = $request->hasAny(['nomor_kontainer', 'start_date', 'end_date']);
        if (! $hasSelection) {
            return view('history-kontainer.index', compact('containerNumbers', 'hasSelection'));
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nomor_kontainer' => ['required', 'string', 'exists:history_kontainers,nomor_kontainer'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'jenis_kegiatan' => ['nullable', 'in:Masuk,Keluar'],
        ]);
        if ($validator->fails()) {
            $hasSelection = false;

            return view('history-kontainer.index', compact('containerNumbers', 'hasSelection'))
                ->withErrors($validator);
        }
        $filters = $validator->validated();

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
            ->where('nomor_kontainer', $filters['nomor_kontainer'])
            ->whereDate('tanggal_kegiatan', '>=', $filters['start_date'])
            ->whereDate('tanggal_kegiatan', '<=', $filters['end_date'])
            ->orderBy('tanggal_kegiatan', 'desc')
            ->orderBy('created_at', 'desc');

        if (! empty($filters['jenis_kegiatan'])) {
            $query->where('jenis_kegiatan', $filters['jenis_kegiatan']);
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

        return view('history-kontainer.index', compact('histories', 'containerNumbers', 'hasSelection'));
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
