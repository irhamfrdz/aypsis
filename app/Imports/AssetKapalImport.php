<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\MasterKapal;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AssetKapalImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $row = $row->toArray();
            $kodeAsset = trim((string) ($row['kode_asset'] ?? ''));
            if ($kodeAsset === '' || str_contains(strtolower($kodeAsset), 'auto') || str_contains(strtolower($kodeAsset), 'kosongkan')) {
                $kodeAsset = Asset::generateNextKode();
            }

            $kapal = MasterKapal::where('nama_kapal', trim((string) $row['nama_kapal']))->firstOrFail();

            Asset::create([
                'kode_asset' => $kodeAsset,
                'nama_asset' => trim((string) $row['nama_asset']),
                'nama_merk' => $row['nama_merk'] ?? null,
                'spesifikasi' => $row['spesifikasi'] ?? null,
                'nomor_seri' => $row['nomor_seri'] ?? null,
                'kategori' => trim((string) $row['kategori']),
                'lokasi' => 'Di kapal',
                'lokasi_asset' => trim((string) $row['lokasi_asset']),
                'kapal_id' => $kapal->id,
                'tanggal_perolehan' => $this->parseDate($row['tanggal_beli'] ?? null),
                'tanggal_kadaluarsa' => $this->parseDate($row['tanggal_kadaluarsa'] ?? null),
                'kuantitas' => $row['kuantitas'] ?? null,
                'satuan' => $row['satuan'] ?? null,
                'kondisi' => trim((string) $row['kondisi']),
                'status' => ! empty($row['status']) ? $row['status'] : 'Tersedia',
                'vendor' => $row['vendor'] ?? null,
                'nomor_faktur' => $row['nomor_faktur'] ?? null,
                'keterangan' => $row['keterangan'] ?? null,
                'created_by' => Auth::id(),
            ]);
        }
    }

    public function prepareForValidation($data, $index): array
    {
        foreach (['tanggal_beli', 'tanggal_kadaluarsa'] as $dateColumn) {
            if (! empty($data[$dateColumn])) {
                try {
                    $data[$dateColumn] = is_numeric($data[$dateColumn])
                        ? ExcelDate::excelToDateTimeObject($data[$dateColumn])->format('Y-m-d')
                        : Carbon::parse($data[$dateColumn])->format('Y-m-d');
                } catch (\Throwable) {
                    // Leave invalid values for the row validation message.
                }
            }
        }

        foreach (['kode_asset', 'nama_asset', 'nama_kapal', 'kategori', 'lokasi_asset', 'kondisi'] as $column) {
            if (isset($data[$column])) {
                $data[$column] = trim((string) $data[$column]);
            }
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            'kode_asset' => 'nullable|string|max:50|unique:assets,kode_asset',
            'nama_asset' => 'required|string|max:255',
            'nama_kapal' => 'required|string|max:255',
            'nama_merk' => 'nullable|string|max:150',
            'spesifikasi' => 'nullable|string|max:2000',
            'nomor_seri' => 'nullable|string|max:100',
            'kategori' => ['required', Rule::in(Asset::KATEGORI_KAPAL_OPTIONS)],
            'lokasi_asset' => ['required', Rule::in(Asset::LOKASI_ASSET_KAPAL_OPTIONS)],
            'tanggal_beli' => 'nullable|date',
            'tanggal_kadaluarsa' => 'nullable|date',
            'kuantitas' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'kondisi' => ['required', Rule::in(Asset::KONDISI_KAPAL_OPTIONS)],
            'status' => ['nullable', Rule::in(Asset::STATUS_OPTIONS)],
            'vendor' => 'nullable|string|max:150',
            'nomor_faktur' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($validator->getData() as $index => $row) {
                if (empty($row['nama_kapal']) || $validator->errors()->has($index.'.nama_kapal')) {
                    continue;
                }

                if (MasterKapal::where('nama_kapal', trim((string) $row['nama_kapal']))->count() !== 1) {
                    $validator->errors()->add($index.'.nama_kapal', 'Nama kapal tidak ditemukan atau tidak unik pada master kapal.');
                }
            }
        });
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_asset.required' => 'Nama aset wajib diisi.',
            'nama_kapal.required' => 'Nama kapal wajib diisi.',
            'nama_kapal.max' => 'Nama kapal tidak boleh lebih dari 255 karakter.',
            'kategori.required' => 'Kategori aset kapal wajib diisi.',
            'kategori.in' => 'Kategori tidak cocok dengan pilihan kategori untuk aset kapal.',
            'lokasi_asset.required' => 'Lokasi aset di kapal wajib diisi.',
            'lokasi_asset.in' => 'Lokasi asset tidak cocok dengan pilihan lokasi kapal pada template.',
            'kuantitas.numeric' => 'Kuantitas harus berupa angka.',
            'kuantitas.min' => 'Kuantitas tidak boleh kurang dari 0.',
            'tanggal_beli.date' => 'Tanggal beli tidak valid. Gunakan format tanggal Excel atau YYYY-MM-DD.',
            'tanggal_kadaluarsa.date' => 'Tanggal kadaluwarsa tidak valid. Gunakan format tanggal Excel atau YYYY-MM-DD.',
            'kode_asset.unique' => 'Kode aset sudah digunakan.',
            'kondisi.required' => 'Kondisi fisik aset kapal wajib diisi.',
            'kondisi.in' => 'Kondisi tidak sesuai dengan pilihan kondisi fisik untuk aset kapal.',
            'status.in' => 'Status tidak sesuai dengan pilihan yang tersedia.',
        ];
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value)
            ? ExcelDate::excelToDateTimeObject($value)->format('Y-m-d')
            : Carbon::parse($value)->format('Y-m-d');
    }
}
