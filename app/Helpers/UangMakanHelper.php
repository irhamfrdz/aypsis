<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class UangMakanHelper
{
    public static function includesSundayAttendance(Model $karyawan): bool
    {
        return self::isNominalTetap($karyawan)
            || strtoupper(trim((string) ($karyawan->departemen ?? ''))) === 'JAKARTA SATPAM';
    }

    public static function isNominalTetap(Model $karyawan): bool
    {
        $departemen = strtoupper(trim((string) ($karyawan->departemen ?? '')));

        return in_array($departemen, ['JAKARTA SATPAM PEL', 'JAKARTA SATPAM PELABUHAN'], true);
    }
}
