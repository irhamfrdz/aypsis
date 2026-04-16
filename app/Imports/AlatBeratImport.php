<?php

namespace App\Imports;

use App\Models\AlatBerat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class AlatBeratImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $lastAlat = AlatBerat::orderBy('id', 'desc')->first();
        $nextNumber = 1;

        if ($lastAlat) {
            $lastCode = $lastAlat->kode_alat;
            if (preg_match('/^AB(\d+)$/', $lastCode, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                // If the last code format is unexpected, just count all records
                $nextNumber = AlatBerat::count() + 1;
            }
        }

        foreach ($rows as $row) {
            $kodeAlat = $row['kode_alat'] ?? null;

            if (empty($kodeAlat)) {
                $kodeAlat = 'AB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $nextNumber++;
            }

            AlatBerat::create([
                'kode_alat'  => $kodeAlat,
                'nama'       => $row['nama'],
                'nickname'   => $row['nickname'] ?? null,
                'jenis'      => $row['jenis'] ?? null,
                'merk'       => $row['merk'] ?? null,
                'tipe'       => $row['tipe'] ?? null,
                'kapasitas'  => $row['kapasitas'] ?? null,
                'nomor_seri' => $row['nomor_seri'] ?? null,
                'tahun_pembuatan' => $row['tahun_pembuatan'] ?? null,
                'lokasi'     => $row['lokasi'] ?? null,
                'tarif_harian'  => $row['tarif_harian'] ?? null,
                'tarif_bulanan' => $row['tarif_bulanan'] ?? null,
                'status'     => strtolower($row['status'] ?? 'active'),
                'keterangan' => $row['keterangan'] ?? null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'status' => 'nullable|in:active,inactive,maintenance,Active,Inactive,Maintenance,ACTIVE,INACTIVE,MAINTENANCE',
            'nomor_seri' => 'nullable|unique:alat_berats,nomor_seri',
            'tarif_harian' => 'nullable|numeric|min:0',
            'tarif_bulanan' => 'nullable|numeric|min:0',
        ];
    }
}
