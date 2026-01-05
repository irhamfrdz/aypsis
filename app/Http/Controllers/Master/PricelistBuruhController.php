<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PricelistBuruh;
use Illuminate\Http\Request;
use App\Exports\PricelistBuruhExport;
use App\Imports\PricelistBuruhImport;
use Maatwebsite\Excel\Facades\Excel;

class PricelistBuruhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistBuruh::query();

        // Search
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('barang', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $items = $query->orderBy('barang')->orderBy('size')->paginate(25);

        return view('master.pricelist-buruh.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-buruh.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'barang' => 'required|string|max:255',
                'size' => 'nullable|string|max:255',
                'tipe' => 'nullable|in:Full,Empty',
                'tarif' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ], [
                'barang.required' => 'Nama barang wajib diisi.',
                'tipe.in' => 'Tipe harus Full atau Empty.',
                'tarif.required' => 'Tarif wajib diisi.',
                'tarif.numeric' => 'Tarif harus berupa angka.',
                'tarif.min' => 'Tarif tidak boleh kurang dari 0.',
            ]);

            $data['is_active'] = $request->has('is_active');
            $data['created_by'] = auth()->id();

            PricelistBuruh::create($data);

            return redirect()->route('master.pricelist-buruh.index')->with('success', 'Pricelist buruh berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error saving pricelist buruh: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pricelist buruh: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistBuruh $pricelistBuruh)
    {
        return view('master.pricelist-buruh.show', ['item' => $pricelistBuruh]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistBuruh $pricelistBuruh)
    {
        return view('master.pricelist-buruh.edit', ['item' => $pricelistBuruh]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistBuruh $pricelistBuruh)
    {
        try {
            $data = $request->validate([
                'barang' => 'required|string|max:255',
                'size' => 'nullable|string|max:255',
                'tipe' => 'nullable|in:Full,Empty',
                'tarif' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ], [
                'barang.required' => 'Nama barang wajib diisi.',
                'tipe.in' => 'Tipe harus Full atau Empty.',
                'tarif.required' => 'Tarif wajib diisi.',
                'tarif.numeric' => 'Tarif harus berupa angka.',
                'tarif.min' => 'Tarif tidak boleh kurang dari 0.',
            ]);

            $data['is_active'] = $request->has('is_active');
            $data['updated_by'] = auth()->id();

            $pricelistBuruh->update($data);

            return redirect()->route('master.pricelist-buruh.index')->with('success', 'Pricelist buruh berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating pricelist buruh: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pricelist buruh: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistBuruh $pricelistBuruh)
    {
        try {
            $pricelistBuruh->delete();
            return redirect()->route('master.pricelist-buruh.index')->with('success', 'Pricelist buruh berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting pricelist buruh: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus pricelist buruh: ' . $e->getMessage());
        }
    }

    /**
     * Export pricelist buruh to Excel
     */
    public function export()
    {
        return Excel::download(new PricelistBuruhExport, 'pricelist-buruh-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Download template Excel for import
     */
    public function downloadTemplate()
    {
        $headers = [
            'Barang',
            'Size',
            'Tipe',
            'Tarif',
            'Status',
            'Keterangan',
        ];

        $sampleData = [
            ['Bongkar Muat', '20', 'Full', 150000, 'Aktif', 'Contoh data'],
            ['Bongkar Muat', '20', 'Empty', 100000, 'Aktif', ''],
            ['Stuffing', '40', 'Full', 200000, 'Aktif', ''],
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        // Add sample data
        $row = 2;
        foreach ($sampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template-pricelist-buruh.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import pricelist buruh from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls, csv).',
            'file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        try {
            Excel::import(new PricelistBuruhImport, $request->file('file'));

            return redirect()->route('master.pricelist-buruh.index')
                ->with('success', 'Data berhasil diimport dari Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()
                ->with('error', 'Gagal import data: ' . implode('<br>', $errors));
        } catch (\Exception $e) {
            \Log::error('Error importing pricelist buruh: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}
