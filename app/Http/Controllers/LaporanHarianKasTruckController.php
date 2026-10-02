<?php

namespace App\Http\Controllers;

use App\Exports\LaporanHarianKasTruckExport;
use App\Models\UangJalan;
use App\Models\UangJalanBongkaran;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'jumlahData' => $this->data($tanggal)->count(),
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        $tanggal = $validated['tanggal'];
        $uangJalans = $this->data($tanggal);
        $filename = 'Laporan Harian Kas Truck '.date('d-m-Y', strtotime($tanggal)).'.xlsx';

        return Excel::download(new LaporanHarianKasTruckExport($uangJalans, $tanggal), $filename);
    }

    private function data(string $tanggal): Collection
    {
        $uangJalans = UangJalan::query()
            ->with([
                'suratJalan.order.jenisBarang',
                'suratJalan.jenisBarangRelation',
                'suratJalan.tujuanPengambilanRelation',
                'suratJalanBongkaran.tujuanPengambilanRelation',
            ])
            ->where(function ($query) {
                $query->whereNotNull('surat_jalan_id')
                    ->orWhereNotNull('surat_jalan_bongkaran_id');
            })
            ->whereHas('pranotaUangJalan.pembayaranPranotaUangJalans', function ($query) use ($tanggal) {
                $query->where('pembayaran_pranota_uang_jalans.status_pembayaran', 'paid')
                    ->whereDate('pembayaran_pranota_uang_jalans.tanggal_pembayaran', $tanggal);
            })
            ->orderBy('tanggal_uang_jalan')
            ->orderBy('id')
            ->get();

        $uangJalanBongkarans = UangJalanBongkaran::query()
            ->with([
                'suratJalanBongkaran.tujuanPengambilanRelation',
            ])
            ->whereNotNull('surat_jalan_bongkaran_id')
            ->whereHas('pranotaUangJalanBongkaran.pembayaranPranotaUangJalanBongkarans', function ($query) use ($tanggal) {
                $query->where('pembayaran_pranota_uang_jalan_bongkarans.status_pembayaran', 'paid')
                    ->whereDate('pembayaran_pranota_uang_jalan_bongkarans.tanggal_pembayaran', $tanggal);
            })
            ->orderBy('tanggal_uang_jalan')
            ->orderBy('id')
            ->get();

        return $uangJalans
            ->concat($uangJalanBongkarans)
            ->sortBy(fn ($uangJalan) => ($uangJalan->tanggal_uang_jalan?->format('Y-m-d') ?? '').'|'.str_pad((string) $uangJalan->id, 10, '0', STR_PAD_LEFT))
            ->values();
    }
}
