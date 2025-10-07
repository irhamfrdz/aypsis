<?php

namespace App\Http\Controllers;

use App\Models\PricelistCat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class MasterPricelistCatImportController extends Controller
{
    /**
     * Download template CSV untuk import master pricelist CAT
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'template_master_pricelist_cat_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers for CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            // CSV content dengan header dan contoh data
            $csvData = [
                ['Vendor', 'Jenis CAT', 'Ukuran Kontainer', 'Tarif'],
                ['CV ABC Workshop', 'cat_sebagian', '20ft', '500000'],
                ['CV ABC Workshop', 'cat_full', '20ft', '800000'],
                ['PT XYZ Paint Service', 'cat_sebagian', '40ft', '750000'],
                ['PT XYZ Paint Service', 'cat_full', '40ft', '1200000'],
                ['Bengkel DEF', 'cat_sebagian', '40ft HC', '800000']
            ];

            $callback = function() use ($csvData) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fputs($file, "\xEF\xBB\xBF");

                // Set CSV dengan semicolon delimiter
                foreach ($csvData as $row) {
                    fputcsv($file, $row, ';');
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mendownload template: ' . $e->getMessage());
        }
    }

    /**
     * Import data master pricelist CAT dari CSV
     */
    public function import(Request $request)
    {
        // Validasi file upload
        $validator = Validator::make($request->all(), [
            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120' // 5MB
            ]
        ], [
            'csv_file.required' => 'File CSV harus dipilih.',
            'csv_file.mimes' => 'File harus berformat .csv',
            'csv_file.max' => 'Ukuran file maksimal 5MB.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // Read CSV file with semicolon delimiter
            $csvData = [];
            if (($handle = fopen($path, 'r')) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                    $csvData[] = $data;
                }
                fclose($handle);
            }

            $header = array_shift($csvData); // Remove header row

            // Validate header format
            $expectedHeader = ['Vendor', 'Jenis CAT', 'Ukuran Kontainer', 'Tarif'];
            if ($header !== $expectedHeader) {
                return back()->with('error', 'Format header CSV tidak sesuai template. Gunakan template yang telah disediakan.');
            }

            $stats = [
                'success' => 0,
                'updated' => 0,
                'errors' => 0,
                'skipped' => 0,
                'error_details' => [],
                'warnings' => []
            ];

            DB::beginTransaction();

            foreach ($csvData as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // +2 because we removed header and start from 1

                // Skip empty rows atau contoh data
                if (empty(array_filter($row))) {
                    continue;
                }

                // Validate row has enough columns
                if (count($row) < 4) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: Data tidak lengkap";
                    continue;
                }

                try {
                    $vendor = trim($row[0]);
                    $jenisCat = trim($row[1]);
                    $ukuranKontainer = trim($row[2]);
                    $tarif = trim($row[3]);

                    // Validation
                    if (empty($vendor) || empty($jenisCat)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Vendor dan Jenis CAT tidak boleh kosong";
                        continue;
                    }

                    // Validate jenis CAT
                    $validJenisCat = ['cat_sebagian', 'cat_full'];
                    if (!in_array(strtolower($jenisCat), $validJenisCat)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Jenis CAT harus 'cat_sebagian' atau 'cat_full'";
                        continue;
                    }

                    // Validate ukuran kontainer
                    $validUkuran = ['20ft', '40ft', '40ft HC'];
                    if (!empty($ukuranKontainer) && !in_array($ukuranKontainer, $validUkuran)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Ukuran kontainer harus '20ft', '40ft', atau '40ft HC'";
                        continue;
                    }

                    // Validate tarif
                    if (!empty($tarif) && !is_numeric(str_replace(',', '', $tarif))) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Tarif harus berupa angka";
                        continue;
                    }

                    // Clean tarif value
                    $tarif = !empty($tarif) ? (float)str_replace(',', '', $tarif) : 0;

                    // Check if pricelist already exists (based on vendor, jenis_cat, ukuran_kontainer)
                    $existingPricelist = PricelistCat::where('vendor', $vendor)
                        ->where('jenis_cat', strtolower($jenisCat))
                        ->where('ukuran_kontainer', $ukuranKontainer)
                        ->first();

                    $data = [
                        'vendor' => $vendor,
                        'jenis_cat' => strtolower($jenisCat),
                        'ukuran_kontainer' => $ukuranKontainer,
                        'tarif' => $tarif,
                        'updated_by' => Auth::id(),
                        'updated_at' => now()
                    ];

                    if ($existingPricelist) {
                        // Update existing
                        $existingPricelist->update($data);
                        $stats['updated']++;
                    } else {
                        // Create new
                        $data['created_by'] = Auth::id();
                        $data['created_at'] = now();
                        PricelistCat::create($data);
                        $stats['success']++;
                    }

                } catch (\Exception $e) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Build success message
            $message = "Import berhasil! ";
            if ($stats['success'] > 0) {
                $message .= "Ditambahkan: {$stats['success']} pricelist, ";
            }
            if ($stats['updated'] > 0) {
                $message .= "Diperbarui: {$stats['updated']} pricelist, ";
            }
            if ($stats['errors'] > 0) {
                $message .= "Error: {$stats['errors']} baris. ";
                if (!empty($stats['error_details'])) {
                    $message .= "Error detail: " . implode(', ', array_slice($stats['error_details'], 0, 5));
                    if (count($stats['error_details']) > 5) {
                        $message .= " (dan " . (count($stats['error_details']) - 5) . " error lainnya)";
                    }
                }
            }

            return back()->with('success', trim($message, ', '));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}