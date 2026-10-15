<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GajiSupirBatamFilteredExport implements WithMultipleSheets
{
    /** @param GajiSupirBatamSlipExport[] $slips */
    public function __construct(private readonly array $slips) {}

    public function sheets(): array
    {
        return $this->slips;
    }
}
