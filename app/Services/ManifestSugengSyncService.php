<?php

namespace App\Services;

use App\Models\ApprovalTandaTerimaDuaGoods;
use App\Models\ApprovalTandaTerimaDuaShipperOverride;
use App\Models\Manifest;
use App\Models\ManifestSugeng;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ManifestSugengSyncService
{
    private const SOURCES = [
        'fcl' => TandaTerima::class,
        'lcl' => TandaTerimaLcl::class,
        'ttsj' => TandaTerimaTanpaSuratJalan::class,
    ];

    private const SHIPPER_COLUMNS = [
        'Q' => 'shipper', 'R' => 'alamat_shipper', 'S' => 'npwp_shipper',
        'T' => 'consignee', 'U' => 'alamat_consignee', 'V' => 'npwp_consignee',
        'W' => 'notify_party_consignee', 'X' => 'alamat_notify_party_consignee',
        'Y' => 'npwp_notify_party_consignee', 'Z' => 'delivery_address_contact_person',
        'AA' => 'document_ppftz_03', 'AB' => 'condition',
    ];

    public function syncCell(ManifestSugeng $item, string $kind, int $line, string $column, string $value): void
    {
        if (! isset(self::SOURCES[$item->source_type])) {
            return;
        }

        $receipt = self::SOURCES[$item->source_type]::query()->find($item->source_id);
        if (! $receipt) {
            return;
        }

        $manifests = $this->relatedManifests($item, $receipt);
        if (($kind === 'container' && in_array($column, ['D', 'E'], true))
            || ($kind === 'detail' && $line === 0 && in_array($column, ['B', 'D', 'E', 'AC', 'AD'], true))) {
            $this->syncHeader($item, $receipt, $manifests, $column, $value);
        }
        if ($kind !== 'detail') {
            return;
        }

        if (isset(self::SHIPPER_COLUMNS[$column])) {
            $this->syncShipper($item, $receipt, $manifests, self::SHIPPER_COLUMNS[$column], $value);
        }

        $manifestFields = ['C' => 'hs_codes', 'I' => 'jumlah', 'J' => 'satuan', 'K' => 'nama_barang', 'O' => 'tonase', 'P' => 'meter_kubik'];
        $detailFields = ['L' => 'jumlah', 'M' => 'satuan', 'N' => 'nama_barang'];
        if ($this->isCargo($item)) {
            $manifestFields['F'] = 'jumlah';
            $manifestFields['G'] = 'satuan';
            $detailFields['AE'] = 'jumlah';
            $detailFields['AF'] = 'satuan';
        }

        if (isset($manifestFields[$column])) {
            $this->syncGoods($item, $receipt, $manifests, 'manifest_goods', $line, $manifestFields[$column], $value);
        } elseif (isset($detailFields[$column])) {
            $this->syncGoods($item, $receipt, $manifests, 'goods', $line, $detailFields[$column], $value);
        }
    }

    public function syncHeaderFields(ManifestSugeng $item, array $data): void
    {
        $receipt = isset(self::SOURCES[$item->source_type])
            ? self::SOURCES[$item->source_type]::query()->find($item->source_id) : null;
        $manifests = $receipt ? $this->relatedManifests($item, $receipt) : collect();
        $columns = [
            'nomor_bl' => 'B', 'nomor_kontainer' => 'D', 'no_seal' => 'E',
        ];
        foreach ($columns as $field => $column) {
            if (array_key_exists($field, $data)) {
                $this->syncCell($item, 'detail', 0, $column, (string) ($data[$field] ?? ''));
            }
        }
        $shipFields = array_intersect_key($data, array_flip(['nama_kapal', 'no_voyage']));
        if ($shipFields) {
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly($shipFields));
        }
        if ($receipt && array_key_exists('tipe_kontainer', $data)) {
            if ($item->source_type === 'lcl') {
                $receipt->kontainerPivot()->where('nomor_kontainer', $item->nomor_kontainer)->update(['tipe_kontainer' => $data['tipe_kontainer']]);
            } else {
                $receipt->update(['tipe_kontainer' => $data['tipe_kontainer']]);
            }
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly(['tipe_kontainer' => $data['tipe_kontainer']]));
        }
        if ($receipt && array_key_exists('size_kontainer', $data)) {
            $field = match ($item->source_type) {
                'fcl' => 'size', 'lcl' => 'ukuran', default => 'size_kontainer',
            };
            $receipt->update([$field => $data['size_kontainer']]);
            if ($item->source_type === 'lcl') {
                $receipt->kontainerPivot()->where('nomor_kontainer', $item->nomor_kontainer)->update(['size_kontainer' => $data['size_kontainer']]);
            }
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly(['size_kontainer' => $data['size_kontainer']]));
        }
    }

    private function syncHeader(ManifestSugeng $item, $receipt, $manifests, string $column, string $value): void
    {
        $value = trim($value);
        if ($column === 'B') {
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly(['nomor_bl' => $value ?: null]));

            return;
        }
        if ($column === 'D') {
            $field = $item->source_type === 'lcl' ? 'nomor_kontainer' : 'no_kontainer';
            if ($item->source_type === 'lcl') {
                $receipt->kontainerPivot()->where('nomor_kontainer', $item->nomor_kontainer)
                    ->update(['nomor_kontainer' => $value ?: null]);
                $receipt->setAttribute('nomor_kontainer', $value ?: null);
                $receipt->save();
            } else {
                $receipt->update([$field => $value ?: null]);
            }
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly(['nomor_kontainer' => $value ?: null]));
            $item->nomor_kontainer = $value ?: null;
            $item->save();

            return;
        }
        if ($column === 'E') {
            if ($item->source_type === 'lcl') {
                $receipt->kontainerPivot()->where('nomor_kontainer', $item->nomor_kontainer)->update(['nomor_seal' => $value ?: null]);
            } else {
                $receipt->update(['no_seal' => $value ?: null]);
            }
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly(['no_seal' => $value ?: null]));
            $item->no_seal = $value ?: null;
            $item->save();

            return;
        }
        if ($column === 'AC') {
            if ($value === '') {
                throw ValidationException::withMessages(['value' => 'Nomor tanda terima tidak boleh kosong.']);
            }
            $field = $item->source_type === 'ttsj' ? 'no_tanda_terima' : 'nomor_tanda_terima';
            if ($value !== '' && self::SOURCES[$item->source_type]::query()->where($field, $value)->whereKeyNot($receipt->id)->exists()) {
                throw ValidationException::withMessages(['value' => 'Nomor tanda terima sudah digunakan.']);
            }
            $receipt->update($item->source_type === 'ttsj'
                ? ['no_tanda_terima' => $value, 'nomor_tanda_terima' => $value]
                : [$field => $value]);
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly(['nomor_tanda_terima' => $value]));
            $item->nomor_tanda_terima = $value;
            $item->save();

            return;
        }
        if ($column === 'AD') {
            if ($value === '') {
                throw ValidationException::withMessages(['value' => 'Tanggal tanda terima tidak boleh kosong.']);
            }
            try {
                $parsed = Carbon::createFromFormat('!d/m/Y', $value);
                if (! $parsed || $parsed->format('d/m/Y') !== $value) {
                    throw new \InvalidArgumentException;
                }
                $date = $parsed->format('Y-m-d');
            } catch (\Throwable) {
                throw ValidationException::withMessages(['value' => 'Tanggal harus berformat DD/MM/YYYY.']);
            }
            $receipt->update([$item->source_type === 'fcl' ? 'tanggal_surat_jalan' : 'tanggal_tanda_terima' => $date]);
            $item->tanggal_tanda_terima = $date;
            $item->save();
        }
    }

    private function syncGoods(ManifestSugeng $item, $receipt, $manifests, string $kind, int $line, string $field, string $value): void
    {
        if ($field === 'jumlah' && $value !== '' && (! ctype_digit($value) || (int) $value < 0)) {
            throw ValidationException::withMessages(['value' => 'Jumlah barang harus berupa bilangan bulat positif.']);
        }
        if (in_array($field, ['tonase', 'meter_kubik'], true) && $value !== ''
            && ! preg_match('/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/', $value)) {
            throw ValidationException::withMessages(['value' => 'Format tonase atau volume tidak valid.']);
        }
        $override = ApprovalTandaTerimaDuaGoods::query()->firstOrNew([
            'source_type' => $item->source_type, 'source_id' => $item->source_id,
        ]);
        if (! $override->exists) {
            $override->goods = $item->perincian_barang ?? [];
            $override->manifest_goods = $item->manifest_goods ?? [];
        }
        $values = $kind === 'goods' ? ($override->goods ?? $item->perincian_barang ?? []) : ($override->manifest_goods ?? $item->manifest_goods ?? []);
        $values[$line] ??= [];
        if ($field === 'hs_codes') {
            $codes = array_values(array_filter(array_map('trim', preg_split('/\R/u', $value))));
            $values[$line]['hs_codes'] = $codes;
            $values[$line]['hs_code'] = $codes[0] ?? null;
        } else {
            $values[$line][$field] = $value === '' ? null : $value;
        }
        if ($kind === 'goods') {
            $override->goods = $values;
            $item->perincian_barang = $values;
        } else {
            $override->manifest_goods = $values;
            $item->manifest_goods = $values;
        }
        $override->updated_by = auth()->id();
        $override->save();
        $item->save();

        if ($kind === 'manifest_goods') {
            $this->syncReceiptGood($item, $receipt, $line, $field, $value);
        } elseif ($line === 0 && $kind === 'goods' && $item->source_type === 'ttsj') {
            $sourceField = ['nama_barang' => 'nama_barang', 'jumlah' => 'jumlah_barang', 'satuan' => 'satuan_barang'][$field] ?? null;
            if ($sourceField) {
                $receipt->update([$sourceField => $value === '' ? null : $value]);
            }
        }
        $manifestSync = app(ManifestApprovalGoodsSync::class);
        $manifestSync->syncSource($item->source_type, $item->source_id);
        $manifestFields = $manifestSync->fields($override);
        if ($manifestFields) {
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly($manifestFields));
        }
    }

    private function syncReceiptGood(ManifestSugeng $item, $receipt, int $line, string $field, string $value): void
    {
        if ($field === 'hs_codes') {
            return;
        }
        $sourceValue = in_array($field, ['tonase', 'meter_kubik'], true) && $value !== ''
            ? app(ManifestApprovalGoodsSync::class)->parseMetric($value)
            : ($value === '' ? null : $value);
        $sourceFields = match ($item->source_type) {
            'fcl' => ['jumlah' => 'jumlah', 'satuan' => 'satuan', 'tonase' => 'tonase', 'meter_kubik' => 'meter_kubik'],
            'ttsj' => ['nama_barang' => 'nama_barang', 'jumlah' => 'jumlah_barang', 'satuan' => 'satuan_barang', 'tonase' => 'tonase', 'meter_kubik' => 'meter_kubik'],
            default => [],
        };
        if ($item->source_type === 'lcl') {
            $sourceItem = $receipt->items()->orderBy('id')->skip($line)->first();
            if ($sourceItem) {
                $sourceItem->update([$field => $sourceValue]);
            }
        } elseif ($line > 0 && ! ($item->source_type === 'fcl' && $field === 'nama_barang')) {
            return;
        } elseif (isset($sourceFields[$field])) {
            $receipt->update([$sourceFields[$field] => $sourceValue]);
        } elseif ($item->source_type === 'fcl' && $field === 'nama_barang') {
            $names = $receipt->nama_barang ?? [];
            $names[$line] = $value;
            $receipt->update(['nama_barang' => $names]);
        }
    }

    private function syncShipper(ManifestSugeng $item, $receipt, $manifests, string $field, string $value): void
    {
        if ($receipt->shipper_jb_id) {
            $override = ApprovalTandaTerimaDuaShipperOverride::query()->firstOrNew([
                'source_type' => $item->source_type, 'source_id' => $item->source_id, 'context' => 'main',
            ]);
            $fields = $override->fields ?? [];
            $fields[$field] = $value === '' ? null : $value;
            $override->fields = $fields;
            $override->shipper_jb_id = $receipt->shipper_jb_id;
            $override->updated_by = auth()->id();
            $override->save();
        } elseif (! in_array($field, ['shipper', 'consignee', 'alamat_shipper', 'alamat_consignee', 'notify_party_consignee', 'alamat_notify_party_consignee'], true)) {
            throw ValidationException::withMessages(['value' => 'Pilih Shipper JB pada tanda terima sebelum mengubah kolom ini.']);
        }

        $receiptFields = match ($field) {
            'shipper' => [$item->source_type === 'lcl' ? 'nama_pengirim' : 'pengirim' => $value],
            'consignee' => [$item->source_type === 'lcl' ? 'nama_penerima' : 'penerima' => $value],
            'alamat_shipper' => ['alamat_pengirim' => $value],
            'alamat_consignee' => ['alamat_penerima' => $value],
            'notify_party_consignee' => ['notify_party' => $value],
            'alamat_notify_party_consignee' => ['alamat_notify_party' => $value],
            default => [],
        };
        if ($receiptFields) {
            $receipt->update($receiptFields);
        }
        $manifestField = [
            'shipper' => 'pengirim', 'alamat_shipper' => 'alamat_pengirim',
            'consignee' => 'penerima', 'alamat_consignee' => 'alamat_penerima',
            'notify_party_consignee' => 'notify_party', 'alamat_notify_party_consignee' => 'alamat_notify_party',
        ][$field] ?? null;
        if ($manifestField) {
            $manifests->each(fn (Manifest $manifest) => $manifest->updateQuietly([$manifestField => $value]));
        }
        if ($field === 'shipper') {
            $item->pengirim = $value;
            $item->save();
        }
    }

    private function relatedManifests(ManifestSugeng $item, $receipt)
    {
        $number = $item->nomor_tanda_terima;
        if (blank($number) && $item->source_type !== 'fcl') {
            return collect();
        }

        return Manifest::query()
            ->when(filled($item->no_voyage),
                fn ($query) => $query->where('no_voyage', $item->no_voyage),
                fn ($query) => $query->where(fn ($voyage) => $voyage->whereNull('no_voyage')->orWhere('no_voyage', '')))
            ->where(function ($query) use ($item, $receipt, $number) {
                if (filled($number)) {
                    $query->where('nomor_tanda_terima', $number);
                }
                if ($item->source_type === 'fcl') {
                    $query->orWhereHas('prospek', fn ($prospek) => $prospek->where('tanda_terima_id', $receipt->id));
                }
            })->get();
    }

    private function isCargo(ManifestSugeng $item): bool
    {
        return str_contains(strtoupper((string) $item->tipe_kontainer), 'CARGO');
    }
}
