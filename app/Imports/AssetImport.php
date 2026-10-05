<?php

namespace App\Imports;

use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        $userId = Auth::id();

        foreach ($rows as $row) {
            $kodeAsset = trim((string) ($row['kode_asset'] ?? ''));

            if (empty($kodeAsset) || str_contains(strtolower($kodeAsset), 'kosongkan') || str_contains(strtolower($kodeAsset), 'auto')) {
                $kodeAsset = Asset::generateNextKode();
            }

            // Parse date if given
            $tanggalPerolehan = null;
            if (! empty($row['tanggal_perolehan'])) {
                try {
                    if (is_numeric($row['tanggal_perolehan'])) {
                        $tanggalPerolehan = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_perolehan'])->format('Y-m-d');
                    } else {
                        $tanggalPerolehan = Carbon::parse($row['tanggal_perolehan'])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $tanggalPerolehan = null;
                }
            }

            $nilaiPerolehan = isset($row['nilai_perolehan']) && is_numeric($row['nilai_perolehan']) ? (float) $row['nilai_perolehan'] : 0;
            $nilaiResidu = isset($row['nilai_residu']) && is_numeric($row['nilai_residu']) ? (float) $row['nilai_residu'] : 0;
            $nilaiBuku = $nilaiPerolehan; // default to perolehan on import if not yet depreciated

            $status = ! empty($row['status']) ? ucwords(strtolower(trim((string) $row['status']))) : 'Tersedia';
            if (! in_array($status, Asset::STATUS_OPTIONS)) {
                $status = 'Tersedia';
            }

            $kondisi = ! empty($row['kondisi']) ? ucwords(strtolower(trim((string) $row['kondisi']))) : 'Baik';
            if (! in_array($kondisi, Asset::KONDISI_OPTIONS)) {
                $kondisi = 'Baik';
            }

            $kategori = ! empty($row['kategori']) ? trim((string) $row['kategori']) : 'Lain-lain';

            Asset::create([
                'kode_asset' => $kodeAsset,
                'nama_asset' => $row['nama_asset'],
                'kategori' => $kategori,
                'merk' => $row['merk'] ?? null,
                'tipe_model' => $row['tipe_model'] ?? null,
                'nomor_seri' => $row['nomor_seri'] ?? null,
                'lokasi' => $row['lokasi'] ?? null,
                'tanggal_perolehan' => $tanggalPerolehan,
                'nilai_perolehan' => $nilaiPerolehan,
                'masa_manfaat_bulan' => isset($row['masa_manfaat_bulan']) && is_numeric($row['masa_manfaat_bulan']) ? (int) $row['masa_manfaat_bulan'] : null,
                'nilai_residu' => $nilaiResidu,
                'nilai_buku' => $nilaiBuku,
                'kondisi' => $kondisi,
                'status' => $status,
                'penanggung_jawab' => $row['penanggung_jawab'] ?? null,
                'vendor' => $row['vendor'] ?? null,
                'nomor_faktur' => $row['nomor_faktur'] ?? null,
                'keterangan' => $row['keterangan'] ?? null,
                'created_by' => $userId,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama_asset' => 'required|string',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'masa_manfaat_bulan' => 'nullable|numeric|min:0',
        ];
    }
}
