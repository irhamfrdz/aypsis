<?php

namespace App\Http\Controllers;

use App\Models\AlatBerat;
use Illuminate\Http\Request;

class AlatBeratController extends Controller
{
    public function index(Request $request)
    {
        $query = AlatBerat::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_alat', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        $alatBerats = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('master-alat-berat.index', compact('alatBerats'));
    }

    public function create()
    {
        // Simple manual generation logic
        // In a real scenario you might want to lock this or use a DB sequence
        $lastAlat = AlatBerat::orderBy('id', 'desc')->first();
        if (!$lastAlat) {
            $nextKode = 'AB001';
        } else {
            // Extract number from ABxxx
            $lastCode = $lastAlat->kode_alat;
            $number = intval(substr($lastCode, 2));
            $nextNumber = $number + 1;
            $nextKode = 'AB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return view('master-alat-berat.create', compact('nextKode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'nullable|string|max:255',
            'merk' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'nomor_seri' => 'nullable|string|max:255|unique:alat_berats,nomor_seri',
            'tahun_pembuatan' => 'nullable|integer',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Ensure kode_alat is set
        if (empty($data['kode_alat'])) {
            // Re-generate if not provided (though form should have it)
            $lastAlat = AlatBerat::orderBy('id', 'desc')->first();
            if (!$lastAlat) {
                $nextKode = 'AB001';
            } else {
                $lastCode = $lastAlat->kode_alat;
                $number = intval(substr($lastCode, 2));
                $nextNumber = $number + 1;
                $nextKode = 'AB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
            $data['kode_alat'] = $nextKode;
        }

        AlatBerat::create($data);

        return redirect()->route('master.alat-berat.index')->with('success', 'Data Alat Berat berhasil ditambahkan');
    }

    public function show(AlatBerat $alatBerat)
    {
        return view('master-alat-berat.show', compact('alatBerat'));
    }

    public function edit(AlatBerat $alatBerat)
    {
        return view('master-alat-berat.edit', compact('alatBerat'));
    }

    public function update(Request $request, AlatBerat $alatBerat)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'nullable|string|max:255',
            'merk' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'nomor_seri' => 'nullable|string|max:255|unique:alat_berats,nomor_seri,' . $alatBerat->id,
            'tahun_pembuatan' => 'nullable|integer',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'keterangan' => 'nullable|string',
        ]);

        $alatBerat->update($request->all());

        return redirect()->route('master.alat-berat.index')->with('success', 'Data Alat Berat berhasil diperbarui');
    }

    public function destroy(AlatBerat $alatBerat)
    {
        $alatBerat->delete();
        return redirect()->route('master.alat-berat.index')->with('success', 'Data Alat Berat berhasil dihapus');
    }
}
