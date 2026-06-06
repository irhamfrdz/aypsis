<?php

namespace App\Http\Controllers;

use App\Models\RincianKontainerPelindo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RincianKontainerPelindoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kapal = $request->input('kapal');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 25);

        $query = RincianKontainerPelindo::with(['tandaTerima', 'tandaTerimaTanpaSuratJalan', 'tandaTerimaLcl']);

        // Apply Search (nomor kontainer, seal)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_seal', 'like', "%{$search}%")
                  ->orWhere('kegiatan', 'like', "%{$search}%");
            });
        }

        // Apply Kapal filter
        if ($kapal) {
            $query->where('estimasi_nama_kapal', 'like', "%{$kapal}%");
        }

        // Apply Date Range
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        // Get distinct list of ships for filter dropdown
        $kapals = RincianKontainerPelindo::select('estimasi_nama_kapal')
            ->whereNotNull('estimasi_nama_kapal')
            ->where('estimasi_nama_kapal', '!=', '')
            ->distinct()
            ->orderBy('estimasi_nama_kapal')
            ->pluck('estimasi_nama_kapal');

        return view('rincian-kontainer-pelindo.index', compact('items', 'kapals', 'search', 'kapal', 'startDate', 'endDate'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $item = RincianKontainerPelindo::findOrFail($id);
            $item->delete();

            return redirect()->route('rincian-kontainer-pelindo.index')
                ->with('success', 'Rincian kontainer Pelindo berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting rincian kontainer pelindo: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
