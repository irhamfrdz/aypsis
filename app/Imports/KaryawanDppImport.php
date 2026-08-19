<?php

namespace App\Imports;

use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KaryawanDppImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;
    public $failedRows = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Excel headers are automatically lowercased and spaces replaced by underscores by WithHeadingRow
            $nik = $row['nik'] ?? null;
            
            if (empty($nik)) {
                $this->failedRows[] = [
                    'row' => $index + 2, // +2 because 1 is header and 0-indexed array
                    'nik' => 'KOSONG',
                    'reason' => 'NIK tidak boleh kosong.'
                ];
                continue;
            }

            $karyawan = Karyawan::where('nik', $nik)->first();

            if (!$karyawan) {
                $this->failedRows[] = [
                    'row' => $index + 2,
                    'nik' => $nik,
                    'reason' => 'Karyawan dengan NIK tersebut tidak ditemukan.'
                ];
                continue;
            }

            // Update data
            try {
                if (isset($row['dpp_jkn'])) {
                    $karyawan->dpp_jkn = $row['dpp_jkn'] === '' || $row['dpp_jkn'] === null ? 0 : $row['dpp_jkn'];
                }
                
                if (isset($row['dpp_bp_jamsostek'])) {
                    $karyawan->dpp_bp_jamsostek = $row['dpp_bp_jamsostek'] === '' || $row['dpp_bp_jamsostek'] === null ? 0 : $row['dpp_bp_jamsostek'];
                }

                if ($karyawan->isDirty()) {
                    $karyawan->save();
                    $this->successCount++;
                }
            } catch (\Exception $e) {
                $this->failedRows[] = [
                    'row' => $index + 2,
                    'nik' => $nik,
                    'reason' => 'Gagal menyimpan: ' . $e->getMessage()
                ];
            }
        }
    }
}
