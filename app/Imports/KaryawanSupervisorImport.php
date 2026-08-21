<?php

namespace App\Imports;

use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KaryawanSupervisorImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;
    public $failedRows = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $nik = $row['nik'] ?? null;
            $nik_supervisor = $row['nik_supervisor'] ?? null;
            
            if (empty($nik)) {
                $this->failedRows[] = [
                    'row' => $index + 2,
                    'nik' => 'KOSONG',
                    'reason' => 'NIK Karyawan tidak boleh kosong.'
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

            try {
                if ($nik_supervisor !== null && $nik_supervisor !== '') {
                    $supervisor = Karyawan::where('nik', $nik_supervisor)->first();
                    if (!$supervisor) {
                        $this->failedRows[] = [
                            'row' => $index + 2,
                            'nik' => $nik,
                            'reason' => "Supervisor dengan NIK {$nik_supervisor} tidak ditemukan."
                        ];
                        continue;
                    }

                    $karyawan->nik_supervisor = $supervisor->nik;
                    $karyawan->supervisor = $supervisor->nama_lengkap;
                } elseif (isset($row['nik_supervisor']) && $nik_supervisor === '') {
                    // if it's explicitly empty in the excel, clear it
                    $karyawan->nik_supervisor = null;
                    $karyawan->supervisor = null;
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
