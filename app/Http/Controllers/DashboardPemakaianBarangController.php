<?php

namespace App\Http\Controllers;

use App\Models\StockAmprahanUsage;
use App\Models\StockBanDalamUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardPemakaianBarangController extends Controller
{
    public function index()
    {
        $categories = ['penerima' => 'Penerima / Karyawan', 'kendaraan' => 'Kendaraan / Truck', 'alat_berat' => 'Alat Berat', 'kapal' => 'Kapal', 'kantor' => 'Kantor'];

        return view('dashboard-pemakaian-barang.index', compact('categories'));
    }

    public function show(Request $request)
    {
        $categories = ['penerima' => 'Penerima / Karyawan', 'kendaraan' => 'Kendaraan / Truck', 'alat_berat' => 'Alat Berat', 'kapal' => 'Kapal', 'kantor' => 'Kantor'];
        $filters = $request->validate([
            'kategori_pemakai' => 'required|in:penerima,kendaraan,alat_berat,kapal,kantor',
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
        ]);
        $fromDate = Carbon::parse($filters['from_date'])->startOfDay();
        $toDate = Carbon::parse($filters['to_date'])->endOfDay();
        $yearStart = $fromDate->copy()->startOfYear();
        $query = StockAmprahanUsage::whereBetween('tanggal_pengambilan', [$yearStart, $toDate]);
        if ($filters['kategori_pemakai'] === 'kendaraan') {
            $query->where(fn ($q) => $q->whereNotNull('kendaraan_id')->orWhereNotNull('truck_id')->orWhereNotNull('buntut_id'));
        } else {
            $column = $filters['kategori_pemakai'] === 'kantor' ? 'kantor' : $filters['kategori_pemakai'].'_id';
            $query->whereNotNull($column);
            if ($column === 'kantor') {
                $query->where('kantor', '!=', '');
            }
        }
        if ($filters['kategori_pemakai'] === 'kapal') {
            $query->whereHas('kapal', function ($q) {
                $q->whereRaw('UPPER(TRIM(pelayaran)) = ?', ['PT. ALEXINDO YAKIN PRIMA']);
            });
        }
        // Same branch restriction as valuasi pemakaian.
        $user = $request->user();
        $batamOnly = $user && $user->karyawan && strtoupper($user->karyawan->cabang ?? '') === 'BATAM'
            && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin');
        if ($batamOnly) {
            $query->whereHas('stockAmprahan', fn ($q) => $q->where('lokasi', 'like', '%BATAM%'));
        }
        $kategori = $filters['kategori_pemakai'];
        $names = match ($kategori) {
            'kapal' => \App\Models\MasterKapal::whereRaw('UPPER(TRIM(pelayaran)) = ?', ['PT. ALEXINDO YAKIN PRIMA'])
                ->orderBy('nama_kapal')
                ->get()
                ->mapWithKeys(fn ($kapal) => [
                    $kapal->id => $kapal->nama_kapal.' — '.$kapal->pelayaran,
                ]),
            'penerima' => \App\Models\Karyawan::orderBy('nama_lengkap')->pluck('nama_lengkap', 'id'),
            'kendaraan' => \App\Models\Mobil::orderBy('nomor_polisi')->get()->mapWithKeys(fn ($mobil) => [
                $mobil->id => ($mobil->nomor_polisi && $mobil->nomor_polisi !== '-' ? $mobil->nomor_polisi : ($mobil->no_kir ?: 'Kendaraan #'.$mobil->id)),
            ]),
            'alat_berat' => \App\Models\AlatBerat::orderBy('nama')->get()->mapWithKeys(fn ($alat) => [
                $alat->id => trim($alat->kode_alat.' '.$alat->nama),
            ]),
            // Offices are stored as text on usage records, not as a master relation.
            'kantor' => (clone $query)->reorder()->distinct()->pluck('kantor')->mapWithKeys(fn ($name) => [$name => $name]),
        };
        $kapalIdRedirects = [];
        if ($kategori === 'kapal') {
            $yakinTangguhId = $names->search(fn ($name) => str_contains(strtoupper($name), 'YAKIN TANGGUH'));
            if ($yakinTangguhId !== false) {
                $alexindo8Ids = $names
                    ->filter(fn ($name) => str_contains(strtoupper($name), 'ALEXINDO 8'))
                    ->keys();

                foreach ($alexindo8Ids as $alexindo8Id) {
                    $kapalIdRedirects[$alexindo8Id] = $yakinTangguhId;
                }

                $names = $names->except($alexindo8Ids);
            }
        }
        $cards = $names->mapWithKeys(fn ($name, $id) => [$id => [
            'nama' => $name, 'saldo_awal' => 0, 'saldo_berjalan' => 0,
        ]])->all();

        foreach ($query->with('stockAmprahan')->lazyById(500) as $usage) {
            $ids = $kategori === 'kendaraan'
                ? array_unique(array_filter([$usage->kendaraan_id, $usage->truck_id, $usage->buntut_id]))
                : [$usage->{$kategori === 'kantor' ? 'kantor' : $kategori.'_id'}];
            $balance = Carbon::parse($usage->tanggal_pengambilan)->lt($fromDate) ? 'saldo_awal' : 'saldo_berjalan';
            $nilai = (float) $usage->jumlah * (float) ($usage->stockAmprahan->harga_satuan ?? 0);
            foreach ($ids as $id) {
                $id = $kapalIdRedirects[$id] ?? $id;
                if (! isset($cards[$id])) {
                    $cards[$id] = ['nama' => $categories[$kategori].' #'.$id, 'saldo_awal' => 0, 'saldo_berjalan' => 0];
                }
                $cards[$id][$balance] += $nilai;
            }
        }
        if ($kategori === 'kendaraan') {
            $banPerutQuery = StockBanDalamUsage::query()
                ->join('stock_ban_dalams', 'stock_ban_dalams.id', '=', 'stock_ban_dalam_usages.stock_ban_dalam_id')
                ->join('nama_stock_bans', 'nama_stock_bans.id', '=', 'stock_ban_dalams.nama_stock_ban_id')
                ->whereRaw('LOWER(nama_stock_bans.nama) like ?', ['%ban perut%'])
                ->whereNotNull('stock_ban_dalam_usages.mobil_id')
                ->whereDate('stock_ban_dalam_usages.tanggal_keluar', '>=', $fromDate->toDateString())
                ->whereDate('stock_ban_dalam_usages.tanggal_keluar', '<=', $toDate->toDateString());
            if ($batamOnly) {
                $banPerutQuery->where('stock_ban_dalams.lokasi', 'like', '%BATAM%');
            }
            $banPerutCounts = $banPerutQuery
                ->selectRaw('stock_ban_dalam_usages.mobil_id, COUNT(*) as total')
                ->groupBy('stock_ban_dalam_usages.mobil_id')->get();
            foreach ($banPerutCounts as $count) {
                if (isset($cards[$count->mobil_id])) {
                    $cards[$count->mobil_id]['ban_perut_total'] = $count->total;
                }
            }
        }
        $cards = collect($cards)->sortBy('nama')->values();

        return view('dashboard-pemakaian-barang.show', compact('categories', 'cards', 'kategori', 'fromDate', 'toDate', 'yearStart'));
    }
}
