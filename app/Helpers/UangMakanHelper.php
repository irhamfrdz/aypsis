<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class UangMakanHelper
{
    public static function isNominalTetap(Model $karyawan): bool
    {
        $departemen = strtoupper(trim((string) ($karyawan->departemen ?? '')));

        return in_array($departemen, ['JAKARTA SATPAM PEL', 'JAKARTA SATPAM PELABUHAN'], true);
    }
}
