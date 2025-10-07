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

            // CSV content - header template dengan format yang jelas
            $csvData = [
                ['Awalan Kontainer (4 karakter)', 'Nomor Seri Kontainer (6 digit)', 'Akhiran Kontainer (1 digit)', 'Nomor Seri Gabungan (11 karakter)', 'Ukuran', 'Tipe Kontainer', 'Status', 'Tahun Pembuatan', 'Keterangan'],
                ['ABCD', '123456', 'X', 'ABCD123456X', '20', 'Dry Container', 'available', '2020', 'Contoh data - hapus baris ini'],
                ['EFGH', '789012', 'Y', 'EFGH789012Y', '40', 'Reefer Container', 'rented', '2021', 'Contoh data - hapus baris ini']
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

            // Validate header format - terima format lama dan baru
            $expectedHeaders = [
                // Format lama
                ['Nomor Kontainer', 'Ukuran', 'Tipe Kontainer', 'Status', 'Tahun Pembuatan', 'Keterangan'],
                ['Nomor Kontainer (11 karakter, contoh: ABCD123456X)', 'Ukuran', 'Tipe Kontainer', 'Status', 'Tahun Pembuatan', 'Keterangan'],
                // Format baru
                ['Awalan Kontainer (4 karakter)', 'Nomor Seri Kontainer (6 digit)', 'Akhiran Kontainer (1 digit)', 'Nomor Seri Gabungan (11 karakter)', 'Ukuran', 'Tipe Kontainer', 'Status', 'Tahun Pembuatan', 'Keterangan']
            ];
            
            $headerValid = false;
            $isNewFormat = false;
            foreach ($expectedHeaders as $index => $expectedHeader) {
                if ($header === $expectedHeader) {
                    $headerValid = true;
                    $isNewFormat = ($index === 2); // format baru (index 2)
                    break;
                }
            }
            
            if (!$headerValid) {
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
                if (empty(array_filter($row)) || 
                    (isset($row[8]) && strpos(strtolower($row[8]), 'contoh data') !== false) ||
                    (isset($row[5]) && strpos(strtolower($row[5]), 'contoh data') !== false)) {
                    continue;
                }

                // Validate row has enough columns
                $minColumns = $isNewFormat ? 9 : 6;
                if (count($row) < $minColumns) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: Data tidak lengkap";
                    continue;
                }

                try {
                    if ($isNewFormat) {
                        // Format baru: Awalan, Nomor Seri, Akhiran, Nomor Gabungan, Ukuran, Tipe, Status, Tahun, Keterangan
                        $awalan = trim($row[0]);
                        $nomor_seri = trim($row[1]);
                        $akhiran = trim($row[2]);
                        $nomorKontainer = trim($row[3]);
                        $ukuran = trim($row[4]);
                        $tipeKontainer = trim($row[5]);
                        $status = trim($row[6]);
                        $tahunPembuatan = trim($row[7]);
                        $keterangan = trim($row[8]);

                        // Validasi komponen kontainer
                        if (strlen($awalan) != 4) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Awalan kontainer harus 4 karakter";
                            continue;
                        }
                        if (strlen($nomor_seri) != 6 || !is_numeric($nomor_seri)) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Nomor seri kontainer harus 6 digit angka";
                            continue;
                        }
                        if (strlen($akhiran) != 1) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Akhiran kontainer harus 1 karakter";
                            continue;
                        }

                        // Validasi konsistensi nomor gabungan
                        $expectedGabungan = $awalan . $nomor_seri . $akhiran;
                        if ($nomorKontainer !== $expectedGabungan) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Nomor gabungan ({$nomorKontainer}) tidak sesuai dengan komponen ({$expectedGabungan})";
                            continue;
                        }
                    } else {
                        // Format lama: Nomor Kontainer, Ukuran, Tipe, Status, Tahun, Keterangan  
                        $nomorKontainer = trim($row[0]);
                        $ukuran = trim($row[1]);
                        $tipeKontainer = trim($row[2]);
                        $status = trim($row[3]);
                        $tahunPembuatan = trim($row[4]);
                        $keterangan = trim($row[5]);

                        // Validasi format nomor kontainer (harus 11 karakter)
                        if (strlen($nomorKontainer) != 11) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Nomor kontainer harus 11 karakter (format: ABCD123456X)";
                            continue;
                        }

                        // Parse nomor kontainer
                        $awalan = substr($nomorKontainer, 0, 4);
                        $nomor_seri = substr($nomorKontainer, 4, 6);
                        $akhiran = substr($nomorKontainer, 10, 1);
                    }

                    // Validasi umum setelah parsing
                    if (empty($nomorKontainer)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Nomor kontainer tidak boleh kosong";
                        continue;
                    }

                    // Validasi format nomor gabungan (harus 11 karakter)
                    if (strlen($nomorKontainer) != 11) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Nomor gabungan kontainer harus 11 karakter";
                        continue;
                    }

                    // Validate status
                    $validStatuses = ['available', 'rented', 'maintenance', 'damaged', 'inactive'];
                    if (!empty($status) && !in_array(strtolower($status), $validStatuses)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Status harus salah satu dari: available, rented, maintenance, damaged, inactive";
                        continue;
                    }

                    // Validate tahun pembuatan (if provided)
                    if (!empty($tahunPembuatan) && (!is_numeric($tahunPembuatan) || $tahunPembuatan < 1900 || $tahunPembuatan > date('Y'))) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Tahun pembuatan harus berupa angka antara 1900 - " . date('Y');
                        continue;
                    }

                    // Check if stock kontainer already exists
                    $existingStock = StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();

                    if ($existingStock) {
                        // Update existing
                        $updateData = [
                            'awalan_kontainer' => $awalan,
                            'nomor_seri_kontainer' => $nomor_seri,
                            'akhiran_kontainer' => $akhiran,
                            'nomor_seri_gabungan' => $nomorKontainer,
                            'ukuran' => $ukuran ?: $existingStock->ukuran,
                            'tipe_kontainer' => $tipeKontainer ?: $existingStock->tipe_kontainer,
                            'tahun_pembuatan' => $tahunPembuatan ?: $existingStock->tahun_pembuatan,
                            'keterangan' => $keterangan ?: $existingStock->keterangan,
                            'updated_at' => now()
                        ];

                        // Cek duplikasi dengan tabel kontainers
                        $existingKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
                        if ($existingKontainer) {
                            $updateData['status'] = 'inactive';
                        } else {
                            $updateData['status'] = $status ? strtolower($status) : $existingStock->status;
                        }

                        $existingStock->update($updateData);
                        $stats['updated']++;
                    } else {
                        // Create new
                        $createData = [
                            'awalan_kontainer' => $awalan,
                            'nomor_seri_kontainer' => $nomor_seri,
                            'akhiran_kontainer' => $akhiran,
                            'nomor_seri_gabungan' => $nomorKontainer,
                            'ukuran' => $ukuran,
                            'tipe_kontainer' => $tipeKontainer,
                            'tahun_pembuatan' => $tahunPembuatan,
                            'keterangan' => $keterangan,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        // Cek duplikasi dengan tabel kontainers
                        $existingKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
                        if ($existingKontainer) {
                            $createData['status'] = 'inactive';
                            $stats['warnings'][] = "Nomor kontainer {$nomorKontainer} sudah ada di master kontainer, status diset inactive";
                        } else {
                            $createData['status'] = $status ? strtolower($status) : 'available';
                        }

                        StockKontainer::create($createData);
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
