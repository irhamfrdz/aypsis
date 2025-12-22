<?php

namespace App\Http\Controllers;

use App\Models\PricelistRit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class MasterPricelistRitImportController extends Controller
{
    /**
     * Download template CSV untuk import master pricelist Rit
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'template_master_pricelist_rit_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers for CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            // CSV content dengan header dan contoh data
            $csvData = [
                ['Tujuan', 'Tarif', 'Status', 'Keterangan'],
                ['Supir', '500000', 'Aktif', 'Tarif uang rit supir'],
                ['Kenek', '300000', 'Aktif', 'Tarif uang rit kenek']
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
     * Import data master pricelist Rit dari CSV
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
            $expectedHeader = ['Tujuan', 'Tarif', 'Status', 'Keterangan'];
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
                if (count($row) < 3) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: Data tidak lengkap (minimal Tujuan, Tarif, dan Status)";
                    continue;
                }

                try {
                    $tujuan = trim($row[0]);
                    $tarif = trim($row[1]);
                    $status = isset($row[2]) ? trim($row[2]) : 'Aktif';
                    $keterangan = isset($row[3]) ? trim($row[3]) : '';

                    // Validation
                    if (empty($tujuan)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Tujuan tidak boleh kosong";
                        continue;
                    }

                    // Validate tujuan
                    $validTujuan = ['Supir', 'Kenek'];
                    if (!in_array($tujuan, $validTujuan)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Tujuan harus 'Supir' atau 'Kenek'";
                        continue;
                    }

                    if (empty($tarif)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Tarif tidak boleh kosong";
                        continue;
                    }

                    // Validate status
                    $validStatus = ['Aktif', 'Tidak Aktif'];
                    if (!in_array($status, $validStatus)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Status harus 'Aktif' atau 'Tidak Aktif'";
                        continue;
                    }

                    // Validate tarif
                    if (!is_numeric(str_replace(',', '', $tarif))) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Tarif harus berupa angka";
                        continue;
                    }

                    // Clean tarif value
                    $tarif = (float)str_replace(',', '', $tarif);

                    // Check if pricelist already exists (based on tujuan)
                    $existingPricelist = PricelistRit::where('tujuan', $tujuan)->first();

                    $data = [
                        'tujuan' => $tujuan,
                        'tarif' => $tarif,
                        'status' => $status,
                        'keterangan' => $keterangan,
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
                        PricelistRit::create($data);
                        $stats['success']++;
                    }

                } catch (\Exception $e) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Prepare summary message
            $message = "Import selesai! ";
            $message .= "Berhasil: {$stats['success']}, ";
            $message .= "Diupdate: {$stats['updated']}, ";
            $message .= "Error: {$stats['errors']}";

            if (!empty($stats['error_details'])) {
                $message .= "<br><strong>Detail Error:</strong><br>" . implode('<br>', array_slice($stats['error_details'], 0, 10));
                if (count($stats['error_details']) > 10) {
                    $message .= "<br>... dan " . (count($stats['error_details']) - 10) . " error lainnya";
                }
            }

            if ($stats['errors'] > 0) {
                return back()->with('warning', $message);
            } else {
                return redirect()->route('master.pricelist-rit.index')->with('success', $message);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }
}
