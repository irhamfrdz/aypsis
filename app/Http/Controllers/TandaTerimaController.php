<?php

namespace App\Http\Controllers;

use App\Models\TandaTerima;
use App\Models\MasterKapal;
use App\Models\MasterKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TandaTerimaController extends Controller
{
    /**
     * Display a listing of tanda terima
     */
    public function index(Request $request)
    {
        $query = TandaTerima::with(['suratJalan', 'creator', 'updater']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('estimasi_nama_kapal', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_surat_jalan', [$request->start_date, $request->end_date]);
        }

        // Order by newest
        $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('tanda-terima.index', compact('tandaTerimas'));
    }

    /**
     * Show the form for creating a new tanda terima
     */
    public function create()
    {
        // Get master data for dropdowns
        $masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();
        $masterKegiatans = MasterKegiatan::where('status', 'aktif')->orderBy('nama_kegiatan')->get();

        return view('tanda-terima.create', compact('masterKapals', 'masterKegiatans'));
    }

    /**
     * Store a newly created tanda terima in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_surat_jalan' => 'required|string|max:255|unique:tanda_terimas,no_surat_jalan',
            'tanggal_surat_jalan' => 'required|date',
            'supir' => 'nullable|string|max:255',
            'kegiatan' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'jumlah_kontainer' => 'nullable|integer|min:1',
            'no_kontainer' => 'nullable|string',
            'no_seal' => 'nullable|string|max:255',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'estimasi_nama_kapal' => 'nullable|string|max:255',
            'tanggal_ambil_kontainer' => 'nullable|date',
            'tanggal_terima_pelabuhan' => 'nullable|date',
            'tanggal_garasi' => 'nullable|date',
            'jumlah' => 'nullable|integer|min:0',
            'satuan' => 'nullable|string|max:50',
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable|numeric|min:0',
            'tonase' => 'nullable|numeric|min:0',
            'gambar_checkpoint' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'dimensi_items' => 'nullable|array',
            'dimensi_items.*.panjang' => 'nullable|numeric|min:0',
            'dimensi_items.*.lebar' => 'nullable|numeric|min:0',
            'dimensi_items.*.tinggi' => 'nullable|numeric|min:0',
            'dimensi_items.*.meter_kubik' => 'nullable|numeric|min:0',
            'dimensi_items.*.tonase' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'surat_jalan_id' => null, // Manual entry, no surat jalan
                'no_surat_jalan' => $request->no_surat_jalan,
                'tanggal_surat_jalan' => $request->tanggal_surat_jalan,
                'supir' => $request->supir,
                'kegiatan' => $request->kegiatan,
                'size' => $request->size,
                'jumlah_kontainer' => $request->jumlah_kontainer ?? 1,
                'no_kontainer' => $request->no_kontainer,
                'no_seal' => $request->no_seal,
                'tujuan_pengiriman' => $request->tujuan_pengiriman,
                'pengirim' => $request->pengirim,
                'estimasi_nama_kapal' => $request->estimasi_nama_kapal,
                'tanggal_ambil_kontainer' => $request->tanggal_ambil_kontainer,
                'tanggal_terima_pelabuhan' => $request->tanggal_terima_pelabuhan,
                'tanggal_garasi' => $request->tanggal_garasi,
                'jumlah' => $request->jumlah,
                'satuan' => $request->satuan,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'tinggi' => $request->tinggi,
                'meter_kubik' => $request->meter_kubik,
                'tonase' => $request->tonase,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ];

            // If dimensi_items is present, store it as JSON
            if ($request->has('dimensi_items') && is_array($request->dimensi_items)) {
                $data['dimensi_items'] = json_encode($request->dimensi_items);
            }

            // Handle gambar checkpoint upload
            if ($request->hasFile('gambar_checkpoint')) {
                $file = $request->file('gambar_checkpoint');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('gambar_checkpoint', $filename, 'public');
                $data['gambar_checkpoint'] = $path;
            }

            $tandaTerima = TandaTerima::create($data);

            Log::info('Tanda terima created manually', [
                'tanda_terima_id' => $tandaTerima->id,
                'no_surat_jalan' => $tandaTerima->no_surat_jalan,
                'created_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('tanda-terima.index')
                ->with('success', 'Tanda terima berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tanda terima: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat tanda terima: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified tanda terima
     */
    public function edit(TandaTerima $tandaTerima)
    {
        // Load relations
        $tandaTerima->load('suratJalan');

        // Get master kapal for dropdown
        $masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();

        return view('tanda-terima.edit', compact('tandaTerima', 'masterKapals'));
    }

    /**
     * Update the specified tanda terima in storage
     */
    public function update(Request $request, TandaTerima $tandaTerima)
    {
        $request->validate([
            'estimasi_nama_kapal' => 'nullable|string|max:255',
            'tanggal_ambil_kontainer' => 'nullable|date',
            'tanggal_terima_pelabuhan' => 'nullable|date',
            'tanggal_garasi' => 'nullable|date',
            'jumlah' => 'nullable|integer|min:0',
            'satuan' => 'nullable|string|max:50',
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable|numeric|min:0',
            'tonase' => 'nullable|numeric|min:0',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted,approved,completed,cancelled',
            'dimensi_items' => 'nullable|array',
            'dimensi_items.*.panjang' => 'nullable|numeric|min:0',
            'dimensi_items.*.lebar' => 'nullable|numeric|min:0',
            'dimensi_items.*.tinggi' => 'nullable|numeric|min:0',
            'dimensi_items.*.meter_kubik' => 'nullable|numeric|min:0',
            'dimensi_items.*.tonase' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'estimasi_nama_kapal' => $request->estimasi_nama_kapal,
                'tanggal_ambil_kontainer' => $request->tanggal_ambil_kontainer,
                'tanggal_terima_pelabuhan' => $request->tanggal_terima_pelabuhan,
                'tanggal_garasi' => $request->tanggal_garasi,
                'jumlah' => $request->jumlah,
                'satuan' => $request->satuan,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'tinggi' => $request->tinggi,
                'meter_kubik' => $request->meter_kubik,
                'tonase' => $request->tonase,
                'tujuan_pengiriman' => $request->tujuan_pengiriman,
                'catatan' => $request->catatan,
                'updated_by' => Auth::id(),
            ];

            // Only include status if the column exists and request has status
            if ($request->has('status') && Schema::hasColumn('tanda_terimas', 'status')) {
                $updateData['status'] = $request->status;
            }

            // If dimensi_items is present, store it as JSON
            if ($request->has('dimensi_items') && is_array($request->dimensi_items)) {
                $updateData['dimensi_items'] = json_encode($request->dimensi_items);
            }

            $tandaTerima->update($updateData);

            Log::info('Tanda terima updated', [
                'tanda_terima_id' => $tandaTerima->id,
                'no_surat_jalan' => $tandaTerima->no_surat_jalan,
                'updated_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('tanda-terima.index')
                ->with('success', 'Tanda terima berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tanda terima: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui tanda terima: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified tanda terima
     */
    public function show(TandaTerima $tandaTerima)
    {
        $tandaTerima->load(['suratJalan', 'creator', 'updater']);

        return view('tanda-terima.show', compact('tandaTerima'));
    }

    /**
     * Remove the specified tanda terima from storage
     */
    public function destroy(TandaTerima $tandaTerima)
    {
        DB::beginTransaction();
        try {
            $noSuratJalan = $tandaTerima->no_surat_jalan;
            $tandaTerima->delete();

            Log::info('Tanda terima deleted', [
                'tanda_terima_id' => $tandaTerima->id,
                'no_surat_jalan' => $noSuratJalan,
                'deleted_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('tanda-terima.index')
                ->with('success', 'Tanda terima berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting tanda terima: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus tanda terima: ' . $e->getMessage());
        }
    }
}

