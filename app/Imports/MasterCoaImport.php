<?php

namespace App\Imports;

use App\Models\Coa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

class MasterCoaImport
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
        $nomor_akun = trim($row[0] ?? '');
        $nama_akun = trim($row[1] ?? '');
        $tipe_akun = trim($row[2] ?? '');
        $saldo = trim($row[3] ?? '');

        // Validate row data
        $validator = Validator::make([
            'nomor_akun' => $nomor_akun,
            'nama_akun' => $nama_akun,
            'tipe_akun' => $tipe_akun,
            'saldo' => $saldo
        ], [
            'nomor_akun' => 'required|string|max:20|unique:akun_coa,nomor_akun',
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string|max:50',
            'saldo' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
            return;
        }

        try {
            Coa::create([
                'nomor_akun' => $nomor_akun,
                'nama_akun' => $nama_akun,
                'tipe_akun' => $tipe_akun,
                'saldo' => $saldo ?: 0
            ]);

            $this->successCount++;
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: Gagal menyimpan data - " . $e->getMessage();
        }
    }
}
