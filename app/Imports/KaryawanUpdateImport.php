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
        // Extract all valid NIKs and Nama Lengkap from this chunk and cast to string
        $niks = $rows->pluck('nik')->map(function($nik) {
            return trim((string)$nik);
        })->filter(function($nik) {
            return $nik !== '';
        })->toArray();

        $namaLengkaps = $rows->pluck('nama_lengkap')->map(function($nama) {
            return trim((string)$nama);
        })->filter(function($nama) {
            return $nama !== '';
        })->toArray();

        Log::info("Import Update Data started", ['nik_count' => count($niks), 'nama_count' => count($namaLengkaps)]);

        if (empty($niks) && empty($namaLengkaps)) {
            Log::info("No valid NIKs or Nama Lengkap found in this chunk");
            return;
        }

        // Fetch all matching Karyawan records in a single query by NIK or Nama Lengkap
        $karyawans = Karyawan::whereIn('nik', $niks)
            ->orWhereIn('nama_lengkap', $namaLengkaps)
            ->get();
            
        $byNik = $karyawans->keyBy('nik');
        $byNama = $karyawans->keyBy('nama_lengkap');
        
        Log::info("Found matching Karyawans", ['count' => $karyawans->count()]);

        // Wrap updates in a transaction for much faster database writes
        DB::transaction(function () use ($rows, $byNik, $byNama) {
            foreach ($rows as $row) {
                $nikStr = isset($row['nik']) ? trim((string)$row['nik']) : '';
                $namaStr = isset($row['nama_lengkap']) ? trim((string)$row['nama_lengkap']) : '';

                if ($nikStr === '' && $namaStr === '') {
                    continue; // Skip if both empty
                }

                $karyawan = null;

                // 1. Try to find by NIK
                if ($nikStr !== '') {
                    $karyawan = $byNik->get($nikStr);
                }

                // 2. If not found by NIK, try to find by NAMA LENGKAP
                if (!$karyawan && $namaStr !== '') {
                    $karyawan = $byNama->get($namaStr);
                }

                if (!$karyawan) {
                    Log::warning("Karyawan not found in database during import. NIK: '$nikStr', Nama: '$namaStr'");
                    continue; // Skip if completely not found
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
