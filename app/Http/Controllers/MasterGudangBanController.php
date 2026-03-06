<?php

namespace App\Http\Controllers;

use App\Models\MasterGudangBan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MasterGudangBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterGudangBan::query();

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

        return view('master-gudang-ban.index', compact('gudangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-gudang-ban.create');
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

        $validated['created_by'] = auth()->id();

        MasterGudangBan::create($validated);

        return redirect()->route('master-gudang-ban.index')
            ->with('success', 'Data gudang ban berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $masterGudangBan = MasterGudangBan::findOrFail($id);
        return view('master-gudang-ban.show', compact('masterGudangBan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $masterGudangBan = MasterGudangBan::findOrFail($id);
        return view('master-gudang-ban.edit', compact('masterGudangBan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $masterGudangBan = MasterGudangBan::findOrFail($id);
        
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $validated['updated_by'] = auth()->id();

        $masterGudangBan->update($validated);

        return redirect()->route('master-gudang-ban.index')
            ->with('success', 'Data gudang ban berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $masterGudangBan = MasterGudangBan::findOrFail($id);
        $masterGudangBan->delete();

        return redirect()->route('master-gudang-ban.index')
            ->with('success', 'Data gudang ban berhasil dihapus');
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
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $imported = 0;
            $errors = [];

            foreach (array_slice($rows, 1) as $index => $row) {
                // Skip empty rows
                if (empty($row[0])) {
                    continue;
                }

                try {
                    MasterGudangBan::create([
                        'nama_gudang' => $row[0] ?? '',
                        'lokasi' => $row[1] ?? '',
                        'keterangan' => $row[2] ?? null,
                        'status' => in_array(strtolower($row[3] ?? ''), ['aktif', 'nonaktif']) 
                                    ? strtolower($row[3]) 
                                    : 'aktif',
                        'created_by' => auth()->id()
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            if (count($errors) > 0) {
                return redirect()->route('master-gudang-ban.index')
                    ->with('success', "{$imported} data berhasil diimport")
                    ->with('error', "Beberapa data gagal: " . implode(", ", array_slice($errors, 0, 3)));
            }

            return redirect()->route('master-gudang-ban.index')
                ->with('success', "{$imported} data gudang ban berhasil diimport");

        } catch (\Exception $e) {
            return redirect()->route('master-gudang-ban.index')
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Nama Gudang');
        $sheet->setCellValue('B1', 'Lokasi');
        $sheet->setCellValue('C1', 'Keterangan');
        $sheet->setCellValue('D1', 'Status');

        // Add example data
        $sheet->setCellValue('A2', 'Ruko 10');
        $sheet->setCellValue('B2', 'Batam');
        $sheet->setCellValue('C2', 'Gudang ban utama');
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
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_master_gudang_ban.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
