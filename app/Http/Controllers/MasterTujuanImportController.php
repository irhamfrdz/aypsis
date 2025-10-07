<?php

namespace App\Http\Controllers;

use App\Models\Tujuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;

class MasterTujuanImportController extends Controller
{
    /**
     * Download template CSV untuk import master tujuan
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'template_master_tujuan_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers for CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            // CSV content dengan header dan contoh data
            $csvData = [
                ['Cabang', 'Wilayah', 'Dari', 'Ke', 'Uang Jalan 20ft', 'Uang Jalan 40ft', 'Antar Lokasi 20ft', 'Antar Lokasi 40ft'],
                ['Jakarta', 'Jakarta Timur', 'Tanjung Priok', 'Bekasi', '150000', '200000', '100000', '150000'],
                ['Jakarta', 'Jakarta Barat', 'Soekarno Hatta', 'Tangerang', '175000', '225000', '125000', '175000'],
                ['Surabaya', 'Surabaya Utara', 'Tanjung Perak', 'Gresik', '120000', '180000', '80000', '120000']
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
     * Import data master tujuan dari CSV
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
            $expectedHeader = ['Cabang', 'Wilayah', 'Dari', 'Ke', 'Uang Jalan 20ft', 'Uang Jalan 40ft', 'Antar Lokasi 20ft', 'Antar Lokasi 40ft'];
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
                if (count($row) < 8) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: Data tidak lengkap";
                    continue;
                }

                try {
                    $cabang = trim($row[0]);
                    $wilayah = trim($row[1]);
                    $dari = trim($row[2]);
                    $ke = trim($row[3]);
                    $uangJalan20 = trim($row[4]);
                    $uangJalan40 = trim($row[5]);
                    $antar20 = trim($row[6]);
                    $antar40 = trim($row[7]);

                    // Validation
                    if (empty($cabang) || empty($wilayah) || empty($dari) || empty($ke)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Cabang, Wilayah, Dari, dan Ke tidak boleh kosong";
                        continue;
                    }

                    // Validate numeric fields
                    $numericFields = [
                        'Uang Jalan 20ft' => $uangJalan20,
                        'Uang Jalan 40ft' => $uangJalan40,
                        'Antar Lokasi 20ft' => $antar20,
                        'Antar Lokasi 40ft' => $antar40
                    ];

                    foreach ($numericFields as $fieldName => $value) {
                        if (!empty($value) && !is_numeric(str_replace(',', '', $value))) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: {$fieldName} harus berupa angka";
                            continue 2; // Skip this row
                        }
                    }

                    // Clean numeric values
                    $uangJalan20 = !empty($uangJalan20) ? (int)str_replace(',', '', $uangJalan20) : 0;
                    $uangJalan40 = !empty($uangJalan40) ? (int)str_replace(',', '', $uangJalan40) : 0;
                    $antar20 = !empty($antar20) ? (int)str_replace(',', '', $antar20) : 0;
                    $antar40 = !empty($antar40) ? (int)str_replace(',', '', $antar40) : 0;

                    // Check if tujuan already exists (based on cabang, wilayah, dari, ke)
                    $existingTujuan = Tujuan::where('cabang', $cabang)
                        ->where('wilayah', $wilayah)
                        ->where('dari', $dari)
                        ->where('ke', $ke)
                        ->first();

                    $data = [
                        'cabang' => $cabang,
                        'wilayah' => $wilayah,
                        'dari' => $dari,
                        'ke' => $ke,
                        'uang_jalan_20' => $uangJalan20,
                        'uang_jalan_40' => $uangJalan40,
                        'antar_20' => $antar20,
                        'antar_40' => $antar40,
                        'updated_at' => now()
                    ];

                    if ($existingTujuan) {
                        // Update existing
                        $existingTujuan->update($data);
                        $stats['updated']++;
                    } else {
                        // Create new
                        $data['created_at'] = now();
                        Tujuan::create($data);
                        $stats['success']++;
                    }

                } catch (Exception $e) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Build success message
            $message = "Import berhasil! ";
            if ($stats['success'] > 0) {
                $message .= "Ditambahkan: {$stats['success']} tujuan, ";
            }
            if ($stats['updated'] > 0) {
                $message .= "Diperbarui: {$stats['updated']} tujuan, ";
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

        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}