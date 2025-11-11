<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class MobilImport implements ToArray
{
    /**
     * Convert Excel data to array
     */
    public function array(array $array): array
    {
        return $array;
    }
}