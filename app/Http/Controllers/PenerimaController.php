<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Penerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Facades\Excel; // Commented out until Export class is created
// use App\Exports\PenerimaExport; // Commented out until Export class is created

class PenerimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Penerima::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_penerima', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('contact_person', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $penerimas = $query->paginate(15);

        return view('master-penerima.index', compact('penerimas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-penerima.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nitku' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'iu_bp_kawasan' => 'nullable|in:ada,tidak ada',
        ]);

        $penerima = Penerima::create($request->all());

        return redirect()->route('penerima.index')->with('success', 'Penerima berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penerima = Penerima::findOrFail($id);
        return view('master-penerima.show', compact('penerima'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penerima = Penerima::findOrFail($id);
        return view('master-penerima.edit', compact('penerima'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $penerima = Penerima::findOrFail($id);

        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nitku' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'iu_bp_kawasan' => 'nullable|in:ada,tidak ada',
        ]);

        $penerima->update($request->all());

        return redirect()->route('penerima.index')->with('success', 'Penerima berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penerima = Penerima::findOrFail($id);
        $penerima->delete();

        return redirect()->route('penerima.index')
            ->with('success', 'Data penerima berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PenerimaTemplateExport, 'template_penerima.xlsx');
    }

    public function importExcel(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048'
        ]);
        
        try {
            DB::beginTransaction();
            
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PenerimaImport, $request->file('file'));
            
            DB::commit();
            return back()->with('success', 'Berhasil mengimport data penerima.');
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Validasi gagal: ' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function generateCode()
    {
        // Cari nomor terakhir dari kode yang sudah ada dengan format MR
        // MR = Master Receiver? Or Just R? Or MP (Master Penerima)? 
        // Pengirim uses MP. Let's use MR for Penerima.
        $lastCode = Penerima::where('kode', 'like', 'MR%')
            ->orderBy('kode', 'desc')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode->kode, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'MR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
