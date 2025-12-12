<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class MasterGudangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Gudang::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_gudang', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $gudangs = $query->orderBy('nama_gudang', 'asc')->paginate(15);

        return view('master-gudang.index', compact('gudangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-gudang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        Gudang::create($validated);

        return redirect()->route('master-gudang.index')
            ->with('success', 'Data gudang berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gudang $masterGudang)
    {
        return view('master-gudang.show', compact('masterGudang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gudang $masterGudang)
    {
        return view('master-gudang.edit', compact('masterGudang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gudang $masterGudang)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $masterGudang->update($validated);

        return redirect()->route('master-gudang.index')
            ->with('success', 'Data gudang berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gudang $masterGudang)
    {
        $masterGudang->delete();

        return redirect()->route('master-gudang.index')
            ->with('success', 'Data gudang berhasil dihapus');
    }

    /**
     * Import data from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $imported = 0;
            $errors = [];

            foreach (array_slice($rows, 1) as $index => $row) {
                // Skip empty rows
                if (empty($row[0]) && empty($row[1])) {
                    continue;
                }

                try {
                    Gudang::create([
                        'nama_gudang' => $row[0] ?? '',
                        'lokasi' => $row[1] ?? '',
                        'keterangan' => $row[2] ?? null,
                        'status' => in_array(strtolower($row[3] ?? ''), ['aktif', 'nonaktif']) 
                                    ? strtolower($row[3]) 
                                    : 'aktif',
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            if (count($errors) > 0) {
                return redirect()->route('master-gudang.index')
                    ->with('success', "{$imported} data berhasil diimport")
                    ->with('error', "Beberapa data gagal: " . implode(", ", array_slice($errors, 0, 3)));
            }

            return redirect()->route('master-gudang.index')
                ->with('success', "{$imported} data gudang berhasil diimport");

        } catch (\Exception $e) {
            return redirect()->route('master-gudang.index')
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template
     */
    public function template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Nama Gudang');
        $sheet->setCellValue('B1', 'Lokasi');
        $sheet->setCellValue('C1', 'Keterangan');
        $sheet->setCellValue('D1', 'Status');

        // Add example data
        $sheet->setCellValue('A2', 'Gudang A');
        $sheet->setCellValue('B2', 'Jakarta');
        $sheet->setCellValue('C2', 'Gudang utama');
        $sheet->setCellValue('D2', 'aktif');

        // Style headers
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2EFDA');

        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_master_gudang.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
