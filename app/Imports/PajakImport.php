<?php

namespace App\Imports;

use App\Models\Pajak;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

class PajakImport
{
    protected $errors = [];
    protected $successCount = 0;

    public function import($file)
    {
        // For now, handle CSV import
        // In production, you would use a proper Excel library
        $data = $this->parseCsvFile($file);

        if (empty($data)) {
            $this->errors[] = 'File kosong atau tidak dapat dibaca';
            return false;
        }

        // Skip header row
        array_shift($data);

        foreach ($data as $rowIndex => $row) {
            $this->processRow($row, $rowIndex + 2); // +2 because we skip header and array is 0-indexed
        }

        return [
            'success_count' => $this->successCount,
            'errors' => $this->errors
        ];
    }

    private function parseCsvFile($file)
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');

        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle); // No BOM, rewind to start
        }

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            $data[] = $row;
        }

        fclose($handle);
        return $data;
    }

    private function processRow($row, $rowNumber)
    {
        // Clean the data
        $namaStatus = trim($row[0] ?? '');
        $keterangan = trim($row[1] ?? '');

        // Validate row data
        $validator = Validator::make([
            'nama_status' => $namaStatus,
            'keterangan' => $keterangan
        ], [
            'nama_status' => 'required|string|max:255|unique:pajaks,nama_status',
            'keterangan' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
            return;
        }

        try {
            Pajak::create([
                'nama_status' => $namaStatus,
                'keterangan' => $keterangan
            ]);

            $this->successCount++;
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: Gagal menyimpan data - " . $e->getMessage();
        }
    }
}
