<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KaryawanAssetService
{
    public function forKaryawan(int $karyawanId): Collection
    {
        $connection = DB::connection();
        $schema = $connection->getSchemaBuilder();

        // MySQL can discover matching tables without inspecting every table individually.
        if ($connection->getDriverName() === 'mysql') {
            $tables = $connection->table('information_schema.columns')
                ->where('table_schema', $connection->getDatabaseName())
                ->where('column_name', 'is_asset_karyawan')
                ->select('TABLE_NAME as asset_table')
                ->pluck('asset_table')->all();
        } else {
            $tables = array_column($schema->getTables(), 'name');
        }

        $assets = collect();
        $displayColumns = [
            'kode_no', 'kode_asset', 'kode_aset', 'kode_barang',
            'nomor_polisi', 'nomor_seri', 'serial_number',
            'nama_asset', 'nama_aset', 'nama_barang', 'nama', 'nickname',
            'merek', 'jenis', 'lokasi', 'keterangan', 'catatan',
            'kategori_asset', 'tanggal_tanda_terima_asset', 'kondisi_fisik',
        ];

        foreach ($tables as $table) {
            $table = Str::after($table, $connection->getTablePrefix());
            $columns = $schema->getColumnListing($table);
            if (! in_array('is_asset_karyawan', $columns) || ! in_array('karyawan_id', $columns)) {
                continue;
            }

            $selectedColumns = array_values(array_intersect(array_merge(['id'], $displayColumns), $columns));
            $query = $connection->table($table)
                ->where('is_asset_karyawan', 1)->where('karyawan_id', $karyawanId);
            if (in_array('deleted_at', $columns)) {
                $query->whereNull('deleted_at');
            }

            foreach ($query->get($selectedColumns ?: ['karyawan_id']) as $row) {
                $data = (array) $row;
                $source = Str::headline(Str::singular($table));
                $firstValue = function (array $keys) use ($data) {
                    foreach ($keys as $key) {
                        if (isset($data[$key]) && trim((string) $data[$key]) !== '') {
                            return $data[$key];
                        }
                    }

                    return null;
                };

                $details = collect($data)->except([
                    'id', 'karyawan_id', 'kode_no', 'kode_asset', 'kode_aset', 'kode_barang',
                    'nama_asset', 'nama_aset', 'nama_barang', 'nama', 'nickname', 'catatan',
                    'kategori_asset', 'tanggal_tanda_terima_asset', 'kondisi_fisik',
                ])->filter(fn ($value) => $value !== null && trim((string) $value) !== '')
                    ->map(fn ($value, $key) => Str::headline($key).': '.$value)->implode(' · ');

                $assets->push([
                    'sumber' => $source,
                    'jenis' => $firstValue(['kategori_asset']) ?? '-',
                    'tanggal_tanda_terima_asset' => $firstValue(['tanggal_tanda_terima_asset']),
                    'kondisi_fisik' => $firstValue(['kondisi_fisik']) ?? '-',
                    'kode' => $firstValue(['kode_no', 'kode_asset', 'kode_aset', 'kode_barang', 'nomor_polisi', 'nomor_seri', 'serial_number', 'id']) ?? '-',
                    'nama' => $firstValue(['catatan']) ?? '-',
                    'detail' => $details ?: '-',
                ]);
            }
        }

        return $assets->sortBy(fn ($asset) => $asset['sumber'].' '.$asset['kode'])->values();
    }
}
