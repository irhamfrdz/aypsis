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
        // Extract all valid NIKs from this chunk and cast to string
        $niks = $rows->pluck('nik')->map(function($nik) {
            return trim((string)$nik);
        })->filter(function($nik) {
            return $nik !== '';
        })->toArray();

        Log::info("Import Update Data started", ['niks' => $niks]);

        if (empty($niks)) {
            Log::info("No valid NIKs found in this chunk");
            return;
        }

        // Fetch all matching Karyawan records in a single query to avoid N+1 problem
        $karyawans = Karyawan::whereIn('nik', $niks)->get()->keyBy('nik');
        
        Log::info("Found matching Karyawans", ['count' => $karyawans->count(), 'keys' => $karyawans->keys()->toArray()]);

        // Wrap updates in a transaction for much faster database writes
        DB::transaction(function () use ($rows, $karyawans) {
            foreach ($rows as $row) {
                if (!isset($row['nik'])) {
                    continue;
                }

                $nikStr = trim((string)$row['nik']);
                if ($nikStr === '') {
                    continue;
                }

                $karyawan = $karyawans->get($nikStr);
                if (!$karyawan) {
                    Log::warning("NIK not found in database during import: " . $nikStr);
                    continue; // Skip if not found
                }

                $rowArray = $row->toArray();

                // Update fields if provided in Excel
                if (array_key_exists('nama_lengkap', $rowArray) && $rowArray['nama_lengkap'] !== null) {
                    $karyawan->nama_lengkap = trim((string)$rowArray['nama_lengkap']);
                }
                if (array_key_exists('kantor_cabang_ayp', $rowArray) && $rowArray['kantor_cabang_ayp'] !== null) {
                    $karyawan->cabang = trim((string)$rowArray['kantor_cabang_ayp']);
                }
                if (array_key_exists('pekerjaan', $rowArray) && $rowArray['pekerjaan'] !== null) {
                    $karyawan->pekerjaan = trim((string)$rowArray['pekerjaan']);
                }
                if (array_key_exists('penempatan', $rowArray) && $rowArray['penempatan'] !== null) {
                    $karyawan->penempatan = trim((string)$rowArray['penempatan']);
                }

                // Group & Sub Group processing
                $hasGroup = array_key_exists('group', $rowArray);
                $hasSubGroup = array_key_exists('sub_group', $rowArray);
                
                if ($hasGroup || $hasSubGroup) {
                    $groupStr = $rowArray['group'] ?? '';
                    $subGroupStr = $rowArray['sub_group'] ?? '';
                    
                    $groups = array_values(array_filter(array_map('trim', explode(',', (string)$groupStr))));
                    $subGroups = array_values(array_filter(array_map('trim', explode(',', (string)$subGroupStr))));
                    
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

                if ($karyawan->isDirty()) {
                    Log::info("Updating NIK: $nikStr", ['dirty' => $karyawan->getDirty()]);
                    $karyawan->save();
                } else {
                    Log::info("No changes for NIK: $nikStr");
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
