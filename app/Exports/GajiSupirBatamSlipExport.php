<?php

namespace App\Exports;

use App\Models\GajiSupirBatam;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class GajiSupirBatamSlipExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly GajiSupirBatam $gaji,
        private readonly array $waybills,
        private readonly Collection $bensinList,
    ) {}

    public function view(): View
    {
        return view('gaji-supir-batam.excel', [
            'gaji' => $this->gaji,
            'waybills' => $this->waybills,
            'bensinList' => $this->bensinList,
        ]);
    }

    public function title(): string
    {
        return 'Gaji '.$this->gaji->id;
    }
}
