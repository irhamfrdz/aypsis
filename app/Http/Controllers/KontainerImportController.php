<?php

namespace App\Http\Controllers;

use App\Models\Kontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Response;

class KontainerImportController extends Controller
{
    /**
     * Download template CSV untuk import kontainer
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'template_master_kontainer_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers for CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            // CSV content - template dengan contoh data
            $csvData = [
                ['Awalan Kontainer (4 karakter)', 'Nomor Seri (6 digit)', 'Akhiran (1 karakter)', 'Ukuran', 'Vendor'],
                ['ALLU', '220209', '7', '20', 'PT. ZONA LINTAS SAMUDERA'],
                ['AMFU', '313132', '7', '20', 'PT. ZONA LINTAS SAMUDERA'],
                ['AMFU', '315369', '2', '20', 'PT. ZONA LINTAS SAMUDERA']
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
    }    /**
     * Import data kontainer dari CSV
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

            // Validate header format - dengan fleksibilitas untuk format yang berbeda
            $expectedHeaders = [
                ['Awalan Kontainer', 'Nomor Seri', 'Akhiran', 'Ukuran', 'Vendor'],
                ['Awalan Kontainer (4 karakter)', 'Nomor Seri (6 digit)', 'Akhiran (1 karakter)', 'Ukuran', 'Vendor']
            ];

            $headerValid = false;
            foreach ($expectedHeaders as $expectedHeader) {
                if ($header === $expectedHeader) {
                    $headerValid = true;
                    break;
                }
            }

            if (!$headerValid) {
                $headerReceived = implode(', ', $header);
                $headerExpected = implode(', ', $expectedHeaders[0]);

                $errorMessage = "Format header CSV tidak sesuai template.\n\n";
                $errorMessage .= "Header yang diterima: " . $headerReceived . "\n";
                $errorMessage .= "Header yang diharapkan: " . $headerExpected . "\n\n";
                $errorMessage .= "Silakan download template terbaru dan pastikan format header sesuai.";

                return back()->with('error', $errorMessage);
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

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Validate row has enough columns
                if (count($row) < 5) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: Data tidak lengkap";
                    continue;
                }

                try {
                    $awalanKontainer = trim($row[0]);
                    $nomorSeri = trim($row[1]);
                    $akhiranKontainer = trim($row[2]);
                    $ukuran = trim($row[3]);
                    $vendor = trim($row[4]);

                    // Validation
                    if (empty($awalanKontainer) || empty($nomorSeri) || empty($akhiranKontainer) || empty($ukuran) || empty($vendor)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Ada kolom yang kosong";
                        continue;
                    }

                    // Validate format
                    if (strlen($awalanKontainer) !== 4) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Awalan kontainer harus 4 karakter";
                        continue;
                    }

                    if (strlen($nomorSeri) !== 6) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Nomor seri harus 6 karakter";
                        continue;
                    }

                    if (strlen($akhiranKontainer) !== 1) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Akhiran kontainer harus 1 karakter";
                        continue;
                    }

                    // Validate ukuran
                    if (!in_array($ukuran, ['20', '40'])) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Ukuran harus 20 atau 40";
                        continue;
                    }

                    // Generate nomor seri gabungan
                    $nomorSeriGabungan = $awalanKontainer . $nomorSeri . $akhiranKontainer;

                    // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer
                    $existingWithSameSerialAndSuffix = Kontainer::where('nomor_seri_kontainer', $nomorSeri)
                        ->where('akhiran_kontainer', $akhiranKontainer)
                        ->where('status', 'active')
                        ->first();

                    if ($existingWithSameSerialAndSuffix) {
                        // Set kontainer yang sudah ada ke inactive
                        $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);

                        $stats['warnings'][] = "Baris {$rowNumber}: Kontainer dengan nomor seri {$nomorSeri} dan akhiran {$akhiranKontainer} sudah ada. Kontainer lama telah dinonaktifkan.";
                    }

                    // Check if kontainer already exists (berdasarkan nomor seri gabungan)
                    $existingKontainer = Kontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)->first();

                    if ($existingKontainer) {
                        // Update existing
                        $existingKontainer->update([
                            'awalan_kontainer' => $awalanKontainer,
                            'nomor_seri_kontainer' => $nomorSeri,
                            'akhiran_kontainer' => $akhiranKontainer,
                            'nomor_seri_gabungan' => $nomorSeriGabungan,
                            'ukuran' => $ukuran,
                            'vendor' => $vendor,
                            'tipe_kontainer' => 'Dry Container', // Default
                            'status' => 'Tersedia',
                            'updated_at' => now()
                        ]);
                        $stats['updated']++;
                    } else {
                        // Create new
                        Kontainer::create([
                            'awalan_kontainer' => $awalanKontainer,
                            'nomor_seri_kontainer' => $nomorSeri,
                            'akhiran_kontainer' => $akhiranKontainer,
                            'nomor_seri_gabungan' => $nomorSeriGabungan,
                            'ukuran' => $ukuran,
                            'vendor' => $vendor,
                            'tipe_kontainer' => 'Dry Container', // Default value
                            'status' => 'Tersedia', // Default value - akan divalidasi oleh model jika ada konflik
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
            $message .= "Ditambahkan: {$stats['success']} kontainer, ";
            $message .= "Diperbarui: {$stats['updated']} kontainer";

            if ($stats['errors'] > 0) {
                $message .= ", Error: {$stats['errors']} baris";
            }

            // Show detailed errors if any
            if (!empty($stats['error_details'])) {
                $errorDetails = implode(', ', array_slice($stats['error_details'], 0, 3));
                if (count($stats['error_details']) > 3) {
                    $errorDetails .= ' dan ' . (count($stats['error_details']) - 3) . ' error lainnya';
                }
                $message .= ". Error detail: " . $errorDetails;
            }

            // Show warnings for duplicate serial+suffix
            if (!empty($stats['warnings'])) {
                session()->flash('warning', implode(' ', $stats['warnings']));
            }

            return redirect()->route('master.kontainer.index')->with('success', $message);

        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Export data kontainer ke CSV
     */
    public function export(Request $request)
    {
        try {
            // Get kontainer data with filters (same as index page)
            $query = Kontainer::query();

            // Apply filters
            if ($request->filled('search')) {
                $query->where('nomor_seri_gabungan', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('vendor')) {
                $query->where('vendor', $request->vendor);
            }

            if ($request->filled('ukuran')) {
                $query->where('ukuran', $request->ukuran);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Get all matching records (no pagination for export)
            $kontainers = $query->orderBy('nomor_seri_gabungan')->get();

            // Generate filename with timestamp and filter info
            $filename = 'export_master_kontainer_' . date('Y-m-d_H-i-s');
            
            // Add filter info to filename
            $filterInfo = [];
            if ($request->filled('search')) {
                $filterInfo[] = 'search-' . str_replace(' ', '-', substr($request->search, 0, 10));
            }
            if ($request->filled('vendor')) {
                $filterInfo[] = 'vendor-' . str_replace([' ', '.'], '-', substr($request->vendor, 0, 15));
            }
            if ($request->filled('ukuran')) {
                $filterInfo[] = 'ukuran-' . $request->ukuran . 'ft';
            }
            if ($request->filled('status')) {
                $filterInfo[] = 'status-' . strtolower($request->status);
            }

            if (!empty($filterInfo)) {
                $filename .= '_' . implode('_', $filterInfo);
            }

            $filename .= '.csv';

            // Headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($kontainers, $request) {
                $file = fopen('php://output', 'w');

                // Add BOM for proper Excel UTF-8 handling
                fputs($file, "\xEF\xBB\xBF");

                // CSV Headers
                $csvHeaders = [
                    'No',
                    'Nomor Kontainer',
                    'Awalan',
                    'Nomor Seri',
                    'Akhiran',
                    'Ukuran',
                    'Tipe',
                    'Vendor',
                    'Status',
                    'Tanggal Dibuat',
                    'Tanggal Diperbarui'
                ];

                // Write headers
                fputcsv($file, $csvHeaders, ';');

                // Write data
                foreach ($kontainers as $index => $kontainer) {
                    // Format status untuk display
                    $displayStatus = 'Tersedia'; // Default

                    if (in_array($kontainer->status, ['Disewa', 'Digunakan', 'rented'])) {
                        $displayStatus = 'Disewa';
                    } elseif ($kontainer->status === 'inactive') {
                        $displayStatus = 'Nonaktif';
                    }

                    $rowData = [
                        $index + 1,
                        $kontainer->nomor_seri_gabungan ?? '-',
                        $kontainer->awalan_kontainer ?? '-',
                        $kontainer->nomor_seri ?? '-',
                        $kontainer->akhiran ?? '-',
                        $kontainer->ukuran ?? '-',
                        $kontainer->tipe_kontainer ?? '-',
                        $kontainer->vendor ?? '-',
                        $displayStatus,
                        $kontainer->created_at ? $kontainer->created_at->format('d-m-Y H:i:s') : '-',
                        $kontainer->updated_at ? $kontainer->updated_at->format('d-m-Y H:i:s') : '-'
                    ];

                    fputcsv($file, $rowData, ';');
                }

                // Add export summary at the end
                fputcsv($file, [], ';'); // Empty row
                fputcsv($file, ['=== INFORMASI EXPORT ==='], ';');
                fputcsv($file, ['Total Data', count($kontainers) . ' kontainer'], ';');
                fputcsv($file, ['Tanggal Export', date('d-m-Y H:i:s')], ';');
                fputcsv($file, ['Exported By', auth()->user()->username ?? 'System'], ';');

                // Add filter information if any
                if ($request->filled('search') || $request->filled('vendor') || 
                    $request->filled('ukuran') || $request->filled('status')) {
                    
                    fputcsv($file, [], ';'); // Empty row
                    fputcsv($file, ['=== FILTER YANG DITERAPKAN ==='], ';');
                    
                    if ($request->filled('search')) {
                        fputcsv($file, ['Pencarian', $request->search], ';');
                    }
                    if ($request->filled('vendor')) {
                        fputcsv($file, ['Vendor', $request->vendor], ';');
                    }
                    if ($request->filled('ukuran')) {
                        fputcsv($file, ['Ukuran', $request->ukuran . ' ft'], ';');
                    }
                    if ($request->filled('status')) {
                        fputcsv($file, ['Status', $request->status], ';');
                    }
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);

        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}
