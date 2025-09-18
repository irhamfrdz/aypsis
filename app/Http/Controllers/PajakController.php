<?php

namespace App\Http\Controllers;

use App\Models\Pajak;
use App\Exports\PajakTemplateExport;
use App\Imports\PajakImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PajakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pajak::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        $pajaks = $query->orderBy('nama_status')->paginate(15);

        return view('master-pajak.index', compact('pajaks'));
    }

    /**
     * Download template Excel untuk import pajak
     */
    public function downloadTemplate()
    {
        $export = new PajakTemplateExport();
        return $export->download();
    }

    /**
     * Import data pajak dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048'
        ]);

        try {
            $import = new PajakImport();
            $result = $import->import($request->file('file'));

            if ($result['success_count'] > 0) {
                $message = "Berhasil mengimport {$result['success_count']} data pajak.";
                if (!empty($result['errors'])) {
                    $message .= " Namun ada " . count($result['errors']) . " error: " . implode('; ', array_slice($result['errors'], 0, 3));
                    if (count($result['errors']) > 3) {
                        $message .= " dan " . (count($result['errors']) - 3) . " error lainnya.";
                    }
                }
                return redirect()->route('master.pajak.index')->with('success', $message);
            } else {
                return redirect()->route('master.pajak.index')->with('error', 'Import gagal: ' . implode('; ', $result['errors']));
            }

        } catch (\Exception $e) {
            return redirect()->route('master.pajak.index')->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-pajak.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_status' => 'required|string|max:100|unique:pajaks,nama_status',
            'keterangan' => 'nullable|string|max:500'
        ]);

        Pajak::create([
            'nama_status' => $request->nama_status,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('master.pajak.index')->with('success', 'Pajak berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pajak $pajak)
    {
        return view('master-pajak.show', compact('pajak'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pajak $pajak)
    {
        return view('master-pajak.edit', compact('pajak'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pajak $pajak)
    {
        $request->validate([
            'nama_status' => ['required', 'string', 'max:100', Rule::unique('pajaks')->ignore($pajak->id)],
            'keterangan' => 'nullable|string|max:500'
        ]);

        $pajak->update([
            'nama_status' => $request->nama_status,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('master.pajak.index')->with('success', 'Pajak berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pajak $pajak)
    {
        $pajak->delete();

        return redirect()->route('master.pajak.index')->with('success', 'Pajak berhasil dihapus!');
    }
}
