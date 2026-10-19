<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;

class MasterArmadaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mobil::with('karyawan')->select('mobils.*')
            ->whereNotNull('imei_gps')
            ->whereRaw("TRIM(imei_gps) <> ''");

        if ($request->user()?->karyawan?->cabang === 'BTM') {
            $query->where('lokasi', 'BTM');
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                foreach (['kode_no', 'nomor_polisi', 'nickname', 'merek', 'jenis', 'lokasi', 'pemakai'] as $column) {
                    $query->orWhere($column, 'like', '%'.$search.'%');
                }
                $query->orWhereHas('karyawan', function ($query) use ($search) {
                    $query->where('nama_lengkap', 'like', '%'.$search.'%')
                        ->orWhere('nik', 'like', '%'.$search.'%');
                });
            });
        }

        $armadas = $query->orderBy('nomor_polisi')->orderBy('id')->paginate(15)->withQueryString();

        return view('master-armada.index', compact('armadas', 'search'));
    }

    public function updateKm(Request $request, Mobil $armada)
    {
        abort_if(trim((string) $armada->imei_gps) === '', 404);
        abort_if($request->user()?->karyawan?->cabang === 'BTM' && $armada->lokasi !== 'BTM', 403);

        $validated = $request->validate([
            'km' => ['nullable', 'numeric', 'min:0', 'max:999999999.999', 'decimal:0,3'],
        ]);

        $armada->km = $validated['km'] ?? null;
        $armada->save();

        return redirect()->back()->with('success', 'Km armada '.$armada->nomor_polisi.' berhasil disimpan.');
    }
}
