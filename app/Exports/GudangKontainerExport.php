<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GudangKontainerExport implements WithMultipleSheets
{
    use Exportable;

    protected $gudangId;

    protected $type;

    public function __construct($gudangId, $type = 'all')
    {
        $this->gudangId = $gudangId;
        $this->type = $type;
    }

    public function sheets(): array
    {
        $sheets = [];

        if ($this->type === 'all') {
            $sheets[] = new GudangKontainerSheetExport($this->gudangId, 'stock', 'Milik Sendiri');
            $sheets[] = new GudangKontainerSheetExport($this->gudangId, 'sewa', 'Sewa');
            $sheets[] = new GudangKontainerSheetExport($this->gudangId, 'all', 'Gabungan');
        } elseif ($this->type === 'stock') {
            $sheets[] = new GudangKontainerSheetExport($this->gudangId, 'stock', 'Milik Sendiri');
        } elseif ($this->type === 'sewa') {
            $sheets[] = new GudangKontainerSheetExport($this->gudangId, 'sewa', 'Sewa');
        }

        return $sheets;
    }
}
