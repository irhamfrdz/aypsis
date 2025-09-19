<?php

namespace App\Http\Controllers;

use App\Models\VendorBengkel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class VendorBengkelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = VendorBengkel::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_bengkel', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $vendorBengkel = $query->orderBy('nama_bengkel')->paginate(10);

        return view('master.vendor-bengkel.index', compact('vendorBengkel'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.vendor-bengkel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bengkel' => 'required|string|max:255|unique:vendor_bengkel,nama_bengkel',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        VendorBengkel::create($validated);

        return redirect()->route('master.vendor-bengkel.index')
            ->with('success', 'Vendor/Bengkel berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VendorBengkel $vendorBengkel): View
    {
        return view('master.vendor-bengkel.show', compact('vendorBengkel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VendorBengkel $vendorBengkel): View
    {
        return view('master.vendor-bengkel.edit', compact('vendorBengkel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VendorBengkel $vendorBengkel): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bengkel' => 'required|string|max:255|unique:vendor_bengkel,nama_bengkel,' . $vendorBengkel->id,
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $validated['updated_by'] = Auth::id();

        $vendorBengkel->update($validated);

        return redirect()->route('master.vendor-bengkel.index')
            ->with('success', 'Vendor/Bengkel berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VendorBengkel $vendorBengkel): RedirectResponse
    {
        // Check if vendor is being used in other tables
        // You might want to add relationship checks here

        $vendorBengkel->delete();

        return redirect()->route('master.vendor-bengkel.index')
            ->with('success', 'Vendor/Bengkel berhasil dihapus.');
    }

    /**
     * Export CSV template for vendor-bengkel.
     */
    public function exportTemplate()
    {
        $filename = 'template_vendor_bengkel_' . date('Y-m-d') . '.csv';

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
                'nama_bengkel',
                'keterangan'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import vendor-bengkel from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $data = array_map(function($line) {
            return str_getcsv($line, ';'); // Use semicolon as delimiter
        }, file($path));

        $header = array_shift($data); // Remove header row

        $successCount = 0;
        $errors = [];
        $rowNumber = 2; // Start from row 2 (after header)

        foreach ($data as $row) {
            try {
                if (count($row) < 1) { // Minimum required fields
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap";
                    $rowNumber++;
                    continue;
                }

                $vendorData = [
                    'nama_bengkel' => trim($row[0] ?? ''),
                    'keterangan' => trim($row[1] ?? ''),
                ];

                // Validate required fields
                if (empty($vendorData['nama_bengkel'])) {
                    $errors[] = "Baris {$rowNumber}: Nama bengkel wajib diisi";
                    $rowNumber++;
                    continue;
                }

                // Check for duplicates
                $existing = VendorBengkel::where('nama_bengkel', $vendorData['nama_bengkel'])->first();

                if ($existing) {
                    $errors[] = "Baris {$rowNumber}: Vendor/Bengkel dengan nama '{$vendorData['nama_bengkel']}' sudah ada";
                    $rowNumber++;
                    continue;
                }

                VendorBengkel::create($vendorData);
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

        return redirect()->route('master.vendor-bengkel.index')->with('success', $message);
    }
}
