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

            // CSV content - template dengan contoh data (basic + extended)
            $csvData = [
                // Header row with all available fields
                [
                    'Awalan Kontainer (4 karakter)', 
                    'Nomor Seri (maks 6 digit)', 
                    'Akhiran (1 karakter)', 
                    'Ukuran (10/20/40)', 
                    'Vendor (ZONA/DPE)',
                    'Tipe Kontainer (opsional)',
                    'Tanggal Mulai Sewa (dd/mmm/yyyy)',
                    'Tanggal Selesai Sewa (dd/mmm/yyyy)',
                    'Keterangan (opsional)',
                    'Status (Tersedia/Tidak Tersedia)'
                ],
                // Example row 1 - Basic format
                ['ALLU', '220209', '7', '20', 'ZONA', '', '', '', '', ''],
                // Example row 2 - With optional fields
                ['AMFU', '313132', '7', '20', 'ZONA', 'Dry Container', '01/Jan/2024', '31/Des/2024', 'Kontainer sewa tahunan', 'Tersedia'],
                // Example row 3 - Minimal format
                ['AMFU', '315369', '2', '40', 'DPE', '', '', '', '', ''],
                // Info rows
                [''],
                ['=== INFORMASI TEMPLATE ==='],
                ['Kolom 1-5: WAJIB diisi'],
                ['Kolom 6-10: OPSIONAL (boleh kosong)'],
                ['Ukuran: 10, 20, atau 40'],
                ['Vendor: ZONA atau DPE'],
                ['Tanggal: format dd/mmm/yyyy (contoh: 15/Jan/2024)'],
                ['Status default: "Tersedia" jika kosong'],
                ['Tipe default: "Dry Container" jika kosong']
            ];

            $callback = function() use ($csvData) {
                $file = fopen('php://output', 'w');

                // Add BOM for proper Excel UTF-8 handling
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

            // Read CSV file with semicolon delimiter and proper encoding
            $csvData = [];
            if (($handle = fopen($path, 'r')) !== FALSE) {
                // Set UTF-8 encoding untuk handle karakter khusus
                stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-8//IGNORE');
                
                while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                    // Clean up each field dari whitespace dan karakter tersembunyi
                    $cleanData = array_map(function($field) {
                        return trim($field, " \t\n\r\0\x0B\xEF\xBB\xBF");
                    }, $data);
                    $csvData[] = $cleanData;
                }
                fclose($handle);
            }
            
            // Log total baris yang dibaca
            \Log::info("CSV Import: Total rows read", ['total_rows' => count($csvData)]);

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
                    // Assign dengan null coalescing untuk menghindari undefined index
                    $awalanKontainer = isset($row[0]) ? trim($row[0]) : '';
                    $nomorSeri = isset($row[1]) ? trim($row[1]) : '';
                    $akhiranKontainer = isset($row[2]) ? trim($row[2]) : '';
                    $ukuran = isset($row[3]) ? trim($row[3]) : '';
                    $vendor = isset($row[4]) ? trim($row[4]) : '';
                    
                    // Additional optional fields
                    $tipeKontainer = isset($row[5]) ? trim($row[5]) : 'Dry Container';
                    $tanggalMulaiSewa = isset($row[6]) ? trim($row[6]) : '';
                    $tanggalSelesaiSewa = isset($row[7]) ? trim($row[7]) : '';
                    $keterangan = isset($row[8]) ? trim($row[8]) : '';
                    $status = isset($row[9]) ? trim($row[9]) : 'Tersedia';
                    
                    // Debug logging untuk troubleshooting
                    \Log::info("Processing CSV row {$rowNumber}", [
                        'raw_row' => $row,
                        'awalan' => $awalanKontainer,
                        'nomor_seri' => $nomorSeri,
                        'akhiran' => $akhiranKontainer,
                        'ukuran' => $ukuran,
                        'vendor' => $vendor,
                        'tipe_kontainer' => $tipeKontainer,
                        'tanggal_mulai_sewa' => $tanggalMulaiSewa,
                        'tanggal_selesai_sewa' => $tanggalSelesaiSewa,
                        'keterangan' => $keterangan,
                        'status' => $status,
                    ]);

                    // Validation dengan pesan error yang lebih spesifik
                    $emptyFields = [];
                    if (empty($awalanKontainer)) $emptyFields[] = 'Awalan Kontainer';
                    if (empty($nomorSeri)) $emptyFields[] = 'Nomor Seri';
                    // Akhiran bisa kosong, akan diset default '0'
                    if ($akhiranKontainer === '') {
                        $akhiranKontainer = '0';
                    }
                    if (empty($ukuran)) $emptyFields[] = 'Ukuran';
                    if (empty($vendor)) $emptyFields[] = 'Vendor';
                    
                    if (!empty($emptyFields)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Kolom kosong - " . implode(', ', $emptyFields);
                        continue;
                    }

                    // Validate format
                    if (strlen($awalanKontainer) !== 4) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Awalan kontainer harus 4 karakter";
                        continue;
                    }

                    // Pad nomor seri dengan leading zeros jika kurang dari 6 digit
                    if (strlen($nomorSeri) > 6) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Nomor seri terlalu panjang (maksimal 6 karakter)";
                        continue;
                    }
                    $nomorSeri = str_pad($nomorSeri, 6, '0', STR_PAD_LEFT);

                    if (strlen($akhiranKontainer) !== 1) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Akhiran kontainer harus 1 karakter";
                        continue;
                    }

                    // Validate ukuran
                    if (!in_array($ukuran, ['10', '20', '40'])) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Ukuran harus 10, 20, atau 40";
                        continue;
                    }

                    // Validate vendor
                    if (!in_array($vendor, ['ZONA', 'DPE'])) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Vendor harus ZONA atau DPE";
                        continue;
                    }

                    // Process optional date fields
                    $tanggalMulaiSewaConverted = null;
                    $tanggalSelesaiSewaConverted = null;
                    
                    if (!empty($tanggalMulaiSewa)) {
                        try {
                            $date = \DateTime::createFromFormat('d/M/Y', $tanggalMulaiSewa);
                            if ($date) {
                                $tanggalMulaiSewaConverted = $date->format('Y-m-d');
                            } else {
                                $stats['errors']++;
                                $stats['error_details'][] = "Baris {$rowNumber}: Format tanggal mulai sewa tidak valid (gunakan dd/mmm/yyyy)";
                                continue;
                            }
                        } catch (\Exception $e) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Format tanggal mulai sewa tidak valid";
                            continue;
                        }
                    }
                    
                    if (!empty($tanggalSelesaiSewa)) {
                        try {
                            $date = \DateTime::createFromFormat('d/M/Y', $tanggalSelesaiSewa);
                            if ($date) {
                                $tanggalSelesaiSewaConverted = $date->format('Y-m-d');
                            } else {
                                $stats['errors']++;
                                $stats['error_details'][] = "Baris {$rowNumber}: Format tanggal selesai sewa tidak valid (gunakan dd/mmm/yyyy)";
                                continue;
                            }
                        } catch (\Exception $e) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Format tanggal selesai sewa tidak valid";
                            continue;
                        }
                    }

                    // Validate status if provided
                    if (!empty($status) && !in_array($status, ['Tersedia', 'Tidak Tersedia', 'Disewa'])) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Status harus 'Tersedia' atau 'Tidak Tersedia'";
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
                        $updateData = [
                            'awalan_kontainer' => $awalanKontainer,
                            'nomor_seri_kontainer' => $nomorSeri,
                            'akhiran_kontainer' => $akhiranKontainer,
                            'nomor_seri_gabungan' => $nomorSeriGabungan,
                            'ukuran' => $ukuran,
                            'vendor' => $vendor,
                            'tipe_kontainer' => $tipeKontainer,
                            'status' => $status,
                            'updated_at' => now()
                        ];
                        
                        // Add optional fields if provided
                        if ($tanggalMulaiSewaConverted) {
                            $updateData['tanggal_mulai_sewa'] = $tanggalMulaiSewaConverted;
                        }
                        if ($tanggalSelesaiSewaConverted) {
                            $updateData['tanggal_selesai_sewa'] = $tanggalSelesaiSewaConverted;
                        }
                        if (!empty($keterangan)) {
                            $updateData['keterangan'] = $keterangan;
                        }
                        
                        $existingKontainer->update($updateData);
                        $stats['updated']++;
                    } else {
                        // Create new
                        $createData = [
                            'awalan_kontainer' => $awalanKontainer,
                            'nomor_seri_kontainer' => $nomorSeri,
                            'akhiran_kontainer' => $akhiranKontainer,
                            'nomor_seri_gabungan' => $nomorSeriGabungan,
                            'ukuran' => $ukuran,
                            'vendor' => $vendor,
                            'tipe_kontainer' => $tipeKontainer,
                            'status' => $status,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        
                        // Add optional fields if provided
                        if ($tanggalMulaiSewaConverted) {
                            $createData['tanggal_mulai_sewa'] = $tanggalMulaiSewaConverted;
                        }
                        if ($tanggalSelesaiSewaConverted) {
                            $createData['tanggal_selesai_sewa'] = $tanggalSelesaiSewaConverted;
                        }
                        if (!empty($keterangan)) {
                            $createData['keterangan'] = $keterangan;
                        }
                        
                        Kontainer::create($createData);
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

            // Tanggal sewa filter
            if ($request->filled('tanggal_sewa')) {
                switch ($request->tanggal_sewa) {
                    case 'tanpa_tanggal_akhir':
                        $query->whereNotNull('tanggal_mulai_sewa')
                              ->whereNull('tanggal_selesai_sewa');
                        break;
                    case 'ada_tanggal_akhir':
                        $query->whereNotNull('tanggal_selesai_sewa');
                        break;
                    case 'tanpa_tanggal_mulai':
                        $query->whereNull('tanggal_mulai_sewa');
                        break;
                    case 'lengkap':
                        $query->whereNotNull('tanggal_mulai_sewa')
                              ->whereNotNull('tanggal_selesai_sewa');
                        break;
                }
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
            if ($request->filled('tanggal_sewa')) {
                switch ($request->tanggal_sewa) {
                    case 'tanpa_tanggal_akhir':
                        $filterInfo[] = 'sewa-aktif-tanpa-akhir';
                        break;
                    case 'ada_tanggal_akhir':
                        $filterInfo[] = 'ada-akhir-sewa';
                        break;
                    case 'tanpa_tanggal_mulai':
                        $filterInfo[] = 'tanpa-mulai-sewa';
                        break;
                    case 'lengkap':
                        $filterInfo[] = 'sewa-lengkap';
                        break;
                }
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
                    'Tanggal Mulai Sewa',
                    'Tanggal Selesai Sewa',
                    'Tanggal Dibuat',
                    'Tanggal Diperbarui'
                ];

                // Write headers
                fputcsv($file, $csvHeaders, '\\');

                // Write data
                foreach ($kontainers as $index => $kontainer) {
                    // Format status untuk display
                    $displayStatus = 'Tersedia'; // Default

                    if (in_array($kontainer->status, ['Disewa', 'Digunakan', 'rented'])) {
                        $displayStatus = 'Disewa';
                    } elseif ($kontainer->status === 'inactive') {
                        $displayStatus = 'Nonaktif';
                    }

                    // Format tanggal sewa untuk display
                    $tanggalMulaiSewa = $kontainer->tanggal_mulai_sewa ? 
                        \Carbon\Carbon::parse($kontainer->tanggal_mulai_sewa)->format('d/M/Y') : '-';
                    $tanggalSelesaiSewa = $kontainer->tanggal_selesai_sewa ? 
                        \Carbon\Carbon::parse($kontainer->tanggal_selesai_sewa)->format('d/M/Y') : '-';

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
                        $tanggalMulaiSewa,
                        $tanggalSelesaiSewa,
                        $kontainer->created_at ? $kontainer->created_at->format('d-m-Y H:i:s') : '-',
                        $kontainer->updated_at ? $kontainer->updated_at->format('d-m-Y H:i:s') : '-'
                    ];

                    fputcsv($file, $rowData, '\\');
                }

                // Add export summary at the end
                fputcsv($file, [], '\\'); // Empty row
                fputcsv($file, ['=== INFORMASI EXPORT ==='], '\\');
                fputcsv($file, ['Total Data', count($kontainers) . ' kontainer'], '\\');
                fputcsv($file, ['Tanggal Export', date('d-m-Y H:i:s')], '\\');
                fputcsv($file, ['Exported By', auth()->user()->username ?? 'System'], '\\');

                // Add filter information if any
                if ($request->filled('search') || $request->filled('vendor') || 
                    $request->filled('ukuran') || $request->filled('status')) {
                    
                    fputcsv($file, [], '\\'); // Empty row
                    fputcsv($file, ['=== FILTER YANG DITERAPKAN ==='], '\\');
                    
                    if ($request->filled('search')) {
                        fputcsv($file, ['Pencarian', $request->search], '\\');
                    }
                    if ($request->filled('vendor')) {
                        fputcsv($file, ['Vendor', $request->vendor], '\\');
                    }
                    if ($request->filled('ukuran')) {
                        fputcsv($file, ['Ukuran', $request->ukuran . ' ft'], '\\');
                    }
                    if ($request->filled('status')) {
                        fputcsv($file, ['Status', $request->status], '\\');
                    }
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);

        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    /**
     * Download template CSV untuk import tanggal sewa kontainer
     */
    public function downloadTemplateTanggalSewa()
    {
        try {
            $fileName = 'template_tanggal_sewa_kontainer_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers for CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            // CSV content - template dengan contoh data
            $csvData = [
                ['Nomor Kontainer', 'Tanggal Mulai Sewa (dd/mmm/yyyy)', 'Tanggal Selesai Sewa (dd/mmm/yyyy)'],
                ['ALLU2202097', '01/Jan/2024', '31/Des/2024'],
                ['AMFU3131327', '15/Feb/2024', '14/Feb/2025'],
                ['AMFU3153692', '01/Mar/2024', '']
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
     * Import tanggal sewa kontainer dari CSV berdasarkan nomor kontainer
     */
    public function importTanggalSewa(Request $request)
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

            // Read CSV file with semicolon delimiter and proper encoding
            $csvData = [];
            if (($handle = fopen($path, 'r')) !== FALSE) {
                // Set UTF-8 encoding untuk handle karakter khusus
                stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-8//IGNORE');
                
                while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                    // Clean up each field dari whitespace dan karakter tersembunyi
                    $cleanData = array_map(function($field) {
                        return trim($field, " \t\n\r\0\x0B\xEF\xBB\xBF");
                    }, $data);
                    $csvData[] = $cleanData;
                }
                fclose($handle);
            }
            
            \Log::info("CSV Import Tanggal Sewa: Total rows read", ['total_rows' => count($csvData)]);

            $header = array_shift($csvData); // Remove header row

            // Validate header format
            $expectedHeaders = [
                ['Nomor Kontainer', 'Tanggal Mulai Sewa (dd/mmm/yyyy)', 'Tanggal Selesai Sewa (dd/mmm/yyyy)'],
                ['Nomor Kontainer', 'Tanggal Mulai Sewa', 'Tanggal Selesai Sewa']
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
                'errors' => 0,
                'not_found' => 0,
                'created' => 0,
                'not_found_list' => [],
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
                if (count($row) < 1) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: Data tidak lengkap";
                    continue;
                }

                try {
                    $nomorKontainer = isset($row[0]) ? trim($row[0]) : '';
                    $tanggalMulaiSewa = isset($row[1]) ? trim($row[1]) : '';
                    $tanggalSelesaiSewa = isset($row[2]) ? trim($row[2]) : '';
                    
                    \Log::info("Processing CSV row {$rowNumber}", [
                        'nomor_kontainer' => $nomorKontainer,
                        'tanggal_mulai_sewa' => $tanggalMulaiSewa,
                        'tanggal_selesai_sewa' => $tanggalSelesaiSewa,
                    ]);

                    // Validation
                    if (empty($nomorKontainer)) {
                        $stats['errors']++;
                        $stats['error_details'][] = "Baris {$rowNumber}: Nomor Kontainer kosong";
                        continue;
                    }

                    // Find kontainer by nomor_seri_gabungan
                    $kontainer = Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();

                    $isNewKontainer = false;
                    
                    // If kontainer not found, create new record
                    if (!$kontainer) {
                        // Parse nomor kontainer to extract components
                        // Standard format: ABCD123456X (4 chars prefix + 6 digits + 1 char suffix = 11 chars)
                        // Handle non-standard formats as well
                        
                        $nomorKontainerLength = strlen($nomorKontainer);
                        
                        if ($nomorKontainerLength < 5) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Nomor kontainer '{$nomorKontainer}' terlalu pendek (minimal 5 karakter)";
                            continue;
                        }
                        
                        // Extract awalan (first 4 characters)
                        $awalanKontainer = substr($nomorKontainer, 0, 4);
                        
                        // For non-standard length containers, handle differently
                        if ($nomorKontainerLength == 11) {
                            // Standard: 4 chars + 6 digits + 1 char
                            $nomorSeri = substr($nomorKontainer, 4, 6);
                            $akhiranKontainer = substr($nomorKontainer, 10, 1);
                        } else if ($nomorKontainerLength > 11) {
                            // Non-standard longer format (e.g., CAIU22348208 = 12 chars)
                            // Take remaining chars after awalan as nomor_seri, last char as akhiran
                            // But limit nomor_seri to max 6 chars to fit DB
                            $remainingChars = substr($nomorKontainer, 4);
                            $akhiranKontainer = substr($remainingChars, -1); // Last char
                            $nomorSeri = substr($remainingChars, 0, -1); // Everything except last char
                            
                            // If nomor_seri is longer than 6, take last 6 digits
                            if (strlen($nomorSeri) > 6) {
                                $nomorSeri = substr($nomorSeri, -6);
                            }
                            
                            // Log warning for non-standard format
                            \Log::warning("Non-standard kontainer format", [
                                'nomor_kontainer' => $nomorKontainer,
                                'length' => $nomorKontainerLength,
                                'awalan' => $awalanKontainer,
                                'nomor_seri' => $nomorSeri,
                                'akhiran' => $akhiranKontainer
                            ]);
                        } else {
                            // Shorter than 11 chars
                            $remainingChars = substr($nomorKontainer, 4);
                            $akhiranKontainer = substr($remainingChars, -1); // Last char
                            $nomorSeri = substr($remainingChars, 0, -1); // Everything except last char
                            
                            // Pad nomor_seri with leading zeros if needed
                            $nomorSeri = str_pad($nomorSeri, 6, '0', STR_PAD_LEFT);
                        }
                        
                        // Ensure nomor_seri_gabungan fits in database (max 11 chars)
                        $nomorSeriGabungan = $awalanKontainer . $nomorSeri . $akhiranKontainer;
                        if (strlen($nomorSeriGabungan) > 11) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Nomor kontainer '{$nomorKontainer}' terlalu panjang setelah diproses (hasil: '{$nomorSeriGabungan}', max 11 karakter). Silakan edit manual.";
                            continue;
                        }
                        
                        // Create new kontainer with default values
                        $kontainer = new Kontainer();
                        $kontainer->awalan_kontainer = $awalanKontainer;
                        $kontainer->nomor_seri_kontainer = $nomorSeri;
                        $kontainer->akhiran_kontainer = $akhiranKontainer;
                        $kontainer->nomor_seri_gabungan = $nomorSeriGabungan;
                        $kontainer->ukuran = '20'; // Default 20ft
                        $kontainer->tipe_kontainer = 'HC'; // Default High Cube
                        $kontainer->status = 'Tersedia'; // Default available
                        
                        $isNewKontainer = true;
                        
                        \Log::info("Creating new kontainer from import", [
                            'original_nomor' => $nomorKontainer,
                            'awalan' => $awalanKontainer,
                            'nomor_seri' => $nomorSeri,
                            'akhiran' => $akhiranKontainer,
                            'nomor_seri_gabungan' => $nomorSeriGabungan
                        ]);
                    }

                    // Parse dates - support format dd/mmm/yyyy (e.g., 12/Nov/2025)
                    $updateData = [];

                    // Indonesian month mapping
                    $monthMap = [
                        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr',
                        'Mei' => 'May', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Agu' => 'Aug',
                        'Sep' => 'Sep', 'Okt' => 'Oct', 'Nov' => 'Nov', 'Des' => 'Dec',
                        // Also support English months
                        'May' => 'May', 'Aug' => 'Aug', 'Oct' => 'Oct', 'Dec' => 'Dec'
                    ];

                    if (!empty($tanggalMulaiSewa)) {
                        try {
                            // Replace Indonesian month names with English equivalents
                            $tanggalMulaiSewaEng = $tanggalMulaiSewa;
                            foreach ($monthMap as $indo => $eng) {
                                $tanggalMulaiSewaEng = str_replace($indo, $eng, $tanggalMulaiSewaEng);
                            }
                            
                            $parsedDate = null;
                            
                            // Try dd mmm yy format with spaces (e.g., "15 Agu 25")
                            try {
                                $parsedDate = \Carbon\Carbon::createFromFormat('d M y', $tanggalMulaiSewaEng);
                            } catch (Exception $e) {}
                            
                            // Try dd mmm yyyy format with spaces (e.g., "15 Agu 2025")
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d M Y', $tanggalMulaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            // Try dd-mmm-yy format (e.g., 07-Apr-23)
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d-M-y', $tanggalMulaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            // Try dd-mmm-yyyy format (e.g., 07-Apr-2023)
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d-M-Y', $tanggalMulaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            // Try dd/mmm/yyyy format (e.g., 07/Apr/2023)
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d/M/Y', $tanggalMulaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            // Try dd/mmm/yy format (e.g., 07/Apr/23)
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d/M/y', $tanggalMulaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            if (!$parsedDate) {
                                $stats['errors']++;
                                $stats['error_details'][] = "Baris {$rowNumber}: Format tanggal mulai sewa tidak valid '{$tanggalMulaiSewa}' (gunakan dd-mmm-yy, dd mmm yy, atau dd-mmm-yyyy, contoh: 07-Apr-23, 15 Agu 25, atau 07-Agu-23)";
                                continue;
                            }
                            
                            $updateData['tanggal_mulai_sewa'] = $parsedDate;
                        } catch (Exception $e) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Error parsing tanggal mulai sewa: " . $e->getMessage();
                            continue;
                        }
                    }

                    if (!empty($tanggalSelesaiSewa)) {
                        try {
                            // Replace Indonesian month names with English equivalents
                            $tanggalSelesaiSewaEng = $tanggalSelesaiSewa;
                            foreach ($monthMap as $indo => $eng) {
                                $tanggalSelesaiSewaEng = str_replace($indo, $eng, $tanggalSelesaiSewaEng);
                            }
                            
                            $parsedDate = null;
                            
                            // Try dd mmm yy format with spaces (e.g., "15 Agu 25")
                            try {
                                $parsedDate = \Carbon\Carbon::createFromFormat('d M y', $tanggalSelesaiSewaEng);
                            } catch (Exception $e) {}
                            
                            // Try dd mmm yyyy format with spaces (e.g., "15 Agu 2025")
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d M Y', $tanggalSelesaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            // Try dd-mmm-yy format (e.g., 06-Agu-23)
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d-M-y', $tanggalSelesaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            // Try dd-mmm-yyyy format (e.g., 06-Agu-2023)
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d-M-Y', $tanggalSelesaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            // Try dd/mmm/yyyy format (e.g., 06/Agu/2023)
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d/M/Y', $tanggalSelesaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            // Try dd/mmm/yy format (e.g., 06/Agu/23)
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat('d/M/y', $tanggalSelesaiSewaEng);
                                } catch (Exception $e) {}
                            }
                            
                            if (!$parsedDate) {
                                $stats['errors']++;
                                $stats['error_details'][] = "Baris {$rowNumber}: Format tanggal selesai sewa tidak valid '{$tanggalSelesaiSewa}' (gunakan dd-mmm-yy, dd mmm yy, atau dd-mmm-yyyy, contoh: 06-Agu-23, 15 Des 25, atau 06-Des-23)";
                                continue;
                            }
                            
                            $updateData['tanggal_selesai_sewa'] = $parsedDate;
                        } catch (Exception $e) {
                            $stats['errors']++;
                            $stats['error_details'][] = "Baris {$rowNumber}: Error parsing tanggal selesai sewa: " . $e->getMessage();
                            continue;
                        }
                    }

                    // Update or save kontainer
                    if (!empty($updateData)) {
                        foreach ($updateData as $key => $value) {
                            $kontainer->$key = $value;
                        }
                    }
                    
                    // Save kontainer (create or update)
                    $kontainer->save();
                    $stats['success']++;
                    
                    if ($isNewKontainer) {
                        $stats['created']++;
                        \Log::info("New kontainer created", ['nomor_kontainer' => $nomorKontainer]);
                    }

                } catch (Exception $e) {
                    $stats['errors']++;
                    $stats['error_details'][] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Build success message
            $message = "Import tanggal sewa berhasil! ";
            $message .= "Diperbarui: {$stats['success']} kontainer";

            if ($stats['created'] > 0) {
                $message .= ", Dibuat baru: {$stats['created']} kontainer";
            }

            if ($stats['errors'] > 0) {
                $message .= ", Error: {$stats['errors']} baris";
            }

            // Show detailed errors if any (removed not found list since we auto-create now)
            if (!empty($stats['error_details'])) {
                $errorDetails = implode(', ', array_slice($stats['error_details'], 0, 5));
                if (count($stats['error_details']) > 5) {
                    $errorDetails .= ' dan ' . (count($stats['error_details']) - 5) . ' error lainnya';
                }
                $message .= ". Error detail: " . $errorDetails;
            }

            return redirect()->route('master.kontainer.index')->with('success', $message);

        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengimpor data tanggal sewa: ' . $e->getMessage());
        }
    }

    /**
     * Export kontainer yang belum memiliki tanggal sewa
     */
    public function exportKontainerTanpaTanggalSewa()
    {
        try {
            $fileName = 'kontainer_tanpa_tanggal_sewa_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers for CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            // Query kontainer yang BELUM memiliki kedua tanggal sewa (both null)
            $kontainers = Kontainer::whereNull('tanggal_mulai_sewa')
                ->whereNull('tanggal_selesai_sewa')
                ->orderBy('nomor_seri_gabungan', 'asc')
                ->get();

            $callback = function() use ($kontainers) {
                $file = fopen('php://output', 'w');

                // Write header
                fputcsv($file, [
                    'Nomor Kontainer',
                    'Ukuran',
                    'Tipe',
                    'Vendor',
                    'Status',
                    'Tanggal Mulai Sewa',
                    'Tanggal Selesai Sewa'
                ], ';');

                // Write data
                foreach ($kontainers as $kontainer) {
                    fputcsv($file, [
                        $kontainer->nomor_seri_gabungan,
                        $kontainer->ukuran,
                        $kontainer->tipe_kontainer,
                        $kontainer->vendor ?? '',
                        $kontainer->status,
                        $kontainer->tanggal_mulai_sewa ? $kontainer->tanggal_mulai_sewa->format('d-M-y') : '',
                        $kontainer->tanggal_selesai_sewa ? $kontainer->tanggal_selesai_sewa->format('d-M-y') : ''
                    ], ';');
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);

        } catch (Exception $e) {
            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }
}
