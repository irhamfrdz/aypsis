<?php

namespace App\Http\Controllers;

use App\Models\BiayaBensin;
use App\Models\PranotaBiayaBensin;
use App\Models\KodeNomor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PranotaBiayaBensinController extends Controller
{
    public function index()
    {
        $pranotas = PranotaBiayaBensin::with(['creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pranota-biaya-bensin.index', compact('pranotas'));
    }

    public function create(Request $request)
    {
        $selectedIds = $request->input('biaya_bensin_ids', []);
        
        if (empty($selectedIds)) {
            return redirect()->route('biaya-bensin.index')
                ->with('error', 'Silakan pilih setidaknya satu biaya bensin untuk dibuatkan pranota.');
        }

        $biayaBensins = BiayaBensin::with(['mobil', 'alatBerat', 'supir'])
            ->whereIn('id', $selectedIds)
            ->whereNull('pranota_biaya_bensin_id')
            ->where('status', 'approved')
            ->get();

        if ($biayaBensins->isEmpty()) {
            return redirect()->route('biaya-bensin.index')
                ->with('error', 'Data biaya bensin yang dipilih tidak valid atau sudah dibuatkan pranota.');
        }

        $totalBiaya = $biayaBensins->sum('biaya');

        return view('pranota-biaya-bensin.create', compact('biayaBensins', 'totalBiaya', 'selectedIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pranota' => 'required|date',
            'biaya_bensin_ids' => 'required|array|min:1',
            'biaya_bensin_ids.*' => 'exists:biaya_bensin,id',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $biayaBensins = BiayaBensin::whereIn('id', $request->biaya_bensin_ids)
                ->whereNull('pranota_biaya_bensin_id')
                ->where('status', 'approved')
                ->lockForUpdate()
                ->get();

            if ($biayaBensins->count() !== count($request->biaya_bensin_ids)) {
                throw new \Exception('Beberapa biaya bensin tidak valid atau sudah memiliki pranota.');
            }

            // Generate nomor pranota
            $kode = KodeNomor::firstOrCreate(
                ['kode' => 'PBB'],
                ['nomor_terakhir' => 0, 'keterangan' => 'Pranota Biaya Bensin']
            );
            $kode->increment('nomor_terakhir');
            
            $nomorPranota = 'PBB-' . date('Ym') . '-' . str_pad($kode->nomor_terakhir, 4, '0', STR_PAD_LEFT);

            $totalBiaya = $biayaBensins->sum('biaya');

            $pranota = PranotaBiayaBensin::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'total_biaya' => $totalBiaya,
                'status' => PranotaBiayaBensin::STATUS_DRAFT,
                'catatan' => $request->catatan,
                'created_by' => auth()->id(),
            ]);

            foreach ($biayaBensins as $bb) {
                $bb->update(['pranota_biaya_bensin_id' => $pranota->id]);
            }

            DB::commit();

            return redirect()->route('pranota-biaya-bensin.show', $pranota->id)
                ->with('success', 'Pranota Biaya Bensin berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pranota = PranotaBiayaBensin::with(['biayaBensins.mobil', 'biayaBensins.alatBerat', 'biayaBensins.supir', 'creator', 'approver'])->findOrFail($id);
        
        return view('pranota-biaya-bensin.show', compact('pranota'));
    }

    public function print($id)
    {
        $pranota = PranotaBiayaBensin::with(['biayaBensins.mobil', 'biayaBensins.alatBerat', 'biayaBensins.supir', 'creator', 'approver'])->findOrFail($id);
        
        return view('pranota-biaya-bensin.print', compact('pranota'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pranota = PranotaBiayaBensin::findOrFail($id);

            if ($pranota->status === PranotaBiayaBensin::STATUS_PAID) {
                throw new \Exception('Pranota yang sudah dibayar tidak dapat dihapus.');
            }

            // Unlink biaya bensins
            BiayaBensin::where('pranota_biaya_bensin_id', $pranota->id)
                ->update(['pranota_biaya_bensin_id' => null]);

            $pranota->delete();

            DB::commit();

            return redirect()->route('pranota-biaya-bensin.index')
                ->with('success', 'Pranota berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus pranota: ' . $e->getMessage());
        }
    }
}
