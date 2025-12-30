<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Http\Request;

class KontainerSearchController extends Controller
{
    /**
     * Search kontainer dari table kontainers dan stock_kontainers
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $ukuran = $request->input('ukuran');
        $tipe = $request->input('tipe', 'fcl');
        $kegiatan = $request->input('kegiatan', '');
        
        $results = [];
        
        if (strlen($query) >= 1) {
            // Search from kontainers table
            $kontainerQuery = Kontainer::where('nomor_seri_gabungan', 'like', '%' . $query . '%');
            
            if ($ukuran) {
                $kontainerQuery->where('ukuran', $ukuran);
            }
            
            // Show all containers regardless of status, filtered by size only
            
            $kontainers = $kontainerQuery->orderBy('nomor_seri_gabungan')
                                        ->limit(50)
                                        ->get();
            
            foreach ($kontainers as $kontainer) {
                $results[] = [
                    'id' => $kontainer->nomor_seri_gabungan,
                    'text' => $kontainer->nomor_seri_gabungan . ' - ' . $kontainer->ukuran . 'ft',
                    'source' => 'kontainers',
                    'status' => $kontainer->status,
                    'ukuran' => $kontainer->ukuran
                ];
            }
            
            // Search from stock_kontainers table
            $stockQuery = StockKontainer::where('nomor_seri_gabungan', 'like', '%' . $query . '%')
                                       ->where('status', '!=', 'inactive');
            
            if ($ukuran) {
                $stockQuery->where('ukuran', $ukuran);
            }
            
            $stocks = $stockQuery->orderBy('nomor_seri_gabungan')
                                ->limit(50)
                                ->get();
            
            foreach ($stocks as $stock) {
                $results[] = [
                    'id' => $stock->nomor_seri_gabungan,
                    'text' => $stock->nomor_seri_gabungan . ' - ' . $stock->ukuran . 'ft [Stock]',
                    'source' => 'stock_kontainers',
                    'status' => $stock->status,
                    'ukuran' => $stock->ukuran
                ];
            }
        } else {
            // If no search query, return some default results
            $kontainerQuery = Kontainer::query();
            $stockQuery = StockKontainer::where('status', '!=', 'inactive');
            
            if ($ukuran) {
                $kontainerQuery->where('ukuran', $ukuran);
                $stockQuery->where('ukuran', $ukuran);
            }
            
            // Show all containers regardless of status, filtered by size only
            
            $kontainers = $kontainerQuery->orderBy('nomor_seri_gabungan')->limit(50)->get();
            $stocks = $stockQuery->orderBy('nomor_seri_gabungan')->limit(50)->get();
            
            foreach ($kontainers as $kontainer) {
                $results[] = [
                    'id' => $kontainer->nomor_seri_gabungan,
                    'text' => $kontainer->nomor_seri_gabungan . ' - ' . $kontainer->ukuran . 'ft',
                    'source' => 'kontainers',
                    'status' => $kontainer->status,
                    'ukuran' => $kontainer->ukuran
                ];
            }
            
            foreach ($stocks as $stock) {
                $results[] = [
                    'id' => $stock->nomor_seri_gabungan,
                    'text' => $stock->nomor_seri_gabungan . ' - ' . $stock->ukuran . 'ft [Stock]',
                    'source' => 'stock_kontainers',
                    'status' => $stock->status,
                    'ukuran' => $stock->ukuran
                ];
            }
        }
        
        return response()->json([
            'results' => $results
        ]);
    }
}
