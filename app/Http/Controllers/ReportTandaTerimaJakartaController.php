<?php

namespace App\Http\Controllers;

use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaBongkaran;
use App\Models\TandaTerimaLcl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportTandaTerimaJakartaExport;

class ReportTandaTerimaJakartaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('tanda-terima-view')) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        return view('report-tanda-terima-jakarta.index', compact('startDate', 'endDate'));
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('tanda-terima-view')) {
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

        if (!$user->can('tanda-terima-view')) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        return Excel::download(new ReportTandaTerimaJakartaExport($startDate, $endDate), 'report-tanda-terima-jakarta-' . $startDate . '-to-' . $endDate . '.xlsx');
    }

    private function getAggregatedData($startDate, $endDate)
    {
        $data = collect();

        // 1. Tanda Terima (Standard)
        $ttStandard = TandaTerima::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->map(function($item) {
                return [
                    'source' => 'Standard',
                    'tanggal' => $item->tanggal,
                    'no_tt' => $item->no_surat_jalan ?? $item->surat_jalan?->no_surat_jalan ?? '-',
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->no_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size,
                    'pengirim' => $item->pengirim,
                    'penerima' => $item->penerima,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->kegiatan,
                ];
            });
        $data = $data->concat($ttStandard);

        // 2. Tanda Terima Tanpa Surat Jalan
        $ttTSJ = TandaTerimaTanpaSuratJalan::whereBetween('tanggal_tanda_terima', [$startDate, $endDate])
            ->get()
            ->map(function($item) {
                return [
                    'source' => 'Tanpa SJ',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $item->no_tanda_terima,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->no_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size_kontainer,
                    'pengirim' => $item->pengirim,
                    'penerima' => $item->penerima,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->aktifitas,
                ];
            });
        $data = $data->concat($ttTSJ);

        // 3. Tanda Terima LCL
        $ttLCL = TandaTerimaLcl::whereBetween('tanggal_tanda_terima', [$startDate, $endDate])
            ->with(['tujuanKirim'])
            ->get()
            ->map(function($item) {
                return [
                    'source' => 'LCL',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $item->nomor_tanda_terima,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->nomor_kontainer,
                    'no_seal' => $item->nomor_seal,
                    'size' => '-',
                    'pengirim' => $item->nama_pengirim,
                    'penerima' => $item->nama_penerima,
                    'tujuan' => $item->tujuanKirim?->nama_tujuan ?? '-',
                    'keterangan' => $item->kegiatan,
                ];
            });
        $data = $data->concat($ttLCL);

        return $data->sortBy([
            [function($item) {
                $hasContainer = !empty($item['no_kontainer']) && $item['no_kontainer'] != '-';
                $hasSeal = !empty($item['no_seal']) && $item['no_seal'] != '-';
                return ($hasContainer && $hasSeal) ? 0 : 1;
            }, 'asc'],
            ['source', 'asc'],
            ['no_kontainer', 'asc'],
            ['no_seal', 'asc'],
            ['tanggal', 'desc'],
        ]);
    }
}
