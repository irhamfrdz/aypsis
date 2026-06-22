<?php

namespace App\Http\Controllers;

use App\Models\PranotaPerbaikanKontainer;
use Illuminate\Http\Request;

class PranotaPerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the pranota perbaikan kontainers.
     */
    public function index(Request $request)
    {
        $query = PranotaPerbaikanKontainer::with('creator')->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pranota', '>=', $request->input('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pranota', '<=', $request->input('tanggal_sampai'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pranotaPerbaikanKontainers = $query->paginate(15)->appends($request->query());

        return view('pranota-perbaikan-kontainer.index', compact('pranotaPerbaikanKontainers'));
    }

    /**
     * Display the specified pranota perbaikan kontainer details.
     */
    public function show($id)
    {
        $pranota = PranotaPerbaikanKontainer::with('creator')->findOrFail($id);

        return view('pranota-perbaikan-kontainer.show', compact('pranota'));
    }

    /**
     * Print the specified pranota perbaikan kontainer.
     */
    public function print($id, Request $request)
    {
        $pranota = PranotaPerbaikanKontainer::findOrFail($id);
        $printType = $request->query('type');

        return view('pranota-perbaikan-kontainer.print', compact('pranota', 'printType'));
    }

    /**
     * Remove the specified pranota perbaikan kontainer from storage.
     */
    public function destroy($id)
    {
        try {
            $pranota = PranotaPerbaikanKontainer::findOrFail($id);

            // Revert status_pranota on related container repair records
            if (is_array($pranota->items)) {
                foreach ($pranota->items as $item) {
                    if (isset($item['id'])) {
                        \App\Models\PerbaikanKontainer::where('id', $item['id'])->update(['status_pranota' => 'Belum']);
                    }
                }
            }

            $pranota->delete();

            return redirect()->route('pranota-perbaikan-kontainer.index')
                ->with('success', 'Pranota perbaikan kontainer berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('pranota-perbaikan-kontainer.index')
                ->with('error', 'Gagal menghapus pranota perbaikan kontainer: '.$e->getMessage());
        }
    }

    /**
     * Update the costs of an item in the pranota and update corresponding PerbaikanKontainer.
     */
    public function updateItem(\Illuminate\Http\Request $request, $id)
    {
        try {
            $data = $request->validate([
                'item_id' => 'required|exists:perbaikan_kontainers,id',
                'estimasi_biaya' => 'required|numeric|min:0',
                'biaya_riil' => 'required|numeric|min:0',
            ]);

            $pranota = PranotaPerbaikanKontainer::findOrFail($id);
            $items = $pranota->items;

            if (! is_array($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Items pranota tidak valid atau kosong.',
                ], 422);
            }

            $itemUpdated = false;
            $newTotalBiaya = 0;

            foreach ($items as &$item) {
                if (isset($item['id']) && $item['id'] == $data['item_id']) {
                    $item['estimasi_biaya'] = $data['estimasi_biaya'];
                    $item['biaya_riil'] = $data['biaya_riil'];
                    $itemUpdated = true;
                }

                // Recalculate each item's biaya_terpakai
                $biayaRiil = floatval($item['biaya_riil'] ?? 0);
                $estimasi = floatval($item['estimasi_biaya'] ?? 0);
                $biayaCat = floatval($item['biaya_cat'] ?? 0);
                $biayaTerpakai = (($biayaRiil > 0) ? $biayaRiil : $estimasi) + $biayaCat;
                $newTotalBiaya += $biayaTerpakai;
            }

            if (! $itemUpdated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan di dalam pranota ini.',
                ], 404);
            }

            $pranota->items = $items;
            $pranota->total_biaya = $newTotalBiaya;
            $pranota->save();

            // Also update PerbaikanKontainer
            $perbaikan = \App\Models\PerbaikanKontainer::findOrFail($data['item_id']);
            $perbaikan->update([
                'estimasi_biaya' => $data['estimasi_biaya'],
                'biaya_riil' => $data['biaya_riil'],
                'updated_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biaya item perbaikan berhasil diperbarui.',
                'subtotal' => $newTotalBiaya,
                'adjustment' => floatval($pranota->adjustment),
                'total_keseluruhan' => $newTotalBiaya + floatval($pranota->adjustment),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui biaya item: '.$e->getMessage(),
            ], 500);
        }
    }
}
