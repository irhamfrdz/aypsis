<?php

namespace App\Http\Controllers;

use App\Exports\ReportTandaTerimaJakartaExport;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportTandaTerimaJakartaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user->can('tanda-terima-view')) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        return view('report-tanda-terima-jakarta.index', compact('startDate', 'endDate'));
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (! $user->can('tanda-terima-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $data = $this->getAggregatedData($startDate, $endDate);

        return view('report-tanda-terima-jakarta.view', compact('data', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        if (! $user->can('tanda-terima-view')) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status', 'semua');

        return Excel::download(new ReportTandaTerimaJakartaExport($startDate, $endDate, $status), 'report-tanda-terima-jakarta-'.$startDate.'-to-'.$endDate.'.xlsx');
    }

    private function getAggregatedData($startDate, $endDate)
    {
        $data = collect();

        // Pre-fetch all manifested tanda terima numbers for efficient lookup
        $manifestedTTs = DB::table('manifests')
            ->whereNotNull('nomor_tanda_terima')
            ->where('nomor_tanda_terima', '!=', '')
            ->select('nomor_tanda_terima', 'nama_kapal', 'no_voyage', 'tanggal_berangkat')
            ->get()
            ->keyBy('nomor_tanda_terima');

        // 1. Tanda Terima (Standard)
        $ttStandard = TandaTerima::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->map(function ($item) use ($manifestedTTs) {
                $noTt = $item->no_surat_jalan ?? $item->surat_jalan?->no_surat_jalan ?? '-';
                $manifest = $manifestedTTs->get($noTt);

                return [
                    'source' => 'Standard',
                    'tanggal' => $item->tanggal,
                    'no_tt' => $noTt,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->no_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size,
                    'pengirim' => $item->pengirim,
                    'penerima' => $item->penerima,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->kegiatan,
                    'naik_kapal' => $manifest !== null,
                    'nama_kapal' => $manifest?->nama_kapal ?? null,
                    'no_voyage' => $manifest?->no_voyage ?? null,
                ];
            });
        $data = $data->concat($ttStandard);

        // 2. Tanda Terima Tanpa Surat Jalan
        $ttTSJ = TandaTerimaTanpaSuratJalan::whereBetween('tanggal_tanda_terima', [$startDate, $endDate])
            ->get()
            ->map(function ($item) use ($manifestedTTs) {
                $noTt = $item->no_tanda_terima;
                $manifest = $manifestedTTs->get($noTt);

                return [
                    'source' => 'Tanpa SJ',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $noTt,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->no_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size_kontainer,
                    'pengirim' => $item->pengirim,
                    'penerima' => $item->penerima,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->aktifitas,
                    'naik_kapal' => $manifest !== null,
                    'nama_kapal' => $manifest?->nama_kapal ?? null,
                    'no_voyage' => $manifest?->no_voyage ?? null,
                ];
            });
        $data = $data->concat($ttTSJ);

        // 3. Tanda Terima LCL
        $ttLCL = TandaTerimaLcl::whereBetween('tanggal_tanda_terima', [$startDate, $endDate])
            ->with(['tujuanKirim', 'kontainerPivot'])
            ->get()
            ->map(function ($item) use ($manifestedTTs) {
                $noTt = $item->nomor_tanda_terima;
                $manifest = $manifestedTTs->get($noTt);

                return [
                    'source' => 'LCL',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $noTt,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->nomor_kontainer,
                    'no_seal' => $item->nomor_seal,
                    'size' => $item->kontainerPivot->first()->size_kontainer ?? '-',
                    'pengirim' => $item->nama_pengirim,
                    'penerima' => $item->nama_penerima,
                    'tujuan' => $item->tujuanKirim?->nama_tujuan ?? '-',
                    'keterangan' => $item->kegiatan,
                    'naik_kapal' => $manifest !== null,
                    'nama_kapal' => $manifest?->nama_kapal ?? null,
                    'no_voyage' => $manifest?->no_voyage ?? null,
                ];
            });
        $data = $data->concat($ttLCL);

        return $data->sortBy([
            [function ($item) {
                $hasContainer = ! empty($item['no_kontainer']) && $item['no_kontainer'] != '-';
                $hasSeal = ! empty($item['no_seal']) && $item['no_seal'] != '-';

                return ($hasContainer && $hasSeal) ? 0 : 1;
            }, 'asc'],
            ['source', 'asc'],
            ['no_kontainer', 'asc'],
            ['no_seal', 'asc'],
            ['tanggal', 'desc'],
        ]);
    }
}
