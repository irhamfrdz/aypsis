<?php

namespace App\Http\Controllers;

use App\Models\TandaTerimaSuratJalanTarikKosongBatam;
use App\Models\SuratJalanTarikKosongBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TandaTerimaSuratJalanTarikKosongBatamController extends Controller
{
    public function index(Request $request)
    {
        $query = TandaTerimaSuratJalanTarikKosongBatam::with(['suratJalan', 'creator'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_tanda_terima', 'like', "%{$search}%")
                  ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('tanggal_tanda_terima', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('tanggal_tanda_terima', '<=', $request->to_date);
        }

        $items = $query->paginate(20)->withQueryString();
        return view('tanda-terima-surat-jalan-tarik-kosong-batam.index', compact('items'));
    }

    public function create(Request $request)
    {
        $suratJalanId = $request->surat_jalan_id;
        $suratJalan = null;
        if ($suratJalanId) {
            $suratJalan = SuratJalanTarikKosongBatam::findOrFail($suratJalanId);
        }

        $noTandaTerima = $this->generateNoTandaTerima();
        return view('tanda-terima-surat-jalan-tarik-kosong-batam.create', compact('suratJalan', 'noTandaTerima'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_tanda_terima' => 'required|string|unique:tanda_terima_surat_jalan_tarik_kosong_batams,no_tanda_terima',
            'tanggal_tanda_terima' => 'required|date',
            'surat_jalan_tarik_kosong_batam_id' => 'nullable|exists:surat_jalan_tarik_kosong_batams,id',
            'penerima' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();

        // If surat_jalan is selected, sync redundant data
        if ($request->surat_jalan_tarik_kosong_batam_id) {
            $sj = SuratJalanTarikKosongBatam::find($request->surat_jalan_tarik_kosong_batam_id);
            if ($sj) {
                $data['no_surat_jalan'] = $sj->no_surat_jalan;
                $data['tanggal_surat_jalan'] = $sj->tanggal_surat_jalan;
                $data['supir'] = $sj->supir;
                $data['no_plat'] = $sj->no_plat;
                $data['no_kontainer'] = $sj->no_kontainer;
                $data['size'] = $sj->size;
            }
        }

        TandaTerimaSuratJalanTarikKosongBatam::create($data);

        return redirect()->route('tanda-terima-surat-jalan-tarik-kosong-batam.index')
            ->with('success', 'Tanda Terima berhasil disimpan');
    }

    public function show($id)
    {
        $item = TandaTerimaSuratJalanTarikKosongBatam::with(['suratJalan', 'creator', 'updater'])->findOrFail($id);
        return view('tanda-terima-surat-jalan-tarik-kosong-batam.show', compact('item'));
    }

    public function edit($id)
    {
        $item = TandaTerimaSuratJalanTarikKosongBatam::findOrFail($id);
        return view('tanda-terima-surat-jalan-tarik-kosong-batam.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_tanda_terima' => 'required|string|unique:tanda_terima_surat_jalan_tarik_kosong_batams,no_tanda_terima,' . $id,
            'tanggal_tanda_terima' => 'required|date',
            'penerima' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $item = TandaTerimaSuratJalanTarikKosongBatam::findOrFail($id);
        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $item->update($data);

        return redirect()->route('tanda-terima-surat-jalan-tarik-kosong-batam.index')
            ->with('success', 'Tanda Terima berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = TandaTerimaSuratJalanTarikKosongBatam::findOrFail($id);
        $item->delete();

        return redirect()->route('tanda-terima-surat-jalan-tarik-kosong-batam.index')
            ->with('success', 'Tanda Terima berhasil dihapus');
    }

    public function print($id)
    {
        $item = TandaTerimaSuratJalanTarikKosongBatam::with(['suratJalan', 'creator'])->findOrFail($id);
        return view('tanda-terima-surat-jalan-tarik-kosong-batam.print', compact('item'));
    }

    private function generateNoTandaTerima()
    {
        $prefix = 'TT-SJTK-' . date('y') . date('m') . '-';
        $last = TandaTerimaSuratJalanTarikKosongBatam::where('no_tanda_terima', 'like', $prefix . '%')
            ->orderBy('no_tanda_terima', 'desc')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->no_tanda_terima, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    public function getSuratJalanData(Request $request)
    {
        $search = $request->search;
        $items = SuratJalanTarikKosongBatam::where('no_surat_jalan', 'like', "%{$search}%")
            ->orWhere('no_kontainer', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($items);
    }
}
