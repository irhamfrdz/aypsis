<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\Mobil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportOngkosTrukController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        // Get unique plat numbers from both tables
        $platsSj = SuratJalan::select('no_plat')->distinct()->whereNotNull('no_plat')->pluck('no_plat');
        $platsSjb = SuratJalanBongkaran::select('no_plat')->distinct()->whereNotNull('no_plat')->pluck('no_plat');
        
        $allPlats = $platsSj->merge($platsSjb)->unique()->sort()->values();

        return view('report-ongkos-truk.select-date', compact('allPlats'));
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'no_plat' => 'nullable|array'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $noPlat = $request->no_plat;

        $querySj = SuratJalan::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
              ->orWhereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                  $tt->whereBetween('tanggal', [$startDate, $endDate]);
              })
              ->orWhereBetween('tanggal_checkpoint', [$startDate, $endDate]);
        });

        $querySjb = SuratJalanBongkaran::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
              ->orWhereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                  $tt->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
              })
              ->orWhereBetween('tanggal_checkpoint', [$startDate, $endDate]);
        });

        if ($noPlat && count($noPlat) > 0) {
            $querySj->whereIn('no_plat', $noPlat);
            $querySjb->whereIn('no_plat', $noPlat);
        }

        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation'])->get();

        $data = collect();

        foreach ($suratJalans as $sj) {
            $ongkosTruk = 0;
            if ($sj->tujuanPengambilanRelation) {
                $size = strtolower($sj->size ?? '');
                if (str_contains($size, '40')) {
                    $ongkosTruk = $sj->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
                } else {
                    $ongkosTruk = $sj->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
                }
            }

            $data->push([
                'tanggal' => $sj->tanggal_surat_jalan,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk,
                'type' => 'regular'
            ]);
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = 0;
            if ($sjb->tujuanPengambilanRelation) {
                $size = strtolower($sjb->size ?? '');
                if (str_contains($size, '40')) {
                    $ongkosTruk = $sjb->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
                } else {
                    $ongkosTruk = $sjb->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
                }
            }

            $data->push([
                'tanggal' => $sjb->tanggal_surat_jalan,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'rit' => $sjb->rit,
                'ongkos_truck' => $ongkosTruk,
                'type' => 'bongkaran'
            ]);
        }

        $data = $data->sortBy('tanggal');

        return view('report-ongkos-truk.view', compact('data', 'startDate', 'endDate', 'noPlat'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'no_plat' => 'nullable|array'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $noPlat = $request->no_plat;

        $querySj = SuratJalan::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
              ->orWhereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                  $tt->whereBetween('tanggal', [$startDate, $endDate]);
              })
              ->orWhereBetween('tanggal_checkpoint', [$startDate, $endDate]);
        });

        $querySjb = SuratJalanBongkaran::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
              ->orWhereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                  $tt->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
              })
              ->orWhereBetween('tanggal_checkpoint', [$startDate, $endDate]);
        });

        if ($noPlat && count($noPlat) > 0) {
            $querySj->whereIn('no_plat', $noPlat);
            $querySjb->whereIn('no_plat', $noPlat);
        }

        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation'])->get();

        $data = collect();

        foreach ($suratJalans as $sj) {
            $ongkosTruk = 0;
            if ($sj->tujuanPengambilanRelation) {
                $size = strtolower($sj->size ?? '');
                if (str_contains($size, '40')) {
                    $ongkosTruk = $sj->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
                } else {
                    $ongkosTruk = $sj->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
                }
            }

            $data->push([
                'tanggal' => $sj->tanggal_surat_jalan,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk,
                'type' => 'regular'
            ]);
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = 0;
            if ($sjb->tujuanPengambilanRelation) {
                $size = strtolower($sjb->size ?? '');
                if (str_contains($size, '40')) {
                    $ongkosTruk = $sjb->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
                } else {
                    $ongkosTruk = $sjb->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
                }
            }

            $data->push([
                'tanggal' => $sjb->tanggal_surat_jalan,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'rit' => $sjb->rit,
                'ongkos_truck' => $ongkosTruk,
                'type' => 'bongkaran'
            ]);
        }

        $data = $data->sortBy('tanggal');

        return view('report-ongkos-truk.view', compact('data', 'startDate', 'endDate', 'noPlat'))->with('isPrint', true);
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'no_plat' => 'nullable|array'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $noPlat = $request->no_plat;

        $querySj = SuratJalan::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
              ->orWhereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                  $tt->whereBetween('tanggal', [$startDate, $endDate]);
              })
              ->orWhereBetween('tanggal_checkpoint', [$startDate, $endDate]);
        });

        $querySjb = SuratJalanBongkaran::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
              ->orWhereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                  $tt->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
              })
              ->orWhereBetween('tanggal_checkpoint', [$startDate, $endDate]);
        });

        if ($noPlat && count($noPlat) > 0) {
            $querySj->whereIn('no_plat', $noPlat);
            $querySjb->whereIn('no_plat', $noPlat);
        }

        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation'])->get();

        $data = collect();

        foreach ($suratJalans as $sj) {
            $ongkosTruk = 0;
            if ($sj->tujuanPengambilanRelation) {
                $size = strtolower($sj->size ?? '');
                if (str_contains($size, '40')) {
                    $ongkosTruk = $sj->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
                } else {
                    $ongkosTruk = $sj->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
                }
            }

            $data->push([
                'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk
            ]);
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = 0;
            if ($sjb->tujuanPengambilanRelation) {
                $size = strtolower($sjb->size ?? '');
                if (str_contains($size, '40')) {
                    $ongkosTruk = $sjb->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
                } else {
                    $ongkosTruk = $sjb->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
                }
            }

            $data->push([
                'tanggal' => $sjb->tanggal_surat_jalan->format('d/m/Y'),
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'rit' => $sjb->rit,
                'ongkos_truck' => $ongkosTruk
            ]);
        }

        $data = $data->sortBy('tanggal');

        $filename = 'report_ongkos_truk_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Tanggal', 'No Surat Jalan', 'Plat Mobil', 'Supir', 'Keterangan', 'Rit', 'Ongkos Truk']);

            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row['tanggal'],
                    $row['no_surat_jalan'],
                    $row['no_plat'],
                    $row['supir'],
                    $row['keterangan'],
                    $row['rit'],
                    $row['ongkos_truck']
                ]);
            }

            fputcsv($file, ['', '', '', '', '', '', 'TOTAL', $data->sum('ongkos_truck')]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
