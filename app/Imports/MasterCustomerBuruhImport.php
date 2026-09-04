<?php

namespace App\Imports;

use App\Models\MasterCustomerBuruh;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasterCustomerBuruhImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        // Get the last ID to continue numbering
        $lastCustomer = MasterCustomerBuruh::orderBy('id', 'desc')->first();
        $lastId = $lastCustomer ? intval(substr($lastCustomer->kode, 4)) : 0;

        foreach ($rows as $row) {
            // Ensure nama_customer is present
            if (empty($row['nama_customer'])) {
                continue;
            }

            $lastId++;
            $kode = 'CBB-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);

            MasterCustomerBuruh::create([
                'kode'           => $kode,
                'nama_customer'  => $row['nama_customer'],
                'bank'           => $row['bank'] ?? null,
                'nomor_rekening' => $row['nomor_rekening'] ?? null,
                'penerima'       => $row['penerima'] ?? null,
                'is_active'      => true,
            ]);
        }
    }
}