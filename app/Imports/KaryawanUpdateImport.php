<?php

namespace App\Imports;

use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KaryawanUpdateImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        // Extract all valid NIKs from this chunk
        $niks = $rows->pluck('nik')->filter()->toArray();

        if (empty($niks)) {
            return;
        }

        // Fetch all matching Karyawan records in a single query to avoid N+1 problem
        $karyawans = Karyawan::whereIn('nik', $niks)->get()->keyBy('nik');

        // Wrap updates in a transaction for much faster database writes
        DB::transaction(function () use ($rows, $karyawans) {
            foreach ($rows as $row) {
                if (!isset($row['nik']) || empty($row['nik'])) {
                    continue;
                }

                $karyawan = $karyawans->get($row['nik']);
                if (!$karyawan) {
                    continue; // Skip if not found
                }

                $rowArray = $row->toArray();

                // Update fields if provided in Excel
                if (array_key_exists('nama_lengkap', $rowArray) && $rowArray['nama_lengkap'] !== null) {
                    $karyawan->nama_lengkap = $rowArray['nama_lengkap'];
                }
                if (array_key_exists('kantor_cabang_ayp', $rowArray) && $rowArray['kantor_cabang_ayp'] !== null) {
                    $karyawan->cabang = $rowArray['kantor_cabang_ayp'];
                }
                if (array_key_exists('pekerjaan', $rowArray) && $rowArray['pekerjaan'] !== null) {
                    $karyawan->pekerjaan = $rowArray['pekerjaan'];
                }
                if (array_key_exists('penempatan', $rowArray) && $rowArray['penempatan'] !== null) {
                    $karyawan->penempatan = $rowArray['penempatan'];
                }

                // Group & Sub Group processing
                $hasGroup = array_key_exists('group', $rowArray);
                $hasSubGroup = array_key_exists('sub_group', $rowArray);
                
                if ($hasGroup || $hasSubGroup) {
                    $groupStr = $rowArray['group'] ?? '';
                    $subGroupStr = $rowArray['sub_group'] ?? '';
                    
                    $groups = array_values(array_filter(array_map('trim', explode(',', $groupStr))));
                    $subGroups = array_values(array_filter(array_map('trim', explode(',', $subGroupStr))));
                    
                    $grupArray = [];
                    foreach ($groups as $index => $g) {
                        $sg = $subGroups[$index] ?? null;
                        if ($sg) {
                            $grupArray[] = $g . ':' . $sg;
                        } else {
                            $grupArray[] = $g;
                        }
                    }
                    
                    $karyawan->grup = !empty($grupArray) ? $grupArray : null;
                }

                // Only save if there are dirty/changed attributes to avoid unnecessary writes
                if ($karyawan->isDirty()) {
                    $karyawan->save();
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
