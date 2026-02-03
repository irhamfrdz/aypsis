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
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama_penerima', 'LIKE', '%' . $searchTerm . '%')
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
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nitku' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
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
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nitku' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
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
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_penerima.csv"',
        ];

        $columns = ['Nama Penerima', 'Alamat', 'NPWP', 'NITKU', 'Catatan', 'Status (active/inactive)'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importExcel(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx|max:2048'
        ]);
        
        try {
            DB::beginTransaction();
            
            $file = $request->file('file');
            $data = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($data); // Remove header row

            // Validate header structure (basic check)
            if (count($header) < 6) {
                return back()->with('error', 'Format file CSV tidak sesuai template.');
            }
            
            $importedCount = 0;
            
            foreach ($data as $row) {
                // Skip empty rows
                if (empty($row[0])) continue;
                
                Penerima::create([
                    'nama_penerima' => $row[0],
                    'alamat' => $row[1] ?? null,
                    'npwp' => $row[2] ?? null,
                    'nitku' => $row[3] ?? null,
                    'catatan' => $row[4] ?? null,
                    'status' => in_array(strtolower($row[5] ?? ''), ['active', 'inactive']) ? strtolower($row[5]) : 'active',
                ]);
                
                $importedCount++;
            }
            
            DB::commit();
            return back()->with('success', "Berhasil mengimport {$importedCount} data penerima.");
            
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
