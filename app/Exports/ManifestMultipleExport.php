<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ManifestMultipleExport implements WithMultipleSheets
{
    protected $manifests;

    public function __construct($manifests)
    {
        $this->manifests = $manifests;
    }

    public function sheets(): array
    {
        return [
            new ManifestTableExport($this->manifests),
            new ShipperConsigneeSheetExport()
        ];
    }
}
