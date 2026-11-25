<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\KaryawanTidakTetap;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class BpjsFormulaEmployees
{
    public static function catalog()
    {
        $employees = collect();
        $columns = ['id', 'nik', 'nama_lengkap', 'group_jkn', 'group_bp_jamsostek', 'tipe_jkn', 'tipe_bp_jamsostek', 'cabang_bpjs'];
        foreach ([Karyawan::class, KaryawanTidakTetap::class] as $model) {
            if (! Schema::hasColumns((new $model)->getTable(), $columns)) {
                continue;
            }
            $employees = $employees->concat($model::orderBy('nama_lengkap')->get($columns)->map(function ($employee) use ($model) {
                $employee->unique_id = class_basename($model).'_'.$employee->id;
                $employee->tipe_label = $model === Karyawan::class ? 'Tetap' : 'Tidak Tetap';

                return $employee;
            }));
        }

        return $employees->values();
    }

    public static function validateTargets(?string $json, string $jenis, array $groups, ?string $type, ?string $branch): array
    {
        $targets = json_decode($json ?? '[]', true);
        if (! is_array($targets) || ! array_is_list($targets) || count($targets) > 10000) {
            throw ValidationException::withMessages(['employee_targets' => 'Pilihan karyawan tidak valid.']);
        }
        $catalog = self::catalog()->keyBy('unique_id');
        $normalize = fn ($value) => KaryawanBpjsGroups::normalize((string) $value);
        $groupColumn = $jenis === 'jkn' ? 'group_jkn' : 'group_bp_jamsostek';
        $typeColumn = $jenis === 'jkn' ? 'tipe_jkn' : 'tipe_bp_jamsostek';
        foreach ($targets as $id) {
            $employee = is_string($id) ? $catalog->get($id) : null;
            if (! $employee || ! in_array($normalize($employee->{$groupColumn}), array_map($normalize, $groups), true)
                || ($type && $normalize($employee->{$typeColumn}) !== $normalize($type))
                || ($branch && $normalize($employee->cabang_bpjs) !== $normalize($branch))) {
                throw ValidationException::withMessages(['employee_targets' => 'Karyawan terpilih harus sesuai dengan Group, tipe, dan cabang rumus.']);
            }
        }

        return array_values(array_unique($targets));
    }
}
