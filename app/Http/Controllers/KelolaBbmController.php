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
                $q->where('bulan', 'like', "%{$search}%")
                  ->orWhere('tahun', 'like', "%{$search}%")
                  ->orWhere('bbm_per_liter', 'like', "%{$search}%")
                  ->orWhere('persentase', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }
        
        $kelolaBbm = $query->orderBy('tahun', 'desc')
                          ->orderBy('bulan', 'desc')
                          ->paginate(10);
        
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
        $pricelists = PricelistUangJalanBatam::all();
        $fields = [
            'tarif_20ft_full' => 'tarif_20ft_full_base',
            'tarif_20ft_empty' => 'tarif_20ft_empty_base',
            'tarif_40ft_full' => 'tarif_40ft_full_base',
            'tarif_40ft_empty' => 'tarif_40ft_empty_base',
        ];

        // Jika persentase di bawah 5%, kembalikan tarif ke nilai base
        if ($persentaseBbm < 5) {
            foreach ($pricelists as $pricelist) {
                $updateData = [];
                $historyLogs = [];

                foreach ($fields as $tarifField => $baseField) {
                    $tarifLama = $pricelist->$tarifField;
                    $tarifBase = $pricelist->$baseField ?? $tarifLama;

                    if ($tarifLama != $tarifBase) {
                        $updateData[$tarifField] = $tarifBase;
                        $historyLogs[] = [
                            'field' => $tarifField,
                            'old' => $tarifLama,
                            'new' => $tarifBase
                        ];
                    }
                }

                if (!empty($updateData)) {
                    $pricelist->update($updateData);

                    foreach ($historyLogs as $log) {
                        PricelistTarifHistory::create([
                            'pricelist_uang_jalan_batam_id' => $pricelist->id,
                            'kelola_bbm_id' => $kelolaBbmId,
                            'tarif_lama' => $log['old'],
                            'tarif_baru' => $log['new'],
                            'persentase_perubahan' => 0,
                            'persentase_bbm' => $persentaseBbm,
                            'keterangan' => "Tarif {$log['field']} dikembalikan ke nilai awal karena persentase BBM {$persentaseBbm}%",
                        ]);
                    }
                }
            }
            return;
        }

        if ($persentaseBbm == 5) return;

        $perubahanTarif = $persentaseBbm - 5;
        $faktorPengali = 1 + ($perubahanTarif / 100);

        foreach ($pricelists as $pricelist) {
            $updateData = [];
            $historyLogs = [];

            foreach ($fields as $tarifField => $baseField) {
                $tarifBase = $pricelist->$baseField ?? $pricelist->$tarifField;
                $tarifLama = $pricelist->$tarifField;
                $tarifBaru = round($tarifBase * $faktorPengali);

                if ($tarifLama != $tarifBaru) {
                    $updateData[$tarifField] = $tarifBaru;
                    $historyLogs[] = [
                        'field' => $tarifField,
                        'old' => $tarifLama,
                        'new' => $tarifBaru
                    ];
                }
            }

            if (!empty($updateData)) {
                $pricelist->update($updateData);

                foreach ($historyLogs as $log) {
                    PricelistTarifHistory::create([
                        'pricelist_uang_jalan_batam_id' => $pricelist->id,
                        'kelola_bbm_id' => $kelolaBbmId,
                        'tarif_lama' => $log['old'],
                        'tarif_baru' => $log['new'],
                        'persentase_perubahan' => $perubahanTarif,
                        'persentase_bbm' => $persentaseBbm,
                        'keterangan' => "Tarif {$log['field']} diupdate otomatis (BBM {$persentaseBbm}%, Kenaikan {$perubahanTarif}%)",
                    ]);
                }
            }
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
