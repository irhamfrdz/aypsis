<?php

namespace App\Imports;

use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AbsensiKoreksiImport implements ToCollection, WithStartRow
{
    public $importedCount = 0;
    protected $tipeAbsen;
    protected $tanggalAbsensi;
    protected $employees;

    public function __construct($tipeAbsen, $tanggalAbsensi)
    {
        $this->tipeAbsen = $tipeAbsen;
        $this->tanggalAbsensi = $tanggalAbsensi;
        
        // Cache employees
        $this->employees = Karyawan::select('id', 'nik')
            ->whereNotNull('nik')
            ->get()
            ->pluck('id', 'nik')
            ->toArray();
    }

    public function startRow(): int
    {
        return 2; // Lewati baris pertama (Header)
    }

    public function collection(Collection $rows)
    {
        $tipeMasuk = ($this->tipeAbsen === 'Reguler') ? 'Masuk' : 'Lembur Masuk';
        $tipePulang = ($this->tipeAbsen === 'Reguler') ? 'Pulang' : 'Lembur_Pulang';

        foreach ($rows as $row) {
            $nikRaw = trim($row[0] ?? '');
            $tanggalRaw = trim($row[2] ?? '');
            $timeMasukRaw = trim($row[3] ?? '');
            $timePulangRaw = trim($row[4] ?? '');

            if (empty($nikRaw) || (empty($timeMasukRaw) && empty($timePulangRaw))) {
                continue; // Lewati jika NIK kosong atau kedua jam kosong
            }

            // Normalisasi NIK
            $nik = is_numeric($nikRaw) ? str_pad($nikRaw, 4, '0', STR_PAD_LEFT) : $nikRaw;
            
            // Format Tanggal (gunakan dari excel jika ada, jika tidak fallback ke form UI)
            $activeDate = $this->tanggalAbsensi;
            if (!empty($tanggalRaw)) {
                try {
                    if (is_numeric($tanggalRaw)) {
                        $activeDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalRaw)->format('Y-m-d');
                    } else {
                        $activeDate = Carbon::parse($tanggalRaw)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $activeDate = $this->tanggalAbsensi;
                }
            }

            // Proses Jam Masuk
            if (!empty($timeMasukRaw)) {
                $this->processWaktu($nik, $activeDate, $timeMasukRaw, $tipeMasuk);
            }

            // Proses Jam Pulang
            if (!empty($timePulangRaw)) {
                $this->processWaktu($nik, $activeDate, $timePulangRaw, $tipePulang);
            }
        }
    }

    private function processWaktu($nik, $activeDate, $timeRaw, $tipe)
    {
        try {
            if (is_numeric($timeRaw) && !str_contains($timeRaw, ':')) { 
                $parsedTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($timeRaw)->format('H:i:s');
            } else if (strlen($timeRaw) > 8 && strpos($timeRaw, ' ') !== false) {
                $parsedTime = Carbon::parse($timeRaw)->format('H:i:s');
            } else {
                $parsedTime = Carbon::parse($timeRaw)->format('H:i:s');
            }
            
            $datetime = Carbon::parse($activeDate . ' ' . $parsedTime)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return; // Abaikan jika tidak valid
        }

        $dateOnly = Carbon::parse($activeDate)->format('Y-m-d');
        
        $existingAbsensi = Absensi::where('nik', $nik)
            ->where('tipe', $tipe)
            ->whereDate('waktu', $dateOnly)
            ->first();

        if ($existingAbsensi) {
            $existingAbsensi->update([
                'waktu' => $datetime,
                'keterangan' => 'Koreksi via Excel Import',
                'verify_mode' => '1',
            ]);
        } else {
            Absensi::create([
                'nik' => $nik,
                'waktu' => $datetime,
                'tipe' => $tipe,
                'karyawan_id' => $this->employees[$nik] ?? null,
                'keterangan' => 'Koreksi via Excel Import',
                'verify_mode' => '1',
            ]);
        }
        
        $this->importedCount++;
    }
}
