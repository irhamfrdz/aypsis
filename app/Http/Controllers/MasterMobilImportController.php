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

            // CSV content dengan header sesuai format asli
            $csvData = [
                [
                    'Kode Aktiva', 'NO.POLISI', 'nik', 'nama_lengkap', 'LOKASI', 'MEREK', 'JENIS', 
                    'TAHUN PEMBUATAN', 'BPKB', 'NO. MESIN', 'NO. RANGKA', 'PAJAK STNK', 'PAJAK PLAT', 
                    'NO. KIR', 'PAJAK KIR', 'ATAS NAMA', 'PEMAKAI', 'ASURANSI', 'JTE ASURANSI', 
                    'WARNA PLAT', 'Catatan'
                ],
                [
                    'AT1122500001', 'B5598BBA', '1234', 'NAMA KARYAWAN', 'JKT', 'HONDA', 'SEPEDA MOTOR',
                    '2020', 'R12345678', 'JBK1E1714025', 'MH1JBK116LK717264', '24 Sep 26', '24 Sep 30',
                    '', '', 'FERRY KURNIAWAN', 'OWEN', 'ZURICH ASURANSI INDONESIA, PT', '26 Jun 26',
                    'HITAM', 'MTR-JKT.031'
                ]
            ];

            $callback = function() use ($csvData) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fputs($file, "\xEF\xBB\xBF");

                // Set CSV dengan comma delimiter (default Excel)
                foreach ($csvData as $row) {
                    fputcsv($file, $row);
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
                'max:10240' // 10MB
            ]
        ], [
            'csv_file.required' => 'File CSV harus dipilih.',
            'csv_file.mimes' => 'File harus berformat .csv',
            'csv_file.max' => 'Ukuran file maksimal 10MB.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // Read CSV file
            $csvData = [];
            if (($handle = fopen($path, 'r')) !== FALSE) {
                while (($data = fgetcsv($handle, 10000)) !== FALSE) {
                    $csvData[] = $data;
                }
                fclose($handle);
            }

            $header = array_shift($csvData); // Remove header row

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

                try {
                    // Map CSV columns to variables
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

                    // Skip if no nomor polisi
                    if (empty($nomorPolisi)) {
                        $stats['skipped']++;
                        $stats['warnings'][] = "Baris {$rowNumber}: Tidak ada nomor polisi, dilewati.";
                        continue;
                    }

                    // Find karyawan by NIK if provided
                    $karyawanId = null;
                    if (!empty($nik)) {
                        $karyawan = \App\Models\Karyawan::where('nik', $nik)->first();
                        if ($karyawan) {
                            $karyawanId = $karyawan->id;
                            // Update plat karyawan
                            $karyawan->update(['plat' => $nomorPolisi]);
                        } else {
                            $stats['warnings'][] = "Baris {$rowNumber}: NIK $nik tidak ditemukan di database karyawan.";
                        }
                    }

                    // Parse dates
                    $pajakStnkDate = $this->parseDate($pajakStnk);
                    $pajakPlatDate = $this->parseDate($pajakPlat);
                    $pajakKirDate = $this->parseDate($pajakKir);
                    $jteAsuransiDate = $this->parseDate($jteAsuransi);

                    // Check if mobil already exists
                    $existingMobil = Mobil::where('nomor_polisi', $nomorPolisi)->first();

                    $data = [
                        'kode_no' => $kodeAktiva ?: null,
                        'nomor_polisi' => $nomorPolisi,
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
}
