<?php

namespace App\Services;

use App\Models\ApprovalTandaTerimaDuaGoods;
use App\Models\Manifest;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Illuminate\Support\Facades\Schema;

class ManifestApprovalGoodsSync
{
    public function syncSource(string $sourceType, int $sourceId): void
    {
        if (! Schema::hasColumn('manifests', 'perincian_barang')) {
            return;
        }

        $override = ApprovalTandaTerimaDuaGoods::query()
            ->where('source_type', $sourceType)->where('source_id', $sourceId)->first();
        if (! $override) {
            return;
        }
        $fields = $this->fields($override);
        if (! $fields) {
            return;
        }

        $receipt = match ($sourceType) {
            'fcl' => TandaTerima::find($sourceId),
            'lcl' => TandaTerimaLcl::find($sourceId),
            'ttsj' => TandaTerimaTanpaSuratJalan::find($sourceId),
            default => null,
        };
        if (! $receipt) {
            return;
        }

        $numbers = match ($sourceType) {
            'fcl' => [$receipt->no_surat_jalan],
            'lcl' => [$receipt->nomor_tanda_terima],
            default => [$receipt->no_tanda_terima, $receipt->nomor_tanda_terima],
        };
        $numbers = array_values(array_unique(array_filter($numbers, fn ($number) => filled($number))));
        if (! $numbers && $sourceType !== 'fcl') {
            return;
        }

        Manifest::query()->where(function ($query) use ($sourceType, $sourceId, $numbers) {
            if ($numbers) {
                $query->whereIn('nomor_tanda_terima', $numbers);
            }
            if ($sourceType === 'fcl') {
                $query->orWhereHas('prospek', fn ($prospek) => $prospek->where('tanda_terima_id', $sourceId));
            }
        })->get()->each(fn (Manifest $manifest) => $manifest->updateQuietly($fields));
    }

    public function syncManifest(Manifest $manifest): void
    {
        if (! Schema::hasTable('approval_tanda_terima_2_goods') || ! Schema::hasColumn('manifests', 'perincian_barang')) {
            return;
        }

        $source = null;
        if ($manifest->prospek_id) {
            $source = $manifest->prospek?->tandaTerima;
        }

        $number = $manifest->nomor_tanda_terima;
        if ($source) {
            $type = 'fcl';
        } elseif (filled($number) && strtoupper(trim((string) $manifest->tipe_kontainer)) === 'LCL') {
            $source = TandaTerimaLcl::where('nomor_tanda_terima', $number)->first();
            $type = 'lcl';
        }
        if (! $source && filled($number)) {
            $source = TandaTerima::where('no_surat_jalan', $number)->first();
            $type = 'fcl';
        }
        if (! $source && filled($number)) {
            $source = TandaTerimaTanpaSuratJalan::where('no_tanda_terima', $number)
                ->orWhere('nomor_tanda_terima', $number)->first();
            $type = 'ttsj';
        }
        if (! $source && filled($number)) {
            $source = TandaTerimaLcl::where('nomor_tanda_terima', $number)->first();
            $type = 'lcl';
        }
        if (! $source) {
            return;
        }

        $override = ApprovalTandaTerimaDuaGoods::query()
            ->where('source_type', $type)->where('source_id', $source->id)->first();
        if ($override) {
            $fields = $this->fields($override);
            if ($fields) {
                $manifest->updateQuietly($fields);
            }
        }
    }

    private function fields(ApprovalTandaTerimaDuaGoods $override): array
    {
        $good = data_get($override->manifest_goods, '0');
        $fields = [];
        if (is_array($good) && filled($good['nama_barang'] ?? null)) {
            $fields = [
                'nama_barang' => $good['nama_barang'],
                'kuantitas' => $good['jumlah'] ?? null,
                'satuan' => filled($good['satuan'] ?? null) ? $good['satuan'] : null,
            ];
            $fields['hs_code'] = filled($good['hs_code'] ?? null) ? $good['hs_code'] : null;
            $fields['tonnage'] = filled($good['tonase'] ?? null) ? $this->parseMetric((string) $good['tonase']) : null;
            $fields['volume'] = filled($good['meter_kubik'] ?? null) ? $this->parseMetric((string) $good['meter_kubik']) : null;
        }

        $perincian = collect($override->goods ?? [])
            ->filter(fn ($item) => is_array($item) && filled($item['nama_barang'] ?? null))
            ->map(fn ($item) => collect(['nama_barang', 'jumlah', 'satuan', 'ukuran', 'keterangan_barang'])
                ->mapWithKeys(fn ($key) => [$key => $item[$key] ?? null])->all())
            ->values()->all();
        if ($perincian) {
            $fields['perincian_barang'] = $perincian;
        }

        return $fields;
    }

    public function parseMetric(string $value): string
    {
        $value = trim($value);
        if (str_contains($value, ',')) {
            return str_replace(',', '.', str_replace('.', '', $value));
        }

        // Tonase dan volume dengan satu titik, seperti 5.825, adalah nilai desimal.
        return $value;
    }
}
