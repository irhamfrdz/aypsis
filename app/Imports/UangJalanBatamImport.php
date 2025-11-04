<?php

namespace App\Imports;

use App\Models\UangJalanBatam;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class UangJalanBatamImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $importedCount = 0;
    protected $skippedCount = 0;

    public function model(array $row)
    {
        try {
            // Parse tanggal
            $tanggalAwal = $this->parseDate($row['tanggal_awal_berlaku']);
            $tanggalAkhir = $this->parseDate($row['tanggal_akhir_berlaku']);

            $this->importedCount++;

            return new UangJalanBatam([
                'wilayah' => $row['wilayah'],
                'rute' => $row['rute'],
                'expedisi' => $row['expedisi'],
                'ring' => $row['ring'],
                'ft' => $row['ft'],
                'f_e' => $row['f_e'],
                'tarif' => (float) str_replace([',', '.'], ['', '.'], $row['tarif']),
                'status' => $row['status'] ?? null,
                'tanggal_awal_berlaku' => $tanggalAwal,
                'tanggal_akhir_berlaku' => $tanggalAkhir,
            ]);
        } catch (\Exception $e) {
            $this->skippedCount++;
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'wilayah' => 'required|string|max:255',
            'rute' => 'required|string|max:255',
            'expedisi' => 'required|string|max:255',
            'ring' => 'required|string|max:255',
            'ft' => 'required|string|max:255',
            'f_e' => 'required|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:aqua,chasis PB',
            'tanggal_awal_berlaku' => 'required|date',
            'tanggal_akhir_berlaku' => 'required|date',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'wilayah.required' => 'Kolom wilayah wajib diisi.',
            'rute.required' => 'Kolom rute wajib diisi.',
            'expedisi.required' => 'Kolom expedisi wajib diisi.',
            'ring.required' => 'Kolom ring wajib diisi.',
            'ft.required' => 'Kolom FT wajib diisi.',
            'f_e.required' => 'Kolom F/E wajib diisi.',
            'tarif.required' => 'Kolom tarif wajib diisi.',
            'tarif.numeric' => 'Kolom tarif harus berupa angka.',
            'tarif.min' => 'Kolom tarif tidak boleh kurang dari 0.',
            'status.in' => 'Status hanya boleh: aqua atau chasis PB.',
            'tanggal_awal_berlaku.required' => 'Tanggal awal berlaku wajib diisi.',
            'tanggal_awal_berlaku.date' => 'Format tanggal awal berlaku tidak valid.',
            'tanggal_akhir_berlaku.required' => 'Tanggal akhir berlaku wajib diisi.',
            'tanggal_akhir_berlaku.date' => 'Format tanggal akhir berlaku tidak valid.',
        ];
    }

    private function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        // Try different date formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y'];
        
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // If all formats fail, try Carbon's parse
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Format tanggal tidak valid: {$date}");
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
