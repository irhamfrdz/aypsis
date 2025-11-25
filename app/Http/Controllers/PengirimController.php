<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pengirim;
use Illuminate\Http\Request;

class PengirimController extends Controller
{
    /**
     * Download CSV template (header only, semicolon delimited)
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_pengirim.csv"',
        ];
        $csv = "kode;nama_pengirim;catatan;status\n";
        return response($csv, 200, $headers);
    }

    /**
     * Show import form
     */
    public function showImport()
    {
        return view('master-pengirim.import');
    }

    /**
     * Process CSV import
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $errors = [];
        $successCount = 0;

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file CSV.');
        }

        $header = fgetcsv($handle, 0, ';');
        $expectedHeader = ['kode', 'nama_pengirim', 'catatan', 'status'];
        if ($header !== $expectedHeader) {
            return back()->with('error', 'Header CSV tidak sesuai. Harus: kode;nama_pengirim;catatan;status');
        }

        $rowNum = 1;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowNum++;
            if (count($row) !== 4) {
                $errors[] = "Baris $rowNum: Jumlah kolom tidak sesuai.";
                continue;
            }
            [$kode, $nama_pengirim, $catatan, $status] = $row;
            $kode = trim($kode);
            $nama_pengirim = trim($nama_pengirim);
            $catatan = trim($catatan);
            $status = trim($status);

            // Skip jika nama_pengirim kosong
            if ($nama_pengirim === '') {
                $errors[] = "Baris $rowNum: Nama pengirim wajib diisi.";
                continue;
            }

            // Auto-generate kode jika kosong
            if ($kode === '') {
                $kode = $this->generatePengirimCode();
            }

            // Set status default ke 'active' jika kosong
            if ($status === '') {
                $status = 'active';
            }

            if (!in_array($status, ['active', 'inactive'])) {
                $errors[] = "Baris $rowNum: Status harus 'active' atau 'inactive'.";
                continue;
            }

            $pengirim = Pengirim::where('kode', $kode)->first();
            if ($pengirim) {
                $pengirim->nama_pengirim = $nama_pengirim;
                $pengirim->catatan = $catatan;
                $pengirim->status = $status;
                $pengirim->save();
            } else {
                Pengirim::create([
                    'kode' => $kode,
                    'nama_pengirim' => $nama_pengirim,
                    'catatan' => $catatan,
                    'status' => $status,
                ]);
            }
            $successCount++;
        }
        fclose($handle);

        if ($errors) {
            return back()->with('error', implode('<br>', $errors));
        }
        return back()->with('success', "Import selesai. $successCount data berhasil diproses.");
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengirim::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama_pengirim', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $pengirims = $query->paginate(15);

        return view('master-pengirim.index', compact('pengirims'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-pengirim.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:pengirim,kode',
            'nama_pengirim' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $pengirim = Pengirim::create($request->all());

        // Check if this is a popup request
        // Either has search parameter OR referer contains orders/create OR has popup parameter
        if ($request->query('search') ||
            $request->has('popup') ||
            ($request->header('referer') && strpos($request->header('referer'), 'orders/create') !== false) ||
            ($request->header('referer') && strpos($request->header('referer'), 'search') !== false)) {

            return view('master-pengirim.success', compact('pengirim'));
        }

        return redirect()->route('pengirim.index')->with('success', 'Pengirim berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengirim = Pengirim::findOrFail($id);
        return view('master-pengirim.show', compact('pengirim'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengirim = Pengirim::findOrFail($id);
        return view('master-pengirim.edit', compact('pengirim'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pengirim = Pengirim::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:pengirim,kode,' . $id,
            'nama_pengirim' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $pengirim->update($request->all());

        return redirect()->route('pengirim.index')->with('success', 'Pengirim berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengirim = Pengirim::findOrFail($id);
        $pengirim->delete();

        return redirect()->route('pengirim.index')->with('success', 'Data pengirim berhasil diimpor.');
    }

    private function generatePengirimCode()
    {
        // Cari nomor terakhir dari kode yang sudah ada dengan format MP
        $lastCode = Pengirim::where('kode', 'like', 'MP%')
            ->orderBy('kode', 'desc')
            ->first();

        if ($lastCode) {
            // Ambil angka dari kode terakhir (contoh: MP00005 -> 5)
            $lastNumber = (int) substr($lastCode->kode, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            // Jika belum ada kode MP, mulai dari 1
            $nextNumber = 1;
        }

        // Format: MP + 5 digit angka (MP00001)
        return 'MP' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Show the form for creating a new resource specifically for Order form.
     * This method doesn't require permissions.
     */
    public function createForOrder(Request $request)
    {
        // Pre-fill nama_pengirim if search parameter exists
        $searchValue = $request->query('search', '');
        
        return view('master-pengirim.create-for-order', compact('searchValue'));
    }

    /**
     * Store a newly created resource in storage specifically for Order form.
     * This method doesn't require permissions.
     */
    public function storeForOrder(Request $request)
    {
        // Handle code generation request
        if ($request->has('_generate_code_only')) {
            $code = $this->generatePengirimCode();
            return response()->json(['code' => $code]);
        }

        $request->validate([
            'kode' => 'required|string|unique:pengirims,kode',
            'nama_pengirim' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $pengirim = Pengirim::create($request->all());

        // Always return popup success view for order form
        return view('master-pengirim.success-for-order', compact('pengirim'));
    }
}
