<?php

namespace App\Http\Controllers;

use App\Exports\LaporanHarianKasTruckExport;
use App\Models\UangJalan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanHarianKasTruckController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['nullable', 'date'],
        ]);
        $tanggal = $validated['tanggal'] ?? now()->toDateString();

        return view('laporan-harian-kas-truck.index', [
            'tanggal' => $tanggal,
            'jumlahData' => $this->query($tanggal)->count(),
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        $tanggal = $validated['tanggal'];
        $uangJalans = $this->query($tanggal)->get();
        $filename = 'Laporan Harian Kas Truck '.date('d-m-Y', strtotime($tanggal)).'.xlsx';

        return Excel::download(new LaporanHarianKasTruckExport($uangJalans, $tanggal), $filename);
    }

    private function query(string $tanggal)
    {
        return UangJalan::query()
            ->with([
                'suratJalan.order.jenisBarang',
                'suratJalan.jenisBarangRelation',
                'suratJalan.tujuanPengirimanRelation',
            ])
            ->whereNotNull('surat_jalan_id')
            ->whereHas('pranotaUangJalan.pembayaranPranotaUangJalans', function ($query) use ($tanggal) {
                $query->where('pembayaran_pranota_uang_jalans.status_pembayaran', 'paid')
                    ->whereDate('pembayaran_pranota_uang_jalans.tanggal_pembayaran', $tanggal);
            })
            ->orderBy('tanggal_uang_jalan')
            ->orderBy('id');
    }
}
