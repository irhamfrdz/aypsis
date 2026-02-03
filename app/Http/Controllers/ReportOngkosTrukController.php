<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\Mobil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportOngkosTrukExport;

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
        
        $allPlatStrings = $platsSj->merge($platsSjb)->unique()->toArray();
        
        // Get Mobil data for the plates
        $mobils = Mobil::whereIn('nomor_polisi', $allPlatStrings)->get();
        
        // Any plates not in Mobil table (if any)
        $knownPlats = $mobils->pluck('nomor_polisi')->toArray();
        $unknownPlats = array_diff($allPlatStrings, $knownPlats);

        return view('report-ongkos-truk.select-date', compact('mobils', 'unknownPlats'));
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

        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation', 'uangJalan'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation', 'uangJalan'])->get();

        $data = collect();

        foreach ($suratJalans as $sj) {
            $ongkosTruk = $this->calculateOngkosTruk($sj);
            $uangJalan = $sj->uangJalan ? $sj->uangJalan->jumlah_total : 0;

            $data->push([
                'tanggal' => $sj->tanggal_surat_jalan,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'tujuan' => $sj->tujuan_pengambilan ?? '-',
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $uangJalan,
                'type' => 'regular'
            ]);
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = $this->calculateOngkosTruk($sjb);
            $uangJalan = $sjb->uangJalan ? $sjb->uangJalan->jumlah_total : 0;

            $data->push([
                'tanggal' => $sjb->tanggal_surat_jalan,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'tujuan' => $sjb->tujuan_pengambilan ?? '-',
                'rit' => $sjb->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $uangJalan,
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
            $ongkosTruk = $this->calculateOngkosTruk($sj);

            $data->push([
                'tanggal' => $sj->tanggal_surat_jalan,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'tujuan' => $sj->tujuan_pengambilan ?? '-',
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk,
                'type' => 'regular'
            ]);
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = $this->calculateOngkosTruk($sjb);

            $data->push([
                'tanggal' => $sjb->tanggal_surat_jalan,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'tujuan' => $sjb->tujuan_pengambilan ?? '-',
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

        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans', 'supirKaryawan', 'kenekKaryawan'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans', 'supirKaryawan', 'kenekKaryawan'])->get();

        $data = collect();

        foreach ($suratJalans as $sj) {
            $ongkosTruk = $this->calculateOngkosTruk($sj);
            $uangJalan = $sj->uangJalan ? $sj->uangJalan->jumlah_total : 0;

            $nomorBukti = '-';
            if ($sj->uangJalan && $sj->uangJalan->pranotaUangJalan) {
                $buktis = collect();
                foreach ($sj->uangJalan->pranotaUangJalan as $pranota) {
                    if ($pranota->pembayaranPranotaUangJalans) {
                        foreach ($pranota->pembayaranPranotaUangJalans as $pembayaran) {
                            if ($pembayaran->nomor_accurate) {
                                $buktis->push($pembayaran->nomor_accurate);
                            }
                        }
                    }
                }
                $nomorBukti = $buktis->unique()->implode(', ') ?: '-';
            }

            $data->push([
                'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'nama_lengkap_supir' => $sj->supirKaryawan ? $sj->supirKaryawan->nama_lengkap : ($sj->supir ?: ($sj->supir2 ?: '-')),
                'nik_supir' => $sj->supir_nik,
                'nama_lengkap_kenek' => $sj->kenekKaryawan ? $sj->kenekKaryawan->nama_lengkap : ($sj->kenek ?: '-'),
                'nik_kenek' => $sj->kenek_nik,
                'rit_supir' => ($sj->supir || $sj->supir2 || $sj->supirKaryawan) ? 1 : 0,
                'rit_kenek' => ($sj->kenek || $sj->kenekKaryawan) ? 1 : 0,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'tujuan' => $sj->tujuan_pengambilan ?? '-',
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $uangJalan,
                'nomor_bukti' => $nomorBukti
            ]);
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = $this->calculateOngkosTruk($sjb);
            $uangJalan = $sjb->uangJalan ? $sjb->uangJalan->jumlah_total : 0;

            $nomorBukti = '-';
            if ($sjb->uangJalan && $sjb->uangJalan->pranotaUangJalan) {
                $buktis = collect();
                foreach ($sjb->uangJalan->pranotaUangJalan as $pranota) {
                    if ($pranota->pembayaranPranotaUangJalans) {
                        foreach ($pranota->pembayaranPranotaUangJalans as $pembayaran) {
                            if ($pembayaran->nomor_accurate) {
                                $buktis->push($pembayaran->nomor_accurate);
                            }
                        }
                    }
                }
                $nomorBukti = $buktis->unique()->implode(', ') ?: '-';
            }

            $data->push([
                'tanggal' => $sjb->tanggal_surat_jalan->format('d/m/Y'),
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'nama_lengkap_supir' => $sjb->supirKaryawan ? $sjb->supirKaryawan->nama_lengkap : ($sjb->supir ?: ($sjb->supir2 ?: '-')),
                'nik_supir' => $sjb->supir_nik,
                'nama_lengkap_kenek' => $sjb->kenekKaryawan ? $sjb->kenekKaryawan->nama_lengkap : ($sjb->kenek ?: '-'),
                'nik_kenek' => $sjb->kenek_nik,
                'rit_supir' => ($sjb->supir || $sjb->supir2 || $sjb->supirKaryawan) ? 1 : 0,
                'rit_kenek' => ($sjb->kenek || $sjb->kenekKaryawan) ? 1 : 0,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'tujuan' => $sjb->tujuan_pengambilan ?? '-',
                'rit' => $sjb->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $uangJalan,
                'nomor_bukti' => $nomorBukti
            ]);
        }

        $data = $data->sortBy('tanggal');

        $filename = 'report_ongkos_truk_' . date('Ymd_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportOngkosTrukExport($data, $startDate, $endDate), 
            $filename
        );
    }

    private function calculateOngkosTruk($item)
    {
        $ongkosTruk = 0;
        if ($item->tujuanPengambilanRelation) {
            $size = strtolower($item->size ?? '');
            if (str_contains($size, '40')) {
                $ongkosTruk = $item->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
            } else {
                $ongkosTruk = $item->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
            }
        }

        // Hardcoded override for specific destination
        if ($item->tujuan_pengambilan == "PULO GADUNG ( BESI SCRAP )") {
            $ongkosTruk = 1050000;
        }

        return $ongkosTruk;
    }
}
