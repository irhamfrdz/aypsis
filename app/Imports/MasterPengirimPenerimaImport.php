<?php

namespace App\Imports;

use App\Models\MasterPengirimPenerima;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class MasterPengirimPenerimaImport
{
    protected $errors = [];
    protected $successCount = 0;

    public function import($file)
    {
        // For now, handle CSV import
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
        $nama = trim($row[0] ?? '');
        $alamat = trim($row[1] ?? '');
        $npwp = trim($row[2] ?? '');

        // Validate row data
        $validator = Validator::make([
            'nama' => $nama,
            'alamat' => $alamat,
            'npwp' => $npwp
        ], [
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
            return;
        }

        try {
            // Generate kode otomatis
            $kode = MasterPengirimPenerima::generateKode();
            
            MasterPengirimPenerima::create([
                'kode' => $kode,
                'nama' => $nama,
                'alamat' => $alamat ?: null,
                'npwp' => $npwp ?: null,
                'status' => 'active',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            $this->successCount++;
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: Gagal menyimpan data - " . $e->getMessage();
        }
    }
}
