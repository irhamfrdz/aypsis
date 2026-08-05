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
    public $successCount = 0;
    public $failedRows = [];
    public $successRows = [];

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

        if (empty($niks) && empty($namaLengkaps)) {
            return;
        }

        // Fetch all matching Karyawan records in a single query by NIK or Nama Lengkap
        $karyawans = Karyawan::whereIn('nik', $niks)
            ->orWhereIn('nama_lengkap', $namaLengkaps)
            ->get();
            
        $byNik = $karyawans->keyBy('nik');
        $byNama = $karyawans->keyBy('nama_lengkap');
        
        // Wrap updates in a transaction for much faster database writes
        DB::transaction(function () use ($rows, $byNik, $byNama) {
            foreach ($rows as $row) {
                $rowArray = $row->toArray();
                $nikStr = isset($rowArray['nik']) ? trim((string)$rowArray['nik']) : '';
                $namaStr = isset($rowArray['nama_lengkap']) ? trim((string)$rowArray['nama_lengkap']) : '';

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
                    $this->failedRows[] = "NIK: " . ($nikStr ?: '-') . " / Nama: " . ($namaStr ?: '-') . " - Tidak ditemukan di sistem";
                    continue; // Skip if completely not found
                }

                // Update fields if provided in Excel
                if (array_key_exists('nama_lengkap', $rowArray) && $rowArray['nama_lengkap'] !== null && trim((string)$rowArray['nama_lengkap']) !== '') {
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
                    $groupVal = $hasGroup ? trim((string)$rowArray['group']) : '';
                    $subGroupVal = $hasSubGroup ? trim((string)$rowArray['sub_group']) : '';
                    
                    if ($groupVal !== '' || $subGroupVal !== '') {
                        $groups = array_map('trim', explode(',', $groupVal));
                        $subGroups = array_map('trim', explode(',', $subGroupVal));
                        
                        $finalGroups = [];
                        foreach ($groups as $idx => $g) {
                            if ($g !== '') {
                                $sg = $subGroups[$idx] ?? '';
                                if ($sg !== '') {
                                    $finalGroups[] = $g . ':' . $sg;
                                } else {
                                    $finalGroups[] = $g;
                                }
                            }
                        }
                        
                        $karyawan->grup = !empty($finalGroups) ? $finalGroups : null;
                    } elseif ($groupVal === '' && $subGroupVal === '') {
                        // Optional: you can choose to clear the group if user provided empty string
                        // For safe update, we will only clear if explicitly both are present and empty.
                        if ($hasGroup && $hasSubGroup) {
                            $karyawan->grup = null;
                        }
                    }
                }

                $karyawan->save();
                
                $this->successCount++;
                if (count($this->successRows) < 5) {
                    $this->successRows[] = $karyawan->nama_lengkap;
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
