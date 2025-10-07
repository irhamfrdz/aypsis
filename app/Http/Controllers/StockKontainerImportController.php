<?php

namespace App\Http\Controllers;

use App\Models\StockKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Response;

class StockKontainerImportController extends Controller
{
    /**
     * Download template CSV untuk import stock kontainer
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'template_stock_kontainer_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers for CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            // CSV content - hanya header template
            $csvData = [
                ['Nomor Kontainer', 'Ukuran', 'Tipe Kontainer', 'Status', 'Tahun Pembuatan', 'Keterangan']
            ];

            $callback = function() use ($csvData) {
                $file = fopen('php://output', 'w');

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
     * Import data stock kontainer dari CSV
     */
    public function import(Request $request)
    {
        // Validasi file upload
        $validator = Validator::make($request->all(), [
            'excel_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120' // 5MB
            ]
        ], [
            'excel_file.required' => 'File CSV harus dipilih.',
            'excel_file.mimes' => 'File harus berformat .csv',
            'excel_file.max' => 'Ukuran file maksimal 5MB.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('excel_file');
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
            $expectedHeader = ['Nomor Kontainer', 'Ukuran', 'Tipe Kontainer', 'Status', 'Tahun Pembuatan', 'Keterangan'];
            if ($header !== $expectedHeader) {
                return back()->with('error', 'Format header CSV tidak sesuai template. Gunakan template yang telah disediakan.');
            }

            $stats = [
                'success' => 0,
                'updated' => 0,
                'errors' => 0,
                'skipped' => 0,
                'error_details' => []
            ];

            DB::beginTransaction();

            foreach ($csvData as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // +2 because we removed header and start from 1

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Validate row has enough columns
                if (count($row) < 6) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: Data tidak lengkap";
                    continue;
                }

                try {
                    $nomorKontainer = trim($row[0]);
                    $ukuran = trim($row[1]);
                    $tipeKontainer = trim($row[2]);
                    $status = trim($row[3]);
                    $tahunPembuatan = trim($row[4]);
                    $keterangan = trim($row[5]);

                    // Validation
                    if (empty($nomorKontainer)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Nomor kontainer tidak boleh kosong";
                        continue;
                    }

                    // Validate status
                    $validStatuses = ['available', 'rented', 'maintenance', 'damaged'];
                    if (!empty($status) && !in_array(strtolower($status), $validStatuses)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Status harus salah satu dari: available, rented, maintenance, damaged";
                        continue;
                    }

                    // Validate tahun pembuatan (if provided)
                    if (!empty($tahunPembuatan) && (!is_numeric($tahunPembuatan) || $tahunPembuatan < 1900 || $tahunPembuatan > date('Y'))) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Tahun pembuatan harus berupa angka antara 1900 - " . date('Y');
                        continue;
                    }

                    // Check if stock kontainer already exists
                    $existingStock = StockKontainer::where('nomor_kontainer', $nomorKontainer)->first();

                    if ($existingStock) {
                        // Update existing
                        $existingStock->update([
                            'ukuran' => $ukuran ?: $existingStock->ukuran,
                            'tipe_kontainer' => $tipeKontainer ?: $existingStock->tipe_kontainer,
                            'status' => $status ? strtolower($status) : $existingStock->status,
                            'tahun_pembuatan' => $tahunPembuatan ?: $existingStock->tahun_pembuatan,
                            'keterangan' => $keterangan ?: $existingStock->keterangan,
                            'updated_at' => now()
                        ]);
                        $stats['updated']++;
                    } else {
                        // Create new
                        StockKontainer::create([
                            'nomor_kontainer' => $nomorKontainer,
                            'ukuran' => $ukuran,
                            'tipe_kontainer' => $tipeKontainer,
                            'status' => $status ? strtolower($status) : 'available',
                            'tahun_pembuatan' => $tahunPembuatan,
                            'keterangan' => $keterangan,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
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
                $message .= "Ditambahkan: {$stats['success']} stock kontainer, ";
            }
            if ($stats['updated'] > 0) {
                $message .= "Diperbarui: {$stats['updated']} stock kontainer, ";
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
