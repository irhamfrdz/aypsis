<?php

namespace App\Http\Controllers;

use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Http\Request;

class RekapPerbaikanKontainerController extends Controller
{
    public function index()
    {
        // Ambil data kontainer dari stock_kontainers
        $stockKontainers = StockKontainer::select('awalan_kontainer', 'nomor_seri_kontainer', 'akhiran_kontainer', 'nomor_seri_gabungan')
            ->get()
            ->map->nomor_kontainer
            ->filter();
            
        // Ambil data kontainer dari kontainers (sewa)
        $kontainers = Kontainer::select('awalan_kontainer', 'nomor_seri_kontainer', 'akhiran_kontainer', 'nomor_seri_gabungan')
            ->get()
            ->map->nomor_kontainer
            ->filter();
            
        // Gabungkan dan ambil yang unik
        $allKontainers = $stockKontainers->concat($kontainers)->unique()->sort()->values();

        return view('rekap-perbaikan-kontainer.index', compact('allKontainers'));
    }

    public function show(Request $request)
    {
        $request->validate([
            'nomor_kontainer' => 'required|string',
        ]);
        
        $nomorKontainer = $request->nomor_kontainer;
        
        // Fetch pranota perbaikan kontainer that contains this container number
        $pranotas = \App\Models\PranotaPerbaikanKontainer::where('items', 'like', '%' . $nomorKontainer . '%')
            ->orderBy('tanggal_pranota', 'desc')
            ->get();
            
        $riwayatPerbaikan = collect();
        
        foreach ($pranotas as $pranota) {
            $items = is_array($pranota->items) ? $pranota->items : json_decode($pranota->items, true);
            if ($items) {
                foreach ($items as $item) {
                    if (isset($item['no_kontainer']) && $item['no_kontainer'] === $nomorKontainer) {
                        // Add pranota details to the item
                        $item['nomor_pranota'] = $pranota->nomor_pranota;
                        $item['tanggal_pranota'] = $pranota->tanggal_pranota;
                        $item['vendor_pranota'] = $pranota->vendor;
                        
                        // Convert to object for easier blade access
                        $riwayatPerbaikan->push((object)$item);
                    }
                }
            }
        }
        
        // Also fetch from PerbaikanKontainer directly for records not yet in Pranota
        $perbaikans = \App\Models\PerbaikanKontainer::with('bengkel')
            ->where('no_kontainer', $nomorKontainer)
            ->where('status_pranota', '!=', 'Sudah')
            ->orderBy('created_at', 'desc')
            ->get();
            
        foreach ($perbaikans as $perbaikan) {
            $riwayatPerbaikan->push((object)[
                'id' => $perbaikan->id,
                'no_perbaikan' => $perbaikan->no_perbaikan,
                'bengkel' => $perbaikan->bengkel->nama_bengkel ?? '-',
                'estimasi_biaya' => $perbaikan->estimasi_biaya,
                'biaya_riil' => $perbaikan->biaya_riil,
                'biaya_cat' => $perbaikan->biaya_cat,
                'jenis_cat' => $perbaikan->jenis_cat,
                'keterangan_kerusakan' => $perbaikan->keterangan_kerusakan,
                'status' => $perbaikan->status,
                'nomor_pranota' => 'Belum ada pranota',
                'tanggal_pranota' => null,
                'vendor_pranota' => '-',
            ]);
        }

        return view('rekap-perbaikan-kontainer.show', compact('nomorKontainer', 'riwayatPerbaikan'));
    }
}
