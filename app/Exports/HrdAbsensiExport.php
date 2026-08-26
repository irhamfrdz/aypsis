<?php

namespace App\Exports;

use App\Exports\Hrd\Sheets\RekapLengkapSheet;
use App\Exports\Hrd\Sheets\TerlambatSheet;
use App\Exports\Hrd\Sheets\PulangCepatSheet;
use App\Exports\Hrd\Sheets\TidakHadirSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HrdAbsensiExport implements WithMultipleSheets
{
    use Exportable;

    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new RekapLengkapSheet($this->startDate, $this->endDate),
            new TerlambatSheet($this->startDate, $this->endDate),
            new PulangCepatSheet($this->startDate, $this->endDate),
            new TidakHadirSheet($this->startDate, $this->endDate),
        ];
    }
}
