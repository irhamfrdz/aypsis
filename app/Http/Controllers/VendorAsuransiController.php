<?php

namespace App\Http\Controllers;

use App\Models\VendorAsuransi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class VendorAsuransiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = VendorAsuransi::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_asuransi', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $vendorAsuransi = $query->orderBy('nama_asuransi')->paginate(10);

        return view('master.vendor-asuransi.index', compact('vendorAsuransi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.vendor-asuransi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'nullable|string|max:50|unique:vendor_asuransi,kode',
            'nama_asuransi' => 'required|string|max:255|unique:vendor_asuransi,nama_asuransi',
            'alamat' => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        VendorAsuransi::create($validated);

        return redirect()->route('master.vendor-asuransi.index')
            ->with('success', 'Vendor Asuransi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VendorAsuransi $vendorAsuransi): View
    {
        return view('master.vendor-asuransi.show', compact('vendorAsuransi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VendorAsuransi $vendorAsuransi): View
    {
        return view('master.vendor-asuransi.edit', compact('vendorAsuransi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VendorAsuransi $vendorAsuransi): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'nullable|string|max:50|unique:vendor_asuransi,kode,' . $vendorAsuransi->id,
            'nama_asuransi' => 'required|string|max:255|unique:vendor_asuransi,nama_asuransi,' . $vendorAsuransi->id,
            'alamat' => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string'
        ]);

        $validated['updated_by'] = Auth::id();

        $vendorAsuransi->update($validated);

        return redirect()->route('master.vendor-asuransi.index')
            ->with('success', 'Vendor Asuransi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VendorAsuransi $vendorAsuransi): RedirectResponse
    {
        $vendorAsuransi->delete();

        return redirect()->route('master.vendor-asuransi.index')
            ->with('success', 'Vendor Asuransi berhasil dihapus.');
    }

    /**
     * Export CSV template for vendor-asuransi.
     */
    public function exportTemplate()
    {
        $filename = 'template_vendor_asuransi_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Header row with semicolon delimiter
            fputcsv($file, [
                'kode',
                'nama_asuransi',
                'alamat',
                'telepon',
                'email',
                'keterangan',
                'catatan'
            ], ';');

            // Sample data
            fputcsv($file, [
                'ASN001',
                'Asuransi Central Asia',
                'Jl. Sudirman No. 1, Jakarta',
                '021-1234567',
                'info@aca.co.id',
                'Vendor asuransi utama',
                'Kontak person: Pak Budi'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import vendor-asuransi from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($path));

        $header = array_shift($data);

        $successCount = 0;
        $errors = [];
        $rowNumber = 2;

        foreach ($data as $row) {
            try {
                if (count($row) < 2) {
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap";
                    $rowNumber++;
                    continue;
                }

                $vendorData = [
                    'kode' => trim($row[0] ?? ''),
                    'nama_asuransi' => trim($row[1] ?? ''),
                    'alamat' => trim($row[2] ?? ''),
                    'telepon' => trim($row[3] ?? ''),
                    'email' => trim($row[4] ?? ''),
                    'keterangan' => trim($row[5] ?? ''),
                    'catatan' => trim($row[6] ?? ''),
                ];

                // Clean empty values to null
                foreach ($vendorData as $key => $value) {
                    if (empty($value)) {
                        $vendorData[$key] = null;
                    }
                }

                if (empty($vendorData['nama_asuransi'])) {
                    $errors[] = "Baris {$rowNumber}: Nama asuransi wajib diisi";
                    $rowNumber++;
                    continue;
                }

                $existing = VendorAsuransi::where('nama_asuransi', $vendorData['nama_asuransi'])->first();
                if ($existing) {
                    $errors[] = "Baris {$rowNumber}: Vendor Asuransi dengan nama '{$vendorData['nama_asuransi']}' sudah ada";
                    $rowNumber++;
                    continue;
                }

                if (!empty($vendorData['kode'])) {
                    $existingKode = VendorAsuransi::where('kode', $vendorData['kode'])->first();
                    if ($existingKode) {
                        $errors[] = "Baris {$rowNumber}: Kode '{$vendorData['kode']}' sudah digunakan";
                        $rowNumber++;
                        continue;
                    }
                }

                $vendorData['created_by'] = Auth::id();
                $vendorData['updated_by'] = Auth::id();

                VendorAsuransi::create($vendorData);
                $successCount++;
                $rowNumber++;

            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $rowNumber++;
            }
        }

        $message = "Import selesai. {$successCount} data berhasil diimpor.";
        if (!empty($errors)) {
            $message .= " Terdapat " . count($errors) . " error(s).";
            session()->flash('import_errors', $errors);
        }

        return redirect()->route('master.vendor-asuransi.index')->with('success', $message);
    }
}
