<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AbsensiImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Log;

class AbsensiImportController extends Controller
{
    /**
     * Menangani proses import file absensi
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_absensi' => 'required|file|mimes:xls,xlsx,csv,txt,dat|max:10240', // max 10MB
        ], [
            'file_absensi.required' => 'Pilih file absensi yang akan diimport.',
            'file_absensi.mimes' => 'Format file tidak didukung. Harap gunakan format Excel (.xls, .xlsx) atau Teks (.dat, .txt, .csv).',
            'file_absensi.max' => 'Ukuran file maksimal adalah 10MB.',
        ]);

        $file = $request->file('file_absensi');
        $extension = strtolower($file->getClientOriginalExtension());
        $importedCount = 0;

        try {
            if (in_array($extension, ['dat', 'txt', 'csv'])) {
                // Parsing file .dat/.txt (ATTLOG raw format ZKTeco)
                $importedCount = $this->parseDatFile($file->getPathname());
            } else {
                // Parsing file .xls/.xlsx menggunakan Laravel Excel
                $import = new AbsensiImport();
                Excel::import($import, $file);
                $importedCount = $import->importedCount;
            }

            if ($importedCount > 0) {
                return redirect()->route('absensi.index')->with('success', "Berhasil mengimport {$importedCount} data absensi baru.");
            } else {
                return redirect()->route('absensi.index')->with('error', 'Tidak ada data absensi baru yang ditambahkan (data kosong, format tidak dikenali, atau semua data sudah ada di database).');
            }

        } catch (\Exception $e) {
            Log::error("Error import absensi: " . $e->getMessage());
            return redirect()->route('absensi.index')->with('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }
    }

    /**
     * Parse raw ZKTeco .dat or .txt files (Tab-separated)
     */
    private function parseDatFile($filePath)
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $importedCount = 0;

        // Cache existing logs based on NIK and Exact Time
        $existingLogs = Absensi::select('nik', 'waktu')
            ->get()
            ->mapWithKeys(function ($item) {
                $timeStr = $item->waktu instanceof Carbon 
                    ? $item->waktu->format('Y-m-d H:i:s') 
                    : Carbon::parse($item->waktu)->format('Y-m-d H:i:s');
                return [$item->nik . '_' . $timeStr => true];
            })
            ->toArray();

        // Cache employees
        $employees = Karyawan::select('id', 'nik')
            ->whereNotNull('nik')
            ->get()
            ->pluck('id', 'nik')
            ->toArray();

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // ZKTeco ATTLOG format: NIK \t Waktu \t State \t VerifyMethod
            // Contoh: 1593    2026-07-22 09:01:21    0    1
            // Kadang dipisah koma jika CSV
            $separator = strpos($line, "\t") !== false ? "\t" : (strpos($line, ",") !== false ? "," : " ");
            
            // Regex untuk memecah berdasarkan separator tab atau multiple spaces
            $parts = preg_split("/[\t,]+| {2,}/", $line);
            
            if (count($parts) >= 2) {
                $nikRaw = trim($parts[0]);
                // Format NIK to 4 digits if numeric
                $nik = is_numeric($nikRaw) ? str_pad($nikRaw, 4, '0', STR_PAD_LEFT) : $nikRaw;
                
                $datetimeStr = trim($parts[1]);
                
                try {
                    $waktu = Carbon::parse($datetimeStr)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    continue; // Skip invalid date
                }

                $state = isset($parts[2]) ? (int)trim($parts[2]) : 0;
                
                // Mapping state
                if ($state == 0) {
                    $type = 'Masuk';
                } elseif ($state == 1) {
                    $type = 'Pulang';
                } elseif ($state == 2) {
                    $type = 'istirahat_keluar';
                } elseif ($state == 3) {
                    $type = 'istirahat_masuk';
                } elseif ($state == 4) {
                    $type = 'lembur_masuk';
                } elseif ($state == 5) {
                    $type = 'lembur_pulang';
                } else {
                    $type = 'Pulang';
                }

                $key = $nik . '_' . $waktu;
                if (!isset($existingLogs[$key])) {
                    Absensi::create([
                        'nik' => $nik,
                        'waktu' => $waktu,
                        'tipe' => $type,
                        'karyawan_id' => $employees[$nik] ?? null,
                        'keterangan' => 'Import File Log (.dat)',
                    ]);
                    
                    $existingLogs[$key] = true;
                    $importedCount++;
                }
            }
        }

        return $importedCount;
    }
}
