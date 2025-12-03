<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPengirimPenerima;
use App\Exports\MasterPengirimPenerimaTemplateExport;
use App\Imports\MasterPengirimPenerimaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterPengirimPenerimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPengirimPenerima::query();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('npwp', 'like', "%{$search}%");
            });
        }

        $data = $query->latest()->paginate(15)->withQueryString();

        return view('master-pengirim-penerima.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kodeOtomatis = MasterPengirimPenerima::generateKode();
        return view('master-pengirim-penerima.create', compact('kodeOtomatis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:master_pengirim_penerima,kode',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        try {
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            MasterPengirimPenerima::create($validated);

            DB::commit();
            return redirect()->route('master-pengirim-penerima.index')
                           ->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                           ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPengirimPenerima $masterPengirimPenerima)
    {
        $masterPengirimPenerima->load('creator', 'updater');
        return view('master-pengirim-penerima.show', compact('masterPengirimPenerima'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPengirimPenerima $masterPengirimPenerima)
    {
        return view('master-pengirim-penerima.edit', compact('masterPengirimPenerima'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPengirimPenerima $masterPengirimPenerima)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:master_pengirim_penerima,kode,' . $masterPengirimPenerima->id,
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        try {
            $validated['updated_by'] = Auth::id();
            $masterPengirimPenerima->update($validated);

            DB::commit();
            return redirect()->route('master-pengirim-penerima.index')
                           ->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                           ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPengirimPenerima $masterPengirimPenerima)
    {
        DB::beginTransaction();
        try {
            $masterPengirimPenerima->delete();
            
            DB::commit();
            return redirect()->route('master-pengirim-penerima.index')
                           ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import master pengirim/penerima
     */
    public function downloadTemplate()
    {
        $export = new MasterPengirimPenerimaTemplateExport();
        return $export->download();
    }

    /**
     * Import data master pengirim/penerima dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048'
        ]);

        try {
            $import = new MasterPengirimPenerimaImport();
            $result = $import->import($request->file('file'));

            if ($result['success_count'] > 0) {
                $message = "Berhasil mengimport {$result['success_count']} data pengirim/penerima";
                if (!empty($result['errors'])) {
                    $message .= ". Namun ada " . count($result['errors']) . " error: " . implode('; ', $result['errors']);
                }
                return redirect()->route('master-pengirim-penerima.index')->with('success', $message);
            } else {
                return redirect()->route('master-pengirim-penerima.index')->with('error', 'Gagal mengimport data: ' . implode('; ', $result['errors']));
            }
        } catch (\Exception $e) {
            return redirect()->route('master-pengirim-penerima.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
