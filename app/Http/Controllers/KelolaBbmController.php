<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KelolaBbm;
use App\Models\PricelistUangJalanBatam;
use App\Models\PricelistTarifHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelolaBbmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = KelolaBbm::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tanggal', 'like', "%{$search}%")
                  ->orWhere('bbm_per_liter', 'like', "%{$search}%")
                  ->orWhere('persentase', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }
        
        $kelolaBbm = $query->orderBy('tanggal', 'desc')->paginate(10);
        
        return view('kelola-bbm.index', compact('kelolaBbm', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelola-bbm.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bbm_per_liter' => 'required|numeric|min:0',
            'persentase' => 'required|numeric', // Bisa negatif atau positif
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Simpan data BBM
            $kelolaBbm = KelolaBbm::create($validated);
            
            // Update tarif pricelist uang jalan Batam berdasarkan persentase BBM
            $this->updatePricelistTarif($validated['persentase'], $kelolaBbm->id);
            
            DB::commit();
            
            $message = 'Data BBM berhasil ditambahkan!';
            
            // Tambahkan info berdasarkan persentase
            if ($validated['persentase'] < 5) {
                $message .= " Tarif pricelist uang jalan Batam telah dikembalikan ke nilai awal (persentase di bawah 5%).";
            } elseif ($validated['persentase'] > 5) {
                $perubahanTarif = $validated['persentase'] - 5;
                $message .= " Tarif pricelist uang jalan Batam telah diupdate dengan kenaikan {$perubahanTarif}%.";
            }
            
            return redirect()->route('kelola-bbm.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data BBM: ' . $e->getMessage());
        }
    }
    
    /**
     * Update tarif pricelist berdasarkan persentase BBM
     */
    private function updatePricelistTarif($persentaseBbm, $kelolaBbmId = null)
    {
        // Get all pricelist
        $pricelists = PricelistUangJalanBatam::all();
        
        // Jika persentase di bawah 5%, kembalikan tarif ke nilai base (awal)
        if ($persentaseBbm < 5) {
            foreach ($pricelists as $pricelist) {
                $tarifLama = $pricelist->tarif;
                $tarifBase = $pricelist->tarif_base ?? $tarifLama;
                
                // Hanya update jika tarif saat ini berbeda dengan tarif base
                if ($tarifLama != $tarifBase) {
                    $pricelist->update(['tarif' => $tarifBase]);
                    
                    // Catat history perubahan
                    PricelistTarifHistory::create([
                        'pricelist_uang_jalan_batam_id' => $pricelist->id,
                        'kelola_bbm_id' => $kelolaBbmId,
                        'tarif_lama' => $tarifLama,
                        'tarif_baru' => $tarifBase,
                        'persentase_perubahan' => 0,
                        'persentase_bbm' => $persentaseBbm,
                        'keterangan' => "Tarif dikembalikan ke nilai awal (Rp " . number_format($tarifBase, 0, ',', '.') . ") karena persentase BBM {$persentaseBbm}% (di bawah threshold 5%)",
                    ]);
                }
            }
            return;
        }
        
        // Jika persentase sama dengan 5%, tidak ada perubahan
        if ($persentaseBbm == 5) {
            return;
        }
        
        // Jika persentase di atas 5%, hitung kenaikan tarif
        // Hitung persentase perubahan tarif (persentase BBM - 5%)
        $perubahanTarif = $persentaseBbm - 5;
        
        // Faktor pengali untuk tarif (contoh: 7% BBM = 2% kenaikan tarif)
        $faktorPengali = 1 + ($perubahanTarif / 100);
        
        // Update semua tarif di pricelist uang jalan Batam
        foreach ($pricelists as $pricelist) {
            $tarifBase = $pricelist->tarif_base ?? $pricelist->tarif;
            $tarifLama = $pricelist->tarif;
            
            // Hitung tarif baru berdasarkan tarif base
            $tarifBaru = round($tarifBase * $faktorPengali);
            
            // Update tarif
            $pricelist->update(['tarif' => $tarifBaru]);
            
            // Catat history perubahan
            PricelistTarifHistory::create([
                'pricelist_uang_jalan_batam_id' => $pricelist->id,
                'kelola_bbm_id' => $kelolaBbmId,
                'tarif_lama' => $tarifLama,
                'tarif_baru' => $tarifBaru,
                'persentase_perubahan' => $perubahanTarif,
                'persentase_bbm' => $persentaseBbm,
                'keterangan' => "Tarif diupdate otomatis karena persentase BBM {$persentaseBbm}% (di atas threshold 5%). Kenaikan tarif: {$perubahanTarif}%",
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KelolaBbm $kelolaBbm)
    {
        return view('kelola-bbm.show', compact('kelolaBbm'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KelolaBbm $kelolaBbm)
    {
        return view('kelola-bbm.edit', compact('kelolaBbm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KelolaBbm $kelolaBbm)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bbm_per_liter' => 'required|numeric|min:0',
            'persentase' => 'required|numeric', // Bisa negatif atau positif
            'keterangan' => 'nullable|string',
        ]);

        $kelolaBbm->update($validated);

        return redirect()->route('kelola-bbm.index')
            ->with('success', 'Data BBM berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KelolaBbm $kelolaBbm)
    {
        $kelolaBbm->delete();

        return redirect()->route('kelola-bbm.index')
            ->with('success', 'Data BBM berhasil dihapus!');
    }
}
