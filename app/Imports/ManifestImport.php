<?php

namespace App\Imports;

use App\Models\Manifest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ManifestImport
{
    protected $errors = [];
    protected $successCount = 0;
    protected $namaKapal;
    protected $noVoyage;

    public function __construct($namaKapal, $noVoyage)
    {
        $this->namaKapal = $namaKapal;
        $this->noVoyage = $noVoyage;
    }

    public function import($file)
    {
        // Handle CSV or Excel import
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
        // Clean the data - sesuaikan dengan kolom Excel
        // Format: No BL | No Manifest | No Kontainer | No Seal | Tipe | Size | Nama Barang | Pengirim | Penerima | Term
        $nomorBl = trim($row[0] ?? '');
        $nomorManifest = trim($row[1] ?? '');
        $nomorKontainer = trim($row[2] ?? '');
        $noSeal = trim($row[3] ?? '');
        $tipeKontainer = trim($row[4] ?? '');
        $sizeKontainer = trim($row[5] ?? '');
        $namaBarang = trim($row[6] ?? '');
        $pengirim = trim($row[7] ?? '');
        $penerima = trim($row[8] ?? '');
        $term = trim($row[9] ?? '');

        // Validate row data
        $validator = Validator::make([
            'nomor_bl' => $nomorBl,
            'nomor_manifest' => $nomorManifest,
            'nomor_kontainer' => $nomorKontainer,
            'no_seal' => $noSeal,
            'tipe_kontainer' => $tipeKontainer,
            'size_kontainer' => $sizeKontainer,
            'nama_barang' => $namaBarang,
            'pengirim' => $pengirim,
            'penerima' => $penerima,
            'term' => $term,
        ], [
            'nomor_bl' => 'required|string|max:255',
            'nomor_kontainer' => 'required|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|string|max:50',
            'nama_barang' => 'nullable|string|max:1000',
            'pengirim' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
            return;
        }

        try {
            // Check if manifest with same nomor_bl and nomor_kontainer already exists for this ship
            $existing = Manifest::where('nomor_bl', $nomorBl)
                ->where('nomor_kontainer', $nomorKontainer)
                ->where('nama_kapal', $this->namaKapal)
                ->where('no_voyage', $this->noVoyage)
                ->first();

            if ($existing) {
                // Update existing manifest
                $existing->update([
                    'nomor_manifest' => $nomorManifest ?: $existing->nomor_manifest,
                    'no_seal' => $noSeal ?: $existing->no_seal,
                    'tipe_kontainer' => $tipeKontainer ?: $existing->tipe_kontainer,
                    'size_kontainer' => $sizeKontainer ?: $existing->size_kontainer,
                    'nama_barang' => $namaBarang ?: $existing->nama_barang,
                    'pengirim' => $pengirim ?: $existing->pengirim,
                    'penerima' => $penerima ?: $existing->penerima,
                    'term' => $term ?: $existing->term,
                ]);
            } else {
                // Create new manifest
                Manifest::create([
                    'nomor_bl' => $nomorBl,
                    'nomor_manifest' => $nomorManifest,
                    'nomor_kontainer' => $nomorKontainer,
                    'no_seal' => $noSeal,
                    'nama_kapal' => $this->namaKapal,
                    'no_voyage' => $this->noVoyage,
                    'tipe_kontainer' => $tipeKontainer,
                    'size_kontainer' => $sizeKontainer,
                    'nama_barang' => $namaBarang,
                    'pengirim' => $pengirim,
                    'penerima' => $penerima,
                    'term' => $term,
                    'input_by' => Auth::id(),
                ]);
            }

            $this->successCount++;
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}
