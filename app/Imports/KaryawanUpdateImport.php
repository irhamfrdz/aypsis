<?php

namespace App\Imports;

use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class KaryawanUpdateImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check if NIK exists in the row
            if (!isset($row['nik']) || empty($row['nik'])) {
                continue;
            }

            // Find Karyawan by NIK
            $karyawan = Karyawan::where('nik', $row['nik'])->first();
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

            $karyawan->save();
        }
    }
}
