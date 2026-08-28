<?php

namespace App\Imports;

use App\Models\MasterPengirimPenerima;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class MasterPengirimPenerimaUpdateImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;
    public $errors = [];

    public function collection(Collection $rows)
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;

            // Maatwebsite Excel slugifies the headings, so 'KODE' -> 'kode', 'CONTACT PERSON' -> 'contact_person'
            $kode = trim($row['kode'] ?? '');

            if (empty($kode)) {
                $this->errors[] = "Baris {$rowNumber}: Kolom KODE kosong.";
                continue;
            }

            $pengirim = MasterPengirimPenerima::where('kode', $kode)->first();

            if (!$pengirim) {
                $this->errors[] = "Baris {$rowNumber}: Data dengan KODE {$kode} tidak ditemukan.";
                continue;
            }

            $updated = false;

            // NAMA
            $namaExcel = trim($row['nama'] ?? '');
            if (empty($pengirim->nama) && !empty($namaExcel)) {
                $pengirim->nama = $namaExcel;
                $updated = true;
            }

            // PIC
            $picExcel = trim($row['pic'] ?? '');
            if (empty($pengirim->pic) && !empty($picExcel)) {
                $pengirim->pic = $picExcel;
                $updated = true;
            }

            // TELEPON
            $teleponExcel = trim($row['telepon'] ?? '');
            if (empty($pengirim->telepon) && !empty($teleponExcel)) {
                $pengirim->telepon = $teleponExcel;
                $updated = true;
            }

            // CONTACT PERSON
            $cpExcel = trim($row['contact_person'] ?? '');
            if (empty($pengirim->contact_person) && !empty($cpExcel)) {
                $pengirim->contact_person = $cpExcel;
                $updated = true;
            }

            if ($updated) {
                try {
                    $pengirim->updated_by = Auth::id();
                    $pengirim->save();
                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$rowNumber}: Gagal update data {$kode} - " . $e->getMessage();
                }
            }
        }
    }
}
