<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Karyawan;
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
                'nomor_seri' => $row['nomor_seri'],
                'karyawan_id' => Karyawan::where('nik', $row['nik_pemegang'])->firstOrFail()->id,
                'kategori' => $kategori,
                'tanggal_perolehan' => $tanggalPerolehan,
                'kondisi' => $kondisi,
                'status' => $status,
                'vendor' => $row['vendor'] ?? null,
                'nomor_faktur' => $row['nomor_faktur'] ?? null,
                'keterangan' => $row['keterangan'] ?? null,
                'created_by' => $userId,
            ]);
        }
    }

    public function prepareForValidation($data, $index)
    {
        $data['nomor_seri'] = trim((string) ($data['nomor_seri'] ?? ''));
        $data['nik_pemegang'] = trim((string) ($data['nik_pemegang'] ?? ''));
        $data['nama_pemegang'] = trim((string) ($data['nama_pemegang'] ?? ''));

        return $data;
    }

    public function rules(): array
    {
        return [
            'nama_asset' => 'required|string',
            'nomor_seri' => 'required|string|max:100',
            'nik_pemegang' => 'bail|required|string|exists:karyawans,nik',
            'nama_pemegang' => 'required|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($validator->getData() as $index => $row) {
                if ($validator->errors()->has($index.'.nik_pemegang') || $validator->errors()->has($index.'.nama_pemegang')) {
                    continue;
                }

                $namaKaryawan = Karyawan::where('nik', $row['nik_pemegang'])->value('nama_lengkap');

                if (mb_strtolower(trim((string) $namaKaryawan)) !== mb_strtolower($row['nama_pemegang'])) {
                    $validator->errors()->add($index.'.nama_pemegang', 'Nama pemegang asset tidak sesuai dengan NIK pada data karyawan.');
                }
            }
        });
    }

    public function customValidationMessages(): array
    {
        return [
            'nomor_seri.required' => 'No Seri/No Mesin wajib diisi.',
            'nomor_seri.max' => 'No Seri/No Mesin maksimal 100 karakter.',
            'nik_pemegang.required' => 'NIK pemegang asset wajib diisi.',
            'nik_pemegang.exists' => 'NIK pemegang asset tidak ditemukan pada data karyawan.',
            'nama_pemegang.required' => 'Nama pemegang asset wajib diisi.',
            'nama_pemegang.max' => 'Nama pemegang asset maksimal 255 karakter.',
        ];
    }
}
