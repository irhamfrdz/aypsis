<?php

namespace App\Imports;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Services\AbsensiDeletionGuard;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AbsensiImport implements ToCollection
{
    protected $employees;

    public $importedCount = 0;

    public function __construct()
    {
        // Cache employees
        $this->employees = Karyawan::select('id', 'nik')
            ->whereNotNull('nik')
            ->get()
            ->pluck('id', 'nik')
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        $existingLogs = Absensi::select('nik', 'waktu')
            ->get()
            ->mapWithKeys(function ($item) {
                $timeStr = $item->waktu instanceof Carbon
                    ? $item->waktu->format('Y-m-d H:i:s')
                    : Carbon::parse($item->waktu)->format('Y-m-d H:i:s');

                return [$item->nik.'_'.$timeStr => true];
            })
            ->toArray();
        $existingLogs += AbsensiDeletionGuard::deletedTimestampKeys();

        foreach ($rows as $index => $row) {
            // Lewati baris kosong
            if ($row->filter()->isEmpty()) {
                continue;
            }

            // Heuristic Parsing:
            // Cari kolom yang berisi NIK (biasanya angka panjang atau pendek)
            // Cari kolom yang berisi Tanggal/Waktu
            // Cari kolom yang berisi Status (opsional)

            $nik = null;
            $waktu = null;
            $typeStr = null;

            foreach ($row as $cell) {
                if (empty($cell)) {
                    continue;
                }

                $cellStr = trim((string) $cell);

                // Cek apakah ini datetime (Excel date number atau string tanggal)
                if (is_numeric($cell) && $cell > 20000 && $cell < 100000 && strpos((string) $cell, '.') !== false) {
                    // Excel datetime format (serial number)
                    try {
                        $waktu = Date::excelToDateTimeObject($cell)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                    }
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $cellStr) ||
                          preg_match('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$/', $cellStr) ||
                          preg_match('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/', $cellStr) ||
                          preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $cellStr)) {
                    // Format tanggal string
                    try {
                        $waktu = Carbon::parse($cellStr)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                    }
                }

                // Cek NIK (angka yang mungkin ada di kolom-kolom awal)
                // NIK kita format 4 digit
                if (is_numeric($cellStr) && strlen($cellStr) <= 10 && strpos($cellStr, '.') === false) {
                    if (! $nik) {
                        $nik = str_pad($cellStr, 4, '0', STR_PAD_LEFT);
                    }
                }

                // Preserve both machine codes and named overtime types.
                $mappedType = \App\Helpers\AttendanceEventType::fromMachineCode($cellStr);
                if ($mappedType !== null) {
                    $typeStr = $mappedType;
                }
            }
            // Jika kita menemukan NIK dan Waktu di baris ini
            if ($nik && $waktu) {
                // Tentukan type default jika tidak ditemukan
                $type = $typeStr ?: 'Masuk';
                $type = app(\App\Services\OvertimeCheckoutClassifier::class)->resolve($nik, $waktu, $type, $this->employees[$nik] ?? null);

                $key = $nik.'_'.$waktu;
                if (isset($existingLogs[$key])) {
                    continue; // Skip duplikat log persis
                }

                Absensi::create([
                    'nik' => $nik,
                    'waktu' => $waktu,
                    'tipe' => $type,
                    'karyawan_id' => $this->employees[$nik] ?? null,
                    'keterangan' => 'Import File Excel',
                ]);

                $existingLogs[$key] = true;
                $this->importedCount++;
            }
        }
    }
}
