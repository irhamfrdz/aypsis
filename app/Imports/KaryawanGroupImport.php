<?php

namespace App\Imports;

use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KaryawanGroupImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;
    public $failedRows = [];

    /**
     * Process imported rows from Excel/CSV.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Kelompokkan baris berdasarkan NIK untuk mendukung multi-row per karyawan
        $groupedByNik = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Row 1 is header
            $nik = isset($row['nik']) ? trim((string)$row['nik']) : null;

            if (empty($nik)) {
                $this->failedRows[] = [
                    'row' => $rowNum,
                    'nik' => 'KOSONG',
                    'reason' => 'NIK tidak boleh kosong.'
                ];
                continue;
            }

            // Dapatkan group dan sub_group dari berbagai kemungkinan header
            $groupVal = $row['group'] ?? ($row['grup'] ?? null);
            $subGroupVal = $row['sub_group'] ?? ($row['sub_grup'] ?? ($row['subgroup'] ?? null));

            if (!isset($groupedByNik[$nik])) {
                $groupedByNik[$nik] = [
                    'rows' => [],
                    'entries' => []
                ];
            }

            $groupedByNik[$nik]['rows'][] = $rowNum;
            $groupedByNik[$nik]['entries'][] = [
                'group' => $groupVal,
                'sub_group' => $subGroupVal
            ];
        }

        // Proses setiap NIK
        foreach ($groupedByNik as $nik => $data) {
            $rowNumbers = implode(', ', $data['rows']);
            $karyawan = Karyawan::where('nik', $nik)->first();

            if (!$karyawan) {
                $this->failedRows[] = [
                    'row' => $rowNumbers,
                    'nik' => $nik,
                    'reason' => 'Karyawan dengan NIK tersebut tidak ditemukan.'
                ];
                continue;
            }

            try {
                $finalGroups = [];
                $shouldClear = false;

                foreach ($data['entries'] as $entry) {
                    $rawGroup = $entry['group'] !== null ? trim((string)$entry['group']) : '';
                    $rawSubGroup = $entry['sub_group'] !== null ? trim((string)$entry['sub_group']) : '';

                    // Jika diisi kata kunci khusus untuk mengosongkan grup
                    if (in_array(strtoupper($rawGroup), ['-', 'CLEAR', 'KOSONG', 'NULL', 'HAPUS'])) {
                        $shouldClear = true;
                        continue;
                    }

                    if (empty($rawGroup)) {
                        continue;
                    }

                    // Cek jika kolom group dipisah pemisah multiple (koma atau titik koma)
                    $groupParts = preg_split('/[;,]/', $rawGroup);
                    $subGroupParts = !empty($rawSubGroup) ? preg_split('/[;,]/', $rawSubGroup) : [];

                    if (count($groupParts) > 1) {
                        foreach ($groupParts as $idx => $gPart) {
                            $gPart = trim($gPart);
                            if (empty($gPart)) continue;

                            // Jika gPart sudah dalam format "GROUP:SUBGROUP"
                            if (strpos($gPart, ':') !== false) {
                                [$g, $s] = explode(':', $gPart, 2);
                                $item = strtoupper(trim($g));
                                $sub = strtoupper(trim($s));
                                if (!empty($sub)) {
                                    $item .= ':' . $sub;
                                }
                                $finalGroups[] = $item;
                            } else {
                                $sPart = isset($subGroupParts[$idx]) ? strtoupper(trim($subGroupParts[$idx])) : '';
                                $item = strtoupper($gPart);
                                if (!empty($sPart)) {
                                    $item .= ':' . $sPart;
                                }
                                $finalGroups[] = $item;
                            }
                        }
                    } else {
                        // Format tunggal per entry
                        if (strpos($rawGroup, ':') !== false) {
                            [$g, $s] = explode(':', $rawGroup, 2);
                            $mainG = strtoupper(trim($g));
                            $subG = !empty($rawSubGroup) ? strtoupper(trim($rawSubGroup)) : strtoupper(trim($s));
                            $item = $mainG;
                            if (!empty($subG)) {
                                $item .= ':' . $subG;
                            }
                            $finalGroups[] = $item;
                        } else {
                            $mainG = strtoupper(trim($rawGroup));
                            $subG = strtoupper(trim($rawSubGroup));
                            $item = $mainG;
                            if (!empty($subG)) {
                                $item .= ':' . $subG;
                            }
                            $finalGroups[] = $item;
                        }
                    }
                }

                // Tentukan data grup baru
                if ($shouldClear && empty($finalGroups)) {
                    $newGroupData = [];
                } else {
                    $newGroupData = array_values(array_unique(array_filter($finalGroups)));
                }

                $karyawan->grup = $newGroupData;
                $karyawan->save();
                $this->successCount++;
            } catch (\Exception $e) {
                $this->failedRows[] = [
                    'row' => $rowNumbers,
                    'nik' => $nik,
                    'reason' => 'Gagal menyimpan data group: ' . $e->getMessage()
                ];
            }
        }
    }
}
