<?php

namespace App\Services;

use App\Models\Bl;
use App\Models\Manifest;
use Illuminate\Support\Collection;

class RekapBlService
{
    public function __construct(private Collection $bls, private Collection $manifests)
    {
        // Keep both catalogs even when BL and manifest IDs overlap.
        $this->bls = collect($bls->all());
        $this->manifests = collect($manifests->all());
    }

    public static function forVoyage(string $kapal, string $voyage): self
    {
        $load = fn ($model) => $model::whereRaw('LOWER(TRIM(no_voyage)) = ?', [strtolower(trim($voyage))])->get()
            ->filter(fn ($row) => self::ship($row->nama_kapal ?? '') === self::ship($kapal));

        return new self($load(Bl::class), $load(Manifest::class));
    }

    private static function ship(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', preg_replace('/^km[.\s]+/i', '', strtolower(trim($name))));
    }

    public static function normalize($number): string
    {
        return strtoupper(preg_replace('/-\d+$/', '', trim((string) $number)));
    }

    private function numbers($value): Collection
    {
        return collect(is_array($value) ? $value : preg_split('/[,;\n]+/', (string) $value))
            ->map(fn ($number) => self::normalize($number))
            ->filter(fn ($number) => $number !== '' && ! in_array($number, ['-', 'N/A', 'NULL']))->unique()->values();
    }

    public function available(): Collection
    {
        return $this->bls->merge($this->manifests)->pluck('nomor_bl')
            ->flatMap(fn ($number) => $this->numbers($number))->unique()->sort()->values();
    }

    public function containerCounts(): array
    {
        $containersByBl = [];

        foreach ($this->bls->merge($this->manifests) as $record) {
            $containers = collect(preg_split('/[,;\r\n]+/', (string) $record->nomor_kontainer))
                ->map(fn ($number) => strtoupper(preg_replace('/\s+/', '', trim($number))))
                ->filter()->unique();

            foreach ($this->numbers($record->nomor_bl) as $blNumber) {
                foreach ($containers as $containerNumber) {
                    $containersByBl[$blNumber][$containerNumber] = true;
                }
            }
        }

        return collect($containersByBl)->map(fn ($containers) => count($containers))->all();
    }

    public function containers(array $selectedBls = []): Collection
    {
        return $this->containerDetails($selectedBls)->pluck('nomor_kontainer');
    }

    public function containerDetails(array $selectedBls = []): Collection
    {
        $selected = $this->numbers($selectedBls);
        $details = collect();

        // Manifest is the source used when TKBM items are created; BL fills gaps.
        foreach ($this->manifests->merge($this->bls) as $record) {
            if ($selected->isNotEmpty() && $this->numbers($record->nomor_bl)->intersect($selected)->isEmpty()) {
                continue;
            }
            $type = strtoupper(trim($record->tipe_kontainer ?? ''));
            if ($type === 'CARGO') {
                continue;
            }

            $sizeRaw = strtoupper((string) ($record->size_kontainer ?? ''));
            $size = str_contains($sizeRaw, '40') ? '40' : (str_contains($sizeRaw, '20') ? '20' : '-');
            $barang = strtoupper((string) ($record->nama_barang ?? ''));
            $explicitStatus = preg_match('/\b(EMPTY|KOSONG)\b/', $barang) ? 'EMPTY'
                : (preg_match('/\bFULL\b/', $barang) ? 'FULL' : null);

            foreach (preg_split('/[,;\r\n]+/', (string) $record->nomor_kontainer) as $rawNumber) {
                $number = strtoupper(preg_replace('/\s+/', '', trim($rawNumber)));
                if ($number === '' || in_array($number, ['-', 'N/A', 'NULL'])) {
                    continue;
                }

                $status = $explicitStatus ?? ($type === 'FCL' && str_starts_with($number, 'CARGO-') ? 'EMPTY' : null);
                $current = $details->get($number);
                $details->put($number, [
                    'nomor_kontainer' => $number,
                    'size' => $current && $current['size'] !== '-' ? $current['size'] : $size,
                    'status' => $current && $current['status_explicit'] ? $current['status'] : ($status ?? 'FULL'),
                    'status_explicit' => ($current['status_explicit'] ?? false) || $status !== null,
                ]);
            }
        }

        return $details->sortKeys()->values()->map(function ($detail) {
            unset($detail['status_explicit']);

            return $detail;
        });
    }

    /** Null means no BL link; zero means a link to a different BL. */
    public function ratio($row, string|array $selected): ?float
    {
        if (is_array($selected)) {
            $ratios = $this->numbers($selected)->map(fn ($number) => $this->ratio($row, $number));

            return $ratios->isEmpty() || $ratios->containsStrict(null) ? null : min(1.0, (float) $ratios->sum());
        }
        foreach (['nomor_bl', 'no_bl'] as $field) {
            $numbers = $this->numbers(data_get($row, $field));
            if ($numbers->isNotEmpty()) {
                return $numbers->contains($selected) ? 1 / $numbers->count() : 0;
            }
        }

        $manifestId = data_get($row, 'manifest_id');
        if ($manifestId && ($manifest = $this->manifests->firstWhere('id', $manifestId))) {
            return $this->ratio(['nomor_bl' => $manifest->nomor_bl], $selected);
        }
        $entries = data_get($row, 'kontainer_ids');
        if (is_array($entries) && count($entries) > 0) {
            $ratios = collect($entries)->map(fn ($entry) => $this->ratio(is_array($entry) ? $entry : ['bl_id' => $entry], $selected));
            // Missing links must not silently assign the entire section to a BL.
            if ($ratios->containsStrict(null)) {
                return null;
            }
            foreach (['biaya_klaim', 'nominal', 'dpp', 'sisa_pembayaran'] as $field) {
                $total = collect($entries)->sum(fn ($entry) => (float) data_get($entry, $field, 0));
                if ($total > 0) {
                    return collect($entries)->map(fn ($entry, $index) => (float) data_get($entry, $field, 0) * ($ratios[$index] ?? 0))->sum() / $total;
                }
            }

            return $ratios->sum() / count($entries);
        }

        $containers = collect(preg_split('/[,;\n]+/', (string) (data_get($row, 'nomor_kontainer') ?: data_get($row, 'no_kontainer'))))
            ->map(fn ($number) => strtoupper(trim($number)))->filter()->unique();
        if ($containers->isNotEmpty()) {
            $catalog = $this->bls->merge($this->manifests);
            $known = false;
            $sum = 0;
            foreach ($containers as $number) {
                $numbers = $catalog->filter(fn ($item) => strtoupper(trim($item->nomor_kontainer ?? '')) === $number)
                    ->pluck('nomor_bl')->flatMap(fn ($bl) => $this->numbers($bl))->unique();
                if ($numbers->isNotEmpty()) {
                    $known = true;
                    $sum += $numbers->contains($selected) ? 1 / $numbers->count() : 0;
                } else {
                    return null;
                }
            }

            return $known ? $sum / $containers->count() : null;
        }

        $blId = data_get($row, 'bl_id');
        if ($blId && ($bl = $this->bls->firstWhere('id', $blId))) {
            return $this->ratio(['nomor_bl' => $bl->nomor_bl], $selected);
        }

        return null;
    }

    public function transportRatio($suratJalan, string $kapal, string $voyage, string|array $selected): ?float
    {
        if (is_array($selected)) {
            $ratios = $this->numbers($selected)->map(fn ($number) => $this->transportRatio($suratJalan, $kapal, $voyage, $number));

            return $ratios->isEmpty() || $ratios->containsStrict(null) ? null : min(1.0, (float) $ratios->sum());
        }
        if (! $suratJalan) {
            return null;
        }
        if (method_exists($suratJalan, 'prospeks')) {
            $prospeks = $suratJalan->prospeks->filter(fn ($p) => self::ship($p->nama_kapal ?? '') === self::ship($kapal)
                && strtolower(trim($p->no_voyage ?? '')) === strtolower(trim($voyage)));
            if ($prospeks->isNotEmpty()) {
                $ratios = $prospeks->map(function ($prospek) use ($selected) {
                    $numbers = $this->manifests->merge($this->bls)->where('prospek_id', $prospek->id)
                        ->pluck('nomor_bl')->flatMap(fn ($number) => $this->numbers($number))->unique();

                    return $numbers->isNotEmpty()
                        ? ($numbers->contains($selected) ? 1 / $numbers->count() : 0)
                        : $this->ratio($prospek, $selected);
                });

                return $ratios->containsStrict(null) ? null : $ratios->sum() / $prospeks->count();
            }
        }

        return $this->ratio($suratJalan, $selected);
    }
}
