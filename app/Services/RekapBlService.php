<?php

namespace App\Services;

use App\Models\Bl;
use App\Models\Manifest;
use App\Models\Prospek;
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

    private static function explicitContainerStatus(string $name): ?string
    {
        $name = strtoupper(trim($name));
        if (preg_match('/^(?:EMPTY|KOSONG)$|\b(?:EMPTY|KOSONG)\s+(?:CONTAINER|KONTAINER)\b|\b(?:CONTAINER|KONTAINER)\b.*\b(?:EMPTY|KOSONG)\b/', $name)) {
            return 'EMPTY';
        }

        return preg_match('/\bFULL\b/', $name) ? 'FULL' : null;
    }

    public static function normalize($number): string
    {
        $number = strtoupper(preg_replace('/-\d+$/', '', trim((string) $number)));

        // Letter suffixes identify parts of the same numeric BL (01A, 01B -> 01).
        return preg_replace('/^(\d+)[\s-]*[A-Z]+$/', '$1', $number);
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

    public function cargoRatio(array $selectedBls = []): ?float
    {
        $cargo = $this->manifests->filter(fn ($record) => strtoupper(trim((string) $record->tipe_kontainer)) === 'CARGO');
        if ($cargo->isEmpty()) {
            $cargo = $this->bls->filter(fn ($record) => strtoupper(trim((string) $record->tipe_kontainer)) === 'CARGO');
        }
        if ($cargo->isEmpty()) {
            return null;
        }
        $selected = $this->numbers($selectedBls);
        $weight = fn ($record) => (float) $record->kuantitas > 0 ? (float) $record->kuantitas : 1;
        $total = $cargo->sum($weight);
        $matchingWeight = $selected->isEmpty() ? $total
            : $cargo->sum(fn ($record) => $weight($record) * ($this->ratio($record, $selected->all()) ?? 0));

        return $matchingWeight / $total;
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
            $explicitStatus = self::explicitContainerStatus((string) ($record->nama_barang ?? ''));

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

    /** Container numbers grouped for the standard and LCL Opslag categories. */
    public function opslagContainerDetails(?Collection $prospects = null): Collection
    {
        $prospects ??= Prospek::whereIn('tipe', ['LCL', 'FCL Booking'])
            ->whereIn('no_voyage', $this->manifests->pluck('no_voyage')->filter()->unique()->all())
            ->get();
        $ships = $this->manifests->pluck('nama_kapal')->map(fn ($ship) => self::ship((string) $ship))->unique();
        $voyages = $this->manifests->pluck('no_voyage')->map(fn ($voyage) => strtolower(trim((string) $voyage)))->unique();
        $matchingProspects = $prospects->filter(fn ($prospect) => $ships->contains(self::ship((string) $prospect->nama_kapal))
                && $voyages->contains(strtolower(trim((string) $prospect->no_voyage)))
                && in_array(strtoupper(trim((string) $prospect->tipe)), ['LCL', 'FCL BOOKING']))
            ->values();
        $bookings = $matchingProspects->filter(fn ($prospect) => strtoupper(trim((string) $prospect->tipe)) === 'FCL BOOKING')
            ->pluck('nomor_kontainer')->map(fn ($number) => strtoupper(preg_replace('/\s+/', '', trim((string) $number))))
            ->filter()->flip();

        $containers = collect();
        $lclContainers = collect();
        foreach ($this->manifests as $manifest) {
            $type = strtoupper(trim((string) $manifest->tipe_kontainer));
            $sizeRaw = strtoupper((string) $manifest->size_kontainer);
            if ($type === 'CARGO' || $sizeRaw === '') {
                continue;
            }
            $size = str_contains($sizeRaw, '40') ? '40' : '20';
            $status = self::explicitContainerStatus((string) $manifest->nama_barang) === 'EMPTY' || ($type === 'FCL'
                && (empty($manifest->nomor_kontainer) || str_starts_with(strtoupper((string) $manifest->nomor_kontainer), 'CARGO-')))
                ? 'EMPTY' : 'FULL';
            foreach (preg_split('/[,;\r\n]+/', (string) $manifest->nomor_kontainer) as $rawNumber) {
                $number = strtoupper(preg_replace('/\s+/', '', trim($rawNumber)));
                if ($number === '' || in_array($number, ['-', 'N/A', 'NULL'])) {
                    continue;
                }
                $container = [
                    'nomor_kontainer' => $number,
                    'nomor_bl' => $manifest->nomor_bl ?: '-',
                    'size' => $size,
                    'status' => $status,
                ];
                if (str_contains($type, 'LCL')) {
                    $container['category'] = 'LCL';
                    $lclContainers->put($number, $container);
                    continue;
                }
                if (str_contains($type, 'BOOKING') || $bookings->has($number)) {
                    continue;
                }
                $container['category'] = 'standard';
                $containers->put($number, $container);
            }
        }

        foreach ($matchingProspects->filter(fn ($prospect) => strtoupper(trim((string) $prospect->tipe)) === 'LCL') as $prospect) {
            $sizeRaw = strtoupper((string) $prospect->ukuran);
            $size = str_contains($sizeRaw, '40') ? '40' : (str_contains($sizeRaw, '20') ? '20' : null);
            if (! $size) {
                continue;
            }
            foreach (preg_split('/[,;\r\n]+/', (string) $prospect->nomor_kontainer) as $rawNumber) {
                $number = strtoupper(preg_replace('/\s+/', '', trim($rawNumber)));
                if ($number === '' || in_array($number, ['-', 'N/A', 'NULL']) || $lclContainers->has($number)) {
                    continue;
                }
                $lclContainers->put($number, [
                    'nomor_kontainer' => $number,
                    'nomor_bl' => '-', 'size' => $size, 'status' => 'FULL', 'category' => 'LCL',
                ]);
            }
        }

        return $containers->merge($lclContainers)->sortKeys()->values();
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
