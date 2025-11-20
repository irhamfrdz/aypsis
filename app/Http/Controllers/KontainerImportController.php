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
                    // Format status untuk display - exactly match database values
                    $displayStatus = $kontainer->status; // Use actual database value
                    
                    // Ensure we handle all possible status values correctly
                    if ($kontainer->status === 'Tersedia') {
                        $displayStatus = 'Tersedia';
                    } elseif ($kontainer->status === 'Tidak Tersedia') {
                        $displayStatus = 'Tidak Tersedia';
                    } elseif (in_array($kontainer->status, ['Disewa', 'Digunakan', 'rented'])) {
                        $displayStatus = 'Disewa';
                    } elseif ($kontainer->status === 'inactive') {
                        $displayStatus = 'Nonaktif';
                    } else {
                        // For any other status, use the actual value from database
                        $displayStatus = $kontainer->status;
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
                ['Nomor Kontainer', 'Tanggal Mulai Sewa (dd/mmm/yyyy)', 'Tanggal Selesai Sewa (dd/mmm/yyyy)', 'Status'],
                ['ALLU2202097', '01/Jan/2024', '31/Des/2024', 'Tersedia'],
                ['AMFU3131327', '15/Feb/2024', '14/Feb/2025', 'Tidak Tersedia'],
                ['AMFU3153692', '01/Mar/2024', '', 'Tersedia']
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
            $fileContent = file_get_contents($file->getRealPath());
            
            // Handle UTF-8 BOM
            $fileContent = str_replace("\xEF\xBB\xBF", '', $fileContent);
            
            // Split into lines
            $lines = array_filter(array_map('trim', explode("\n", $fileContent)));
            
            if (count($lines) < 2) {
                return redirect()->route('master.kontainer.index')
                    ->with('error', 'File CSV kosong atau tidak valid. Minimal harus ada header dan 1 baris data.');
            }

            // Skip header
            array_shift($lines);
            
            $updated = 0;
            $notFound = [];
            $errors = [];
            $skipped = 0;

            DB::beginTransaction();

            foreach ($lines as $lineNumber => $line) {
                $actualLine = $lineNumber + 2; // +2 karena array index 0 + skip header
                
                // Parse CSV with semicolon delimiter
                $data = str_getcsv($line, ';');
                
                // Skip empty lines
                if (empty(array_filter($data))) {
                    $skipped++;
                    continue;
                }

                // Minimal harus ada nomor kontainer (kolom 1)
                if (!isset($data[0]) || empty(trim($data[0]))) {
                    $errors[] = "Baris {$actualLine}: Nomor kontainer tidak boleh kosong";
                    continue;
                }

                $nomorKontainer = strtoupper(trim($data[0]));
                
                // Cari kontainer berdasarkan nomor gabungan
                $kontainer = Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
                
                // HANYA UPDATE - JANGAN CREATE BARU
                if (!$kontainer) {
                    $notFound[] = "Baris {$actualLine}: Kontainer '{$nomorKontainer}' tidak ditemukan";
                    continue;
                }

                // Data yang akan diupdate
                $updateData = [];
                
                // Parse tanggal mulai sewa (kolom 2)
                if (isset($data[1]) && !empty(trim($data[1]))) {
                    $tanggalMulai = trim($data[1]);
                    
                    try {
                        // Coba parse format d/M/Y (contoh: 01/Jan/2025)
                        $parsedDate = \Carbon\Carbon::createFromFormat('d/M/Y', $tanggalMulai);
                        $updateData['tanggal_mulai_sewa'] = $parsedDate->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            // Coba parse format Y-m-d (contoh: 2025-01-01)
                            $parsedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalMulai);
                            $updateData['tanggal_mulai_sewa'] = $parsedDate->format('Y-m-d');
                        } catch (\Exception $e2) {
                            $errors[] = "Baris {$actualLine}: Format tanggal mulai sewa tidak valid '{$tanggalMulai}'. Gunakan format dd/mmm/yyyy (contoh: 01/Jan/2025)";
                            continue;
                        }
                    }
                }
                
                // Parse tanggal selesai sewa (kolom 3)
                if (isset($data[2]) && !empty(trim($data[2]))) {
                    $tanggalSelesai = trim($data[2]);
                    
                    try {
                        // Coba parse format d/M/Y (contoh: 31/Dec/2025)
                        $parsedDate = \Carbon\Carbon::createFromFormat('d/M/Y', $tanggalSelesai);
                        $updateData['tanggal_selesai_sewa'] = $parsedDate->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            // Coba parse format Y-m-d (contoh: 2025-12-31)
                            $parsedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalSelesai);
                            $updateData['tanggal_selesai_sewa'] = $parsedDate->format('Y-m-d');
                        } catch (\Exception $e2) {
                            $errors[] = "Baris {$actualLine}: Format tanggal selesai sewa tidak valid '{$tanggalSelesai}'. Gunakan format dd/mmm/yyyy (contoh: 31/Dec/2025)";
                            continue;
                        }
                    }
                }
                
                // Parse status (kolom 4) - opsional
                if (isset($data[3]) && !empty(trim($data[3]))) {
                    $status = trim($data[3]);
                    
                    // Validasi status hanya boleh Tersedia atau Tidak Tersedia
                    if (!in_array($status, ['Tersedia', 'Tidak Tersedia'])) {
                        $errors[] = "Baris {$actualLine}: Status tidak valid '{$status}'. Gunakan 'Tersedia' atau 'Tidak Tersedia'";
                        continue;
                    }
                    
                    $updateData['status'] = $status;
                }

                // Skip jika tidak ada data untuk diupdate
                if (empty($updateData)) {
                    $skipped++;
                    continue;
                }

                // Update kontainer
                foreach ($updateData as $field => $value) {
                    $kontainer->$field = $value;
                }
                $kontainer->save();
                
                $updated++;
            }

            DB::commit();

            // Build pesan hasil import
            $messages = [];
            
            if ($updated > 0) {
                $messages[] = "Berhasil update {$updated} kontainer";
            }
            
            if (!empty($notFound)) {
                $notFoundCount = count($notFound);
                $notFoundPreview = implode('; ', array_slice($notFound, 0, 3));
                if ($notFoundCount > 3) {
                    $notFoundPreview .= " ... dan " . ($notFoundCount - 3) . " kontainer lainnya";
                }
                $messages[] = "WARNING: {$notFoundCount} kontainer tidak ditemukan - {$notFoundPreview}";
            }
            
            if (!empty($errors)) {
                $errorCount = count($errors);
                $errorPreview = implode('; ', array_slice($errors, 0, 3));
                if ($errorCount > 3) {
                    $errorPreview .= " ... dan " . ($errorCount - 3) . " error lainnya";
                }
                $messages[] = "ERROR: {$errorCount} baris gagal - {$errorPreview}";
            }
            
            if ($skipped > 0) {
                $messages[] = "Dilewati: {$skipped} baris (kosong/tidak ada data update)";
            }

            $finalMessage = implode('. ', $messages);
            $flashType = (!empty($errors) || !empty($notFound)) ? 'warning' : 'success';

            return redirect()->route('master.kontainer.index')->with($flashType, $finalMessage);

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
