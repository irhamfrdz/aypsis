<?php

namespace App\Imports;

use App\Models\KaryawanTidakTetap;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KaryawanTidakTetapImport
{
    protected $errors = [];
    protected $successCount = 0;

    public function import($file)
    {
        $data = $this->parseExcelFile($file);

        if (empty($data)) {
            $this->errors[] = 'File kosong atau tidak dapat dibaca';
            return false;
        }

        // Skip header row
        array_shift($data);

        foreach ($data as $rowIndex => $row) {
            $this->processRow($row, $rowIndex + 2); // +2 because header is row 1, and array is 0-indexed
        }

        return [
            'success_count' => $this->successCount,
            'errors' => $this->errors
        ];
    }

    private function parseExcelFile($file)
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = [];

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];

                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                $data[] = $rowData;
            }

            return $data;
        } catch (\Exception $e) {
            $this->errors[] = 'Error membaca file Excel: ' . $e->getMessage();
            return [];
        }
    }

    private function processRow($row, $rowNumber)
    {
        // Expected Columns:
        // 0: NIK (Required)
        // 1: Nama Lengkap (Required)
        // 2: Nama Panggilan
        // 3: Divisi
        // 4: Pekerjaan
        // 5: Cabang
        // 6: NIK KTP
        // 7: Jenis Kelamin
        // 8: Agama
        // 9: RT/RW
        // 10: Alamat Lengkap
        // 11: Kelurahan
        // 12: Kecamatan
        // 13: Kabupaten
        // 14: Provinsi
        // 15: Kode Pos
        // 16: Email
        // 17: Tanggal Masuk (YYYY-MM-DD or Excel Date)
        // 18: Status Pajak

        $nik = trim($row[0] ?? '');
        $namaLengkap = trim($row[1] ?? '');
        
        // Skip empty rows
        if (empty($nik) && empty($namaLengkap)) {
            return;
        }

        $namaPanggilan = trim($row[2] ?? '');
        $divisi = trim($row[3] ?? 'NON KARYAWAN');
        $pekerjaan = trim($row[4] ?? '');
        $cabang = trim($row[5] ?? '');
        $nikKtp = trim($row[6] ?? '');
        $jenisKelamin = trim($row[7] ?? '');
        $agama = trim($row[8] ?? '');
        $rtRw = trim($row[9] ?? '');
        $alamatLengkap = trim($row[10] ?? '');
        $kelurahan = trim($row[11] ?? '');
        $kecamatan = trim($row[12] ?? '');
        $kabupaten = trim($row[13] ?? '');
        $provinsi = trim($row[14] ?? '');
        $kodePos = trim($row[15] ?? '');
        $email = trim($row[16] ?? '');
        $tanggalMasuk = $row[17] ?? null;
        $statusPajak = trim($row[18] ?? '');

        // Handle Date
        if (!empty($tanggalMasuk)) {
            if (is_numeric($tanggalMasuk)) {
                $tanggalMasuk = Date::excelToDateTimeObject($tanggalMasuk)->format('Y-m-d');
            } else {
                try {
                    $tanggalMasuk = \Carbon\Carbon::parse($tanggalMasuk)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalMasuk = null;
                }
            }
        }

        // Validate
        $validator = Validator::make([
            'nik' => $nik,
            'nama_lengkap' => $namaLengkap,
            'email' => $email,
            'nik_ktp' => $nikKtp
        ], [
            'nik' => 'required|unique:karyawan_tidak_tetaps,nik',
            'nama_lengkap' => 'required',
            'email' => 'nullable|email',
            'nik_ktp' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            $this->errors[] = "Baris {$rowNumber} (NIK: {$nik}): " . implode(', ', $validator->errors()->all());
            return;
        }

        try {
            KaryawanTidakTetap::create([
                'nik' => $nik,
                'nama_lengkap' => $namaLengkap,
                'nama_panggilan' => $namaPanggilan,
                'divisi' => $divisi,
                'pekerjaan' => $pekerjaan,
                'cabang' => $cabang,
                'nik_ktp' => $nikKtp,
                'jenis_kelamin' => $jenisKelamin,
                'agama' => $agama,
                'rt_rw' => $rtRw,
                'alamat_lengkap' => $alamatLengkap,
                'kelurahan' => $kelurahan,
                'kecamatan' => $kecamatan,
                'kabupaten' => $kabupaten,
                'provinsi' => $provinsi,
                'kode_pos' => $kodePos,
                'email' => $email,
                'tanggal_masuk' => $tanggalMasuk,
                'status_pajak' => $statusPajak,
            ]);

            $this->successCount++;
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: Gagal menyimpan data - " . $e->getMessage();
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
