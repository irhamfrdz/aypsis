<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;

class MasterMobilImportController extends Controller
{
    /**
     * Download template CSV untuk import master mobil
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'template_master_mobil_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers for CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            // CSV content dengan header dan contoh data
            $csvData = [
                ['Aktiva', 'Plat', 'Nomor Rangka', 'Ukuran'],
                ['MOB001', 'B 1234 ABC', 'MH4GD1234A567890', '20ft'],
                ['MOB002', 'B 5678 DEF', 'MH4GD5678B123456', '40ft'],
                ['MOB003', 'D 9876 GHI', 'MH4GD9876C654321', '20ft']
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

        } catch (Exception $e) {
            return back()->with('error', 'Gagal mendownload template: ' . $e->getMessage());
        }
    }

    /**
     * Import data master mobil dari CSV
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
            $expectedHeader = ['Aktiva', 'Plat', 'Nomor Rangka', 'Ukuran'];
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
                    $aktiva = trim($row[0]);
                    $plat = trim($row[1]);
                    $nomorRangka = trim($row[2]);
                    $ukuran = trim($row[3]);

                    // Validation
                    if (empty($aktiva) || empty($plat)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Aktiva dan Plat tidak boleh kosong";
                        continue;
                    }

                    // Validate ukuran format
                    $validUkuran = ['20ft', '40ft', '45ft'];
                    if (!empty($ukuran) && !in_array($ukuran, $validUkuran)) {
                        $stats['warnings'][] = "Baris {$rowNumber}: Ukuran '{$ukuran}' tidak valid, menggunakan default '20ft'";
                        $ukuran = '20ft';
                    }

                    // Check if mobil already exists (based on aktiva or plat)
                    $existingMobil = Mobil::where('aktiva', $aktiva)
                        ->orWhere('plat', $plat)
                        ->first();

                    $data = [
                        'aktiva' => $aktiva,
                        'plat' => $plat,
                        'nomor_rangka' => $nomorRangka ?: null,
                        'ukuran' => $ukuran ?: '20ft',
                        'updated_at' => now()
                    ];

                    if ($existingMobil) {
                        // Update existing
                        $existingMobil->update($data);
                        $stats['updated']++;
                    } else {
                        // Create new
                        $data['created_at'] = now();
                        Mobil::create($data);
                        $stats['success']++;
                    }

                } catch (Exception $e) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Prepare success message
            $message = "Import selesai! ";
            if ($stats['success'] > 0) {
                $message .= "{$stats['success']} data baru berhasil ditambahkan. ";
            }
            if ($stats['updated'] > 0) {
                $message .= "{$stats['updated']} data berhasil diperbarui. ";
            }
            if ($stats['errors'] > 0) {
                $message .= "{$stats['errors']} data gagal diproses.";
            }

            $redirectResponse = back()->with('success', $message);

            // Add error details if any
            if (!empty($stats['error_details'])) {
                $redirectResponse = $redirectResponse->with('import_errors', $stats['error_details']);
            }

            // Add warnings if any
            if (!empty($stats['warnings'])) {
                $redirectResponse = $redirectResponse->with('import_warnings', $stats['warnings']);
            }

            return $redirectResponse;

        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }
}