<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MobilImport;
use App\Exports\MobilTemplateExport;
use Exception;

class MasterMobilImportController extends Controller
{
    /**
     * Download template Excel untuk import master kendaraan
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'template_master_asset_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new MobilTemplateExport, $fileName);

        } catch (Exception $e) {
            return back()->with('error', 'Gagal mendownload template: ' . $e->getMessage());
        }
    }



    /**
     * Import data master kendaraan dari Excel
     */
    public function import(Request $request)
    {
        // Validasi file upload
        $validator = Validator::make($request->all(), [
            'excel_file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240' // 10MB
            ]
        ], [
            'excel_file.required' => 'File Excel harus dipilih.',
            'excel_file.mimes' => 'File harus berformat .xlsx atau .xls',
            'excel_file.max' => 'Ukuran file maksimal 10MB.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('excel_file');
            
            // Read Excel file using Maatwebsite Excel
            $importData = Excel::toArray(new MobilImport, $file);
            $excelData = $importData[0]; // Get first sheet data
            
            $header = array_shift($excelData); // Remove header row

            $stats = [
                'success' => 0,
                'updated' => 0,
                'errors' => 0,
                'skipped' => 0,
                'error_details' => [],
                'warnings' => []
            ];

            DB::beginTransaction();

            foreach ($excelData as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // +2 because we removed header and start from 1

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Map Excel columns to variables
                    $kodeAktiva = trim($row[0] ?? '');
                    $nomorPolisi = trim($row[1] ?? '');
                    $nik = trim($row[2] ?? '');
                    $namaLengkap = trim($row[3] ?? '');
                    $lokasi = trim($row[4] ?? '');
                    $merek = trim($row[5] ?? '');
                    $jenis = trim($row[6] ?? '');
                    $tahunPembuatan = trim($row[7] ?? '');
                    $bpkb = trim($row[8] ?? '');
                    $noMesin = trim($row[9] ?? '');
                    $noRangka = trim($row[10] ?? '');
                    $pajakStnk = trim($row[11] ?? '');
                    $pajakPlat = trim($row[12] ?? '');
                    $noKir = trim($row[13] ?? '');
                    $pajakKir = trim($row[14] ?? '');
                    $atasNama = trim($row[15] ?? '');
                    $pemakai = trim($row[16] ?? '');
                    $asuransi = trim($row[17] ?? '');
                    $jteAsuransi = trim($row[18] ?? '');
                    $warnaPlat = trim($row[19] ?? '');
                    $catatan = trim($row[20] ?? '');

                    // Skip only if both kode aktiva and nomor polisi are empty
                    if (empty($kodeAktiva) && empty($nomorPolisi)) {
                        $stats['skipped']++;
                        $stats['warnings'][] = "Baris {$rowNumber}: Tidak ada kode aktiva dan nomor polisi, dilewati.";
                        continue;
                    }

                    // Find karyawan by NIK if provided
                    $karyawanId = null;
                    if (!empty($nik)) {
                        $karyawanQuery = \App\Models\Karyawan::where('nik', $nik);
                        
                        // Filter berdasarkan cabang user yang login
                        $currentUser = auth()->user();
                        if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang) {
                            $userCabang = $currentUser->karyawan->cabang;
                            $karyawanQuery->where('cabang', $userCabang);
                        }
                        
                        $karyawan = $karyawanQuery->first();
                        
                        if ($karyawan) {
                            $karyawanId = $karyawan->id;
                            // Update plat karyawan only if nomor polisi exists
                            if (!empty($nomorPolisi)) {
                                $karyawan->update(['plat' => $nomorPolisi]);
                            }
                        } else {
                            if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang) {
                                $stats['warnings'][] = "Baris {$rowNumber}: NIK $nik tidak ditemukan di cabang {$currentUser->karyawan->cabang}.";
                            } else {
                                $stats['warnings'][] = "Baris {$rowNumber}: NIK $nik tidak ditemukan di database karyawan.";
                            }
                        }
                    }

                    // Parse dates
                    $pajakStnkDate = $this->parseDate($pajakStnk);
                    $pajakPlatDate = $this->parseDate($pajakPlat);
                    $pajakKirDate = $this->parseDate($pajakKir);
                    $jteAsuransiDate = $this->parseDate($jteAsuransi);

                    // Check if mobil already exists - prioritize by kode_aktiva, fallback to nomor_polisi
                    $existingMobil = null;
                    if (!empty($kodeAktiva)) {
                        $existingMobil = Mobil::where('kode_no', $kodeAktiva)->first();
                    } elseif (!empty($nomorPolisi)) {
                        $existingMobil = Mobil::where('nomor_polisi', $nomorPolisi)->first();
                    }

                    $data = [
                        'kode_no' => $kodeAktiva ?: null,
                        'nomor_polisi' => $nomorPolisi ?: null,
                        'lokasi' => $lokasi ?: null,
                        'merek' => $merek ?: null,
                        'jenis' => $jenis ?: null,
                        'tahun_pembuatan' => is_numeric($tahunPembuatan) ? (int)$tahunPembuatan : null,
                        'bpkb' => $bpkb ?: null,
                        'no_mesin' => $noMesin ?: null,
                        'nomor_rangka' => $noRangka ?: null,
                        'pajak_stnk' => $pajakStnkDate,
                        'pajak_plat' => $pajakPlatDate,
                        'no_kir' => $noKir ?: null,
                        'pajak_kir' => $pajakKirDate,
                        'atas_nama' => $atasNama ?: null,
                        'pemakai' => $pemakai ?: null,
                        'asuransi' => $asuransi ?: null,
                        'jatuh_tempo_asuransi' => $jteAsuransiDate,
                        'warna_plat' => $warnaPlat ?: null,
                        'catatan' => $catatan ?: null,
                        'karyawan_id' => $karyawanId,
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
            if ($stats['skipped'] > 0) {
                $message .= "{$stats['skipped']} data dilewati. ";
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

    /**
     * Parse tanggal dari berbagai format.
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Try different date formats
        $formats = [
            'd M y',  // 24 Sep 26
            'd M Y',  // 24 Sep 2026
            'd/m/Y',  // 24/09/2026
            'Y-m-d',  // 2026-09-24
            'd-m-Y',  // 24-09-2026
            'd-m-y',  // 24-09-26
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                // Convert 2-digit year to 4-digit
                $year = $date->format('Y');
                if (strlen($year) == 2) {
                    $yearInt = (int)$year;
                    if ($yearInt < 50) {
                        $date->setDate(2000 + $yearInt, $date->format('m'), $date->format('d'));
                    } else {
                        $date->setDate(1900 + $yearInt, $date->format('m'), $date->format('d'));
                    }
                }
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Export data mobil ke berbagai format
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'excel');
            
            // Build query dengan filter yang sama seperti index
            $query = Mobil::with('karyawan');

            // Filter berdasarkan lokasi mobil untuk user cabang BTM
            $currentUser = auth()->user();
            if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
                // Filter mobil berdasarkan lokasi BTM (Batam) untuk user BTM
                $query->where('lokasi', 'BTM');
            }

            // Apply search filter if exists
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('kode_no', 'like', "%{$search}%")
                      ->orWhere('nomor_polisi', 'like', "%{$search}%")
                      ->orWhere('no_kir', 'like', "%{$search}%")
                      ->orWhere('merek', 'like', "%{$search}%")
                      ->orWhere('jenis', 'like', "%{$search}%")
                      ->orWhere('lokasi', 'like', "%{$search}%")
                      ->orWhere('no_mesin', 'like', "%{$search}%")
                      ->orWhere('nomor_rangka', 'like', "%{$search}%")
                      ->orWhere('bpkb', 'like', "%{$search}%")
                      ->orWhere('atas_nama', 'like', "%{$search}%")
                      ->orWhere('pemakai', 'like', "%{$search}%")
                      ->orWhereHas('karyawan', function($subQ) use ($search) {
                          $subQ->where('nama_lengkap', 'like', "%{$search}%")
                               ->orWhere('nik', 'like', "%{$search}%");
                      });
                });
            }

            $mobils = $query->latest()->get();

            switch ($format) {
                case 'excel':
                    // Use Maatwebsite Excel (XLSX) to export where possible
                    $fileName = 'master_mobil_' . date('Y-m-d_H-i-s') . '.xlsx';
                    return Excel::download(new \App\Exports\MobilExport($mobils), $fileName);
                case 'csv':
                    return $this->exportToCsv($mobils, $request);
                case 'pdf':
                    return $this->exportToPdf($mobils, $request);
                default:
                    return $this->exportToExcel($mobils, $request);
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error saat export: ' . $e->getMessage());
        }
    }

    /**
     * Export ke format Excel
     */
    private function exportToExcel($mobils, $request)
    {
        $searchTerm = $request->get('search', '');
        $fileName = 'master_mobil_' . date('Y-m-d_H-i-s') . '.csv';
        
        if ($searchTerm) {
            $fileName = 'master_mobil_search_' . date('Y-m-d_H-i-s') . '.csv';
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($mobils) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($file, "\xEF\xBB\xBF");

            // Headers
            fputcsv($file, [
                'No',
                'Kode Aktiva',
                'Nomor Polisi', 
                'NIK Karyawan',
                'Nama Karyawan',
                'Lokasi',
                'Merek',
                'Jenis',
                'Tahun Pembuatan',
                'BPKB',
                'No. Mesin',
                'No. Rangka',
                'Pajak STNK',
                'Pajak Plat',
                'No. KIR',
                'Pajak KIR',
                'Atas Nama',
                'Pemakai',
                'Asuransi',
                'JTE Asuransi',
                'Warna Plat',
                'Catatan',
                'Dibuat Tanggal',
                'Diperbarui Tanggal'
            ], '|');

            // Data rows
            foreach ($mobils as $index => $mobil) {
                fputcsv($file, [
                    $index + 1,
                    $mobil->kode_no ?? '',
                    $mobil->nomor_polisi ?? '',
                    $mobil->karyawan->nik ?? '',
                    $mobil->karyawan->nama_lengkap ?? '',
                    $mobil->lokasi ?? '',
                    $mobil->merek ?? '',
                    $mobil->jenis ?? '',
                    $mobil->tahun_pembuatan ?? '',
                    $mobil->bpkb ?? '',
                    $mobil->no_mesin ?? '',
                    $mobil->nomor_rangka ?? '',
                    $mobil->pajak_stnk ? date('d M Y', strtotime($mobil->pajak_stnk)) : '',
                    $mobil->pajak_plat ? date('d M Y', strtotime($mobil->pajak_plat)) : '',
                    $mobil->no_kir ?? '',
                    $mobil->pajak_kir ? date('d M Y', strtotime($mobil->pajak_kir)) : '',
                    $mobil->atas_nama ?? '',
                    $mobil->pemakai ?? '',
                    $mobil->asuransi ?? '',
                    $mobil->jte_asuransi ? date('d M Y', strtotime($mobil->jte_asuransi)) : '',
                    $mobil->warna_plat ?? '',
                    $mobil->catatan ?? '',
                    $mobil->created_at ? $mobil->created_at->format('d M Y H:i') : '',
                    $mobil->updated_at ? $mobil->updated_at->format('d M Y H:i') : '',
                ], '|');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export ke format CSV
     */
    private function exportToCsv($mobils, $request)
    {
        return $this->exportToExcel($mobils, $request); // Same as Excel for now
    }

    /**
     * Export ke format PDF
     */
    private function exportToPdf($mobils, $request)
    {
        $searchTerm = $request->get('search', '');
        
        // Data untuk PDF
        $data = [
            'mobils' => $mobils,
            'search' => $searchTerm,
            'total' => $mobils->count(),
            'exported_at' => now()->format('d F Y H:i:s'),
            'exported_by' => auth()->user()->name ?? 'System'
        ];

        // Generate PDF menggunakan view
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('exports.mobil-pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'master_mobil_' . date('Y-m-d_H-i-s') . '.pdf';
        if ($searchTerm) {
            $fileName = 'master_mobil_search_' . date('Y-m-d_H-i-s') . '.pdf';
        }

        return $pdf->download($fileName);
    }
}
