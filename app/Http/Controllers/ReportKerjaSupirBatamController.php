<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class ReportKerjaSupirBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $karyawanId = $request->get('karyawan_id', '');

        // Get list of Batam supir for filter dropdown
        $supirList = Karyawan::where('cabang', 'BATAM')
            ->where(function ($q) {
                $q->where('pekerjaan', 'like', 'SUPIR%')
                    ->orWhere('pekerjaan', 'like', '%DRIVER%');
            })
            ->orderBy('nama_lengkap')
            ->get();

        $data = $this->getData($startDate, $endDate, $karyawanId, $supirList);

        return view('report-kerja-supir-batam.index', [
            'supirList' => $supirList,
            'waybills' => $data['waybills'],
            'totalRit' => $data['totalRit'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'karyawanId' => $karyawanId
        ]);
    }

    /**
     * Export to Excel.
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $karyawanId = $request->get('karyawan_id', '');

        if (!$startDate || !$endDate) {
            return back()->with('error', 'Silakan pilih rentang tanggal terlebih dahulu.');
        }

        // Get list of Batam supir for filter dropdown
        $supirList = Karyawan::where('cabang', 'BATAM')
            ->where(function ($q) {
                $q->where('pekerjaan', 'like', 'SUPIR%')
                    ->orWhere('pekerjaan', 'like', '%DRIVER%');
            })
            ->orderBy('nama_lengkap')
            ->get();

        $data = $this->getData($startDate, $endDate, $karyawanId, $supirList);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportKerjaSupirBatamExport($data['waybills'], $startDate, $endDate, $data['totalRit']),
            'Report_Kerja_Supir_Batam_' . $startDate . '_sd_' . $endDate . '.xlsx'
        );
    }

    /**
     * Private helper to fetch the report data.
     */
    private function getData($startDate, $endDate, $karyawanId, $supirList)
    {
        $waybills = [];
        $totalRit = 0;

        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();

            // Kumpulkan nama panggilan & nama lengkap supir yang dipilih atau semua supir batam
            // (karena beberapa tabel menyimpan nama_panggilan, dan yang lain menyimpan nama_lengkap)
            $supirNames = [];
            if ($karyawanId) {
                $karyawan = Karyawan::find($karyawanId);
                if ($karyawan) {
                    $supirNames[] = $karyawan->nama_panggilan;
                    $supirNames[] = $karyawan->nama_lengkap;
                }
            } else {
                $supirNames = array_merge(
                    $supirList->pluck('nama_panggilan')->filter()->toArray(),
                    $supirList->pluck('nama_lengkap')->filter()->toArray()
                );
            }
            
            $supirNames = array_unique($supirNames);

            if (!empty($supirNames)) {
                // 1. Surat Jalan Batam (Regular)
                $regularSJs = \App\Models\SuratJalanBatam::whereIn('supir', $supirNames)
                    ->whereBetween('tanggal_surat_jalan', [$start, $end])
                    ->get();

                // 2. Surat Jalan Bongkaran Batam
                $bongkaranSJs = \App\Models\SuratJalanBongkaranBatam::whereIn('supir', $supirNames)
                    ->whereBetween('tanggal_surat_jalan', [$start, $end])
                    ->get();

                // 3. Surat Jalan Tarik Kosong Batam
                $tarikKosongSJs = \App\Models\SuratJalanTarikKosongBatam::whereIn('supir', $supirNames)
                    ->whereBetween('tanggal_surat_jalan', [$start, $end])
                    ->get();

                // 4. Langsir Batam
                $langsirBatamList = \App\Models\LangsirBatam::whereIn('supir', $supirNames)
                    ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                    ->get();

                foreach ($regularSJs as $sj) {
                    $ritVal = is_numeric($sj->uang_jalan) ? (float) $sj->uang_jalan : 0;
                    $totalRit += $ritVal;
                    $waybills[] = [
                        'tanggal_sort' => $sj->tanggal_surat_jalan->format('Y-m-d H:i:s'),
                        'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                        'tipe' => 'SJ Reguler',
                        'no_dokumen' => $sj->no_surat_jalan,
                        'no_kontainer' => $sj->no_kontainer ?? '-',
                        'supir' => $sj->supir,
                        'uang_jalan' => $ritVal,
                        'tujuan' => $sj->tujuan_pengambilan ?? $sj->tujuan_pengiriman ?? $sj->tujuan_alamat ?? '-',
                    ];
                }

                foreach ($bongkaranSJs as $sj) {
                    $ritVal = is_numeric($sj->uang_jalan_nominal) ? (float) $sj->uang_jalan_nominal : 0;
                    $totalRit += $ritVal;
                    $waybills[] = [
                        'tanggal_sort' => $sj->tanggal_surat_jalan->format('Y-m-d H:i:s'),
                        'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                        'tipe' => 'SJ Bongkaran',
                        'no_dokumen' => $sj->nomor_surat_jalan,
                        'no_kontainer' => $sj->nomor_kontainer ?? '-',
                        'supir' => $sj->supir,
                        'uang_jalan' => $ritVal,
                        'tujuan' => $sj->tujuan_pengambilan ?? $sj->tujuan_pengiriman ?? $sj->tujuan_alamat ?? '-',
                    ];
                }

                foreach ($tarikKosongSJs as $sj) {
                    $ritVal = is_numeric($sj->uang_jalan) ? (float) $sj->uang_jalan : 0;
                    $totalRit += $ritVal;
                    $waybills[] = [
                        'tanggal_sort' => $sj->tanggal_surat_jalan->format('Y-m-d H:i:s'),
                        'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                        'tipe' => 'SJ Tarik Kosong',
                        'no_dokumen' => $sj->no_surat_jalan,
                        'no_kontainer' => $sj->nomor_kontainer ?? '-',
                        'supir' => $sj->supir,
                        'uang_jalan' => $ritVal,
                        'tujuan' => $sj->tujuan_pengambilan ?? $sj->tujuan_pengiriman ?? '-',
                    ];
                }

                foreach ($langsirBatamList as $langsir) {
                    $ritVal = is_numeric($langsir->biaya) ? (float) $langsir->biaya : 0;
                    $totalRit += $ritVal;
                    $waybills[] = [
                        'tanggal_sort' => $langsir->tanggal->format('Y-m-d H:i:s'),
                        'tanggal' => $langsir->tanggal->format('d/m/Y'),
                        'tipe' => 'Langsir Batam',
                        'no_dokumen' => $langsir->no_transaksi,
                        'no_kontainer' => $langsir->no_kontainer ?? '-',
                        'supir' => $langsir->supir,
                        'uang_jalan' => $ritVal,
                        'tujuan' => $langsir->ke ?? '-',
                    ];
                }

                // Urutkan berdasarkan tanggal terbaru ke terlama
                usort($waybills, function ($a, $b) {
                    return strtotime($b['tanggal_sort']) - strtotime($a['tanggal_sort']);
                });
            }
        }

        return [
            'waybills' => $waybills,
            'totalRit' => $totalRit
        ];
    }
}
