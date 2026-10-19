<?php

namespace App\Imports;

use App\Models\PenerimaBuktiPotongPph;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;

class PenerimaBuktiPotongPphImport implements ToCollection
{
    public int $count = 0;

    public function collection(Collection $rows): void
    {
        $header = $rows->first()?->map(fn ($value) => mb_strtolower(trim((string) $value)))->take(4)->all();

        if (array_values($header ?? []) !== ['nama', 'nik/npwp', 'pph', 'grup']) {
            throw ValidationException::withMessages([
                'file' => 'Header Excel harus berurutan: Nama, NIK/NPWP, PPh, Grup. Unduh template terbaru.',
            ]);
        }

        $records = [];
        $errors = [];

        foreach ($rows->skip(1) as $index => $row) {
            $values = array_map(fn ($value) => trim((string) $value), array_pad($row->take(4)->all(), 4, ''));

            if (count(array_filter($values, fn ($value) => $value !== '')) === 0) {
                continue;
            }

            $data = array_combine(['nama', 'nik_npwp', 'pph', 'grup'], $values);
            $validator = Validator::make($data, [
                'nama' => ['required', 'string', 'max:255'],
                'nik_npwp' => ['nullable', 'string', 'max:64'],
                'pph' => ['nullable', 'string', 'max:100'],
                'grup' => ['nullable', 'string', 'max:100'],
            ]);

            if ($validator->fails()) {
                $errors[] = 'Baris '.($index + 1).': '.$validator->errors()->first();

                continue;
            }

            $records[] = $validator->validated();
        }

        if ($errors) {
            throw ValidationException::withMessages(['file' => $errors]);
        }

        if (! $records) {
            throw ValidationException::withMessages(['file' => 'File Excel tidak berisi data penerima.']);
        }

        foreach ($records as $record) {
            PenerimaBuktiPotongPph::create($record);
            $this->count++;
        }
    }
}
