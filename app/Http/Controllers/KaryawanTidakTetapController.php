<?php

namespace App\Http\Controllers;

use App\Models\KaryawanTidakTetap;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KaryawanTidakTetapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KaryawanTidakTetap::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%")
                  ->orWhere('divisi', 'LIKE', "%{$search}%")
                  ->orWhere('pekerjaan', 'LIKE', "%{$search}%");
            });
        }

        $karyawans = $query->latest()->paginate(15);

        return view('karyawan-tidak-tetap.index', compact('karyawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pekerjaans = \App\Models\Pekerjaan::all();
        $pajaks = \App\Models\Pajak::all();
        return view('karyawan-tidak-tetap.create', compact('pekerjaans', 'pajaks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:50|unique:karyawan_tidak_tetaps,nik',
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:100',
            'divisi' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'cabang' => 'nullable|string|max:100',
            'nik_ktp' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|string|max:20',
            'agama' => 'nullable|string|max:50',
            'rt_rw' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'tanggal_masuk' => 'nullable|date',
            'status_pajak' => 'nullable|string|max:50',
        ]);

        KaryawanTidakTetap::create($validated);

        return redirect()->route('karyawan-tidak-tetap.index')
            ->with('success', 'Data karyawan tidak tetap berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KaryawanTidakTetap $karyawanTidakTetap)
    {
        return view('karyawan-tidak-tetap.show', compact('karyawanTidakTetap'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KaryawanTidakTetap $karyawanTidakTetap)
    {
        return view('karyawan-tidak-tetap.edit', compact('karyawanTidakTetap'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KaryawanTidakTetap $karyawanTidakTetap)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:50|unique:karyawan_tidak_tetaps,nik,' . $karyawanTidakTetap->id,
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:100',
            'divisi' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'cabang' => 'nullable|string|max:100',
            'nik_ktp' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|string|max:20',
            'agama' => 'nullable|string|max:50',
            'rt_rw' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'tanggal_masuk' => 'nullable|date',
            'status_pajak' => 'nullable|string|max:50',
        ]);

        $karyawanTidakTetap->update($validated);

        return redirect()->route('karyawan-tidak-tetap.index')
            ->with('success', 'Data karyawan tidak tetap berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KaryawanTidakTetap $karyawanTidakTetap)
    {
        $karyawanTidakTetap->delete();

        return redirect()->route('karyawan-tidak-tetap.index')
            ->with('success', 'Data karyawan tidak tetap berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\KaryawanTidakTetapImport();
        $result = $import->import($request->file('file'));

        if ($result === false) {
            return back()->with('error', 'Gagal memproses file import.');
        }

        $message = "Berhasil mengimport {$result['success_count']} data.";
        
        if (!empty($result['errors'])) {
            $message .= " Namun terdapat " . count($result['errors']) . " error.";
            return redirect()->route('karyawan-tidak-tetap.index')
                ->with('success', $message)
                ->with('import_errors', $result['errors']);
        }

        return redirect()->route('karyawan-tidak-tetap.index')
            ->with('success', $message);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'NIK',
            'Nama Lengkap',
            'Nama Panggilan',
            'Divisi',
            'Pekerjaan',
            'Cabang',
            'NIK KTP',
            'Jenis Kelamin',
            'Agama',
            'RT/RW',
            'Alamat Lengkap',
            'Kelurahan',
            'Kecamatan',
            'Kabupaten',
            'Provinsi',
            'Kode Pos',
            'Email',
            'Tanggal Masuk (YYYY-MM-DD)',
            'Status Pajak'
        ];

        // Set Headers
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
            
            // Make header bold
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            
            // Auto size column
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add Example Row
        $exampleData = [
            '12345',
            'Budi Santoso',
            'Budi',
            'NON KARYAWAN',
            'Sopir',
            'JAKARTA',
            '1234567890123456',
            'Laki-laki',
            'Islam',
            '005/010',
            'Jl. Contoh No. 1',
            'Kelapa Gading',
            'Kelapa Gading',
            'Jakarta Utara',
            'DKI Jakarta',
            '14240',
            'budi@example.com',
            '2024-01-01',
            'TK/0'
        ];

        foreach ($exampleData as $index => $value) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '2', $value);
        }

        // Create Excel file in temp directory
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'template_import_karyawan_tidak_tetap.xlsx';
        $tempFile = sys_get_temp_dir() . '/' . $fileName;
        
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function printSingle(KaryawanTidakTetap $karyawanTidakTetap)
    {
        $karyawan = $karyawanTidakTetap;
        return view('karyawan-tidak-tetap.print-single', compact('karyawan'));
    }
}
