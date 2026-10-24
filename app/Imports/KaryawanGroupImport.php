<?php

namespace App\Imports;

use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KaryawanGroupImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;

    public $failedRows = [];

    /**
     * Process imported rows from Excel/CSV.
     */
    public function collection(Collection $rows)
    {
        // Kelompokkan baris berdasarkan NIK untuk mendukung multi-row per karyawan
        $groupedByNik = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Row 1 is header
            $nik = isset($row['nik']) ? trim((string) $row['nik']) : null;

            if (empty($nik)) {
                $this->failedRows[] = [
                    'row' => $rowNum,
                    'nik' => 'KOSONG',
                    'reason' => 'NIK tidak boleh kosong.',
                ];

                continue;
            }

            // Dapatkan group dan sub_group dari berbagai kemungkinan header
            $groupVal = $row['group'] ?? ($row['grup'] ?? null);
            $subGroupVal = $row['sub_group'] ?? ($row['sub_grup'] ?? ($row['subgroup'] ?? null));

            if (! isset($groupedByNik[$nik])) {
                $groupedByNik[$nik] = [
                    'rows' => [],
                    'entries' => [],
                ];
            }

            $groupedByNik[$nik]['rows'][] = $rowNum;
            $groupedByNik[$nik]['entries'][] = [
                'group' => $groupVal,
                'sub_group' => $subGroupVal,
            ];
        }

        // Proses setiap NIK
        foreach ($groupedByNik as $nik => $data) {
            $rowNumbers = implode(', ', $data['rows']);
            $karyawan = Karyawan::where('nik', $nik)->first();

            if (! $karyawan) {
                $this->failedRows[] = [
                    'row' => $rowNumbers,
                    'nik' => $nik,
                    'reason' => 'Karyawan dengan NIK tersebut tidak ditemukan.',
                ];

                continue;
            }

            try {
                $finalGroups = [];

                foreach ($data['entries'] as $entry) {
                    $rawGroup = $entry['group'] !== null ? trim((string) $entry['group']) : '';
                    $rawSubGroup = $entry['sub_group'] !== null ? trim((string) $entry['sub_group']) : '';

                    // This import only appends groups; deletion belongs in the edit form.
                    if (in_array(strtoupper($rawGroup), ['-', 'CLEAR', 'KOSONG', 'NULL', 'HAPUS'])) {
                        throw new \InvalidArgumentException('Impor ini hanya menambahkan Group & Sub Group. Untuk menghapus group, gunakan edit karyawan.');
                    }

                    if (empty($rawGroup)) {
                        continue;
                    }

                    // Cek jika kolom group dipisah pemisah multiple (koma atau titik koma)
                    $groupParts = preg_split('/[;,]/', $rawGroup);
                    $subGroupParts = ! empty($rawSubGroup) ? preg_split('/[;,]/', $rawSubGroup) : [];

                    if (count($groupParts) > 1) {
                        foreach ($groupParts as $idx => $gPart) {
                            $gPart = trim($gPart);
                            if (empty($gPart)) {
                                continue;
                            }

                            // Jika gPart sudah dalam format "GROUP:SUBGROUP"
                            if (strpos($gPart, ':') !== false) {
                                [$g, $s] = explode(':', $gPart, 2);
                                $item = strtoupper(trim($g));
                                $sub = strtoupper(trim($s));
                                if (! empty($sub)) {
                                    $item .= ':'.$sub;
                                }
                                $finalGroups[] = $item;
                            } else {
                                $sPart = isset($subGroupParts[$idx]) ? strtoupper(trim($subGroupParts[$idx])) : '';
                                $item = strtoupper($gPart);
                                if (! empty($sPart)) {
                                    $item .= ':'.$sPart;
                                }
                                $finalGroups[] = $item;
                            }
                        }
                    } else {
                        // Format tunggal per entry
                        if (strpos($rawGroup, ':') !== false) {
                            [$g, $s] = explode(':', $rawGroup, 2);
                            $mainG = strtoupper(trim($g));
                            $subG = ! empty($rawSubGroup) ? strtoupper(trim($rawSubGroup)) : strtoupper(trim($s));
                            $item = $mainG;
                            if (! empty($subG)) {
                                $item .= ':'.$subG;
                            }
                            $finalGroups[] = $item;
                        } else {
                            $mainG = strtoupper(trim($rawGroup));
                            $subG = strtoupper(trim($rawSubGroup));
                            $item = $mainG;
                            if (! empty($subG)) {
                                $item .= ':'.$subG;
                            }
                            $finalGroups[] = $item;
                        }
                    }
                }

                if (! empty($finalGroups)) {
                    DB::transaction(function () use ($karyawan, $finalGroups) {
                        $employee = Karyawan::whereKey($karyawan->id)->lockForUpdate()->firstOrFail();
                        $existingGroups = $employee->grup;
                        if (! is_array($existingGroups)) {
                            $legacyValue = $existingGroups ?? $employee->getRawOriginal('grup');
                            $existingGroups = is_string($legacyValue) && trim($legacyValue) !== '' ? [$legacyValue] : [];
                        }

                        // Keep the old values and order, and deduplicate complete pairs.
                        $groups = $existingGroups;
                        $seen = [];
                        foreach ($existingGroups as $group) {
                            if (is_string($group)) {
                                $seen[$this->groupKey($group)] = true;
                            }
                        }
                        foreach ($finalGroups as $group) {
                            $key = $this->groupKey($group);
                            if ($key !== '' && ! isset($seen[$key])) {
                                $groups[] = $group;
                                $seen[$key] = true;
                            }
                        }
                        if ($groups !== $existingGroups) {
                            $employee->grup = array_values($groups);
                            $employee->save();
                        }
                    });
                }
                $this->successCount++;
            } catch (\Exception $e) {
                $this->failedRows[] = [
                    'row' => $rowNumbers,
                    'nik' => $nik,
                    'reason' => 'Gagal menyimpan data group: '.$e->getMessage(),
                ];
            }
        }
    }

    private function groupKey(string $group): string
    {
        return implode(':', array_map(fn ($part) => strtoupper(trim($part)), explode(':', $group, 2)));
    }
}
