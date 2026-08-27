<?php

namespace App\Imports;

use App\Models\ShipperConsignee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ShipperContactImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $shipperName = $row['shipper'] ?? null;
            $contactPerson = $row['contact_person'] ?? null;

            if ($shipperName && $contactPerson) {
                ShipperConsignee::where('shipper', $shipperName)
                    ->update(['contact_person' => $contactPerson]);
            }
        }
    }
}
