<?php

namespace App\Services;

use App\Models\Gudang;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GudangPlanService
{
    public function validateLayout(array $layout): array
    {
        $layout = Validator::make($layout, [
            'blocks' => 'required|array|min:1|max:12',
            'blocks.*' => 'required|array:code,bays,rows,tiers,disabled',
            'blocks.*.code' => ['required', 'string', 'max:12', 'regex:/^[A-Z0-9_-]+$/', 'distinct'],
            'blocks.*.bays' => 'required|integer|min:1|max:40',
            'blocks.*.rows' => 'required|integer|min:1|max:20',
            'blocks.*.tiers' => 'required|integer|min:1|max:6',
            'blocks.*.disabled' => 'present|array|max:800',
            'blocks.*.disabled.*' => 'required|array:bay,row',
            'blocks.*.disabled.*.bay' => 'required|integer|min:1',
            'blocks.*.disabled.*.row' => 'required|integer|min:1',
        ], [], [
            'blocks' => 'area gudang',
            'blocks.*.code' => 'kode area',
            'blocks.*.bays' => 'jumlah slot',
            'blocks.*.rows' => 'jumlah baris',
            'blocks.*.tiers' => 'tingkat maksimum',
            'blocks.*.disabled' => 'petak nonaktif',
            'blocks.*.disabled.*.bay' => 'slot petak nonaktif',
            'blocks.*.disabled.*.row' => 'baris petak nonaktif',
        ])->validate();

        $area = 0;
        foreach ($layout['blocks'] as &$block) {
            foreach (['bays', 'rows', 'tiers'] as $dimension) {
                $block[$dimension] = (int) $block[$dimension];
            }
            $area += $block['bays'] * $block['rows'];
            $disabled = [];
            foreach ($block['disabled'] as $cell) {
                if ($cell['bay'] > $block['bays'] || $cell['row'] > $block['rows']) {
                    $this->fail('Petak nonaktif berada di luar ukuran area '.$block['code'].'.');
                }
                $disabled[$cell['bay'].':'.$cell['row']] = ['bay' => (int) $cell['bay'], 'row' => (int) $cell['row']];
            }
            $block['disabled'] = array_values($disabled);
        }
        unset($block);
        if ($area > 2000) {
            $this->fail('Maksimal 2.000 petak dasar untuk seluruh area gudang.');
        }

        return $layout;
    }

    /** Validate the entire proposed plan, including footprints and support below each tier. */
    public function validatePositions(?array $layout, array $positions): void
    {
        if (! $layout && count($positions)) {
            $this->fail('Atur dan simpan layout gudang terlebih dahulu.');
        }
        $blocks = collect($layout['blocks'] ?? [])->keyBy('code');
        $occupied = [];
        $numbers = [];
        foreach ($positions as $position) {
            $block = $blocks->get($position['block']);
            $number = preg_replace('/[^A-Z0-9]/', '', strtoupper($position['container_number']));
            if (isset($numbers[$number])) {
                $this->fail('Kontainer '.$position['container_number'].' sudah ditempatkan di denah.');
            }
            $numbers[$number] = true;
            if (! $block || $position['bay'] < 1 || $position['row'] < 1 || $position['tier'] < 1
                || $block['bays'] < $position['bay'] + $position['span'] - 1
                || $position['row'] > $block['rows'] || $position['tier'] > $block['tiers']) {
                $this->fail('Posisi '.$position['container_number'].' berada di luar layout. Pindahkan kontainer sebelum memperkecil atau menghapus area.');
            }
            for ($bay = $position['bay']; $bay < $position['bay'] + $position['span']; $bay++) {
                foreach ($block['disabled'] as $disabled) {
                    if ($disabled['bay'] == $bay && $disabled['row'] == $position['row']) {
                        $this->fail('Petak nonaktif tidak boleh ditempati: '.$position['container_number'].'.');
                    }
                }
                $key = $position['block'].':'.$bay.':'.$position['row'].':'.$position['tier'];
                if (isset($occupied[$key])) {
                    $this->fail('Area '.$position['block'].', Slot '.str_pad((string) $bay, 2, '0', STR_PAD_LEFT)
                        .', Baris '.str_pad((string) $position['row'], 2, '0', STR_PAD_LEFT)
                        .', Tingkat '.str_pad((string) $position['tier'], 2, '0', STR_PAD_LEFT)
                        .' sudah ditempati kontainer lain.');
                }
                $occupied[$key] = true;
            }
        }
        foreach ($positions as $position) {
            if ($position['tier'] <= 1) {
                continue;
            }
            for ($bay = $position['bay']; $bay < $position['bay'] + $position['span']; $bay++) {
                $below = $position['block'].':'.$bay.':'.$position['row'].':'.($position['tier'] - 1);
                if (! isset($occupied[$below])) {
                    $this->fail('Tingkat di bawah '.$position['container_number'].' harus terisi. Pindahkan kontainer dari tingkat paling atas terlebih dahulu.');
                }
            }
        }
    }

    public function containers(Gudang $gudang)
    {
        return collect(['sewa' => Kontainer::class, 'stock' => StockKontainer::class])
            ->flatMap(function ($model, $source) use ($gudang) {
                return $model::where('gudangs_id', $gudang->id)->where('status', '!=', 'inactive')
                    ->orderBy('nomor_seri_gabungan')->get()->map(function ($container) use ($source) {
                        return [
                            'key' => $source.':'.$container->id,
                            'source' => $source,
                            'container_id' => $container->id,
                            'number' => $container->nomor_seri_gabungan ?: $container->awalan_kontainer.$container->nomor_seri_kontainer.$container->akhiran_kontainer,
                            'size' => $container->ukuran,
                            'span' => $this->span($container->ukuran),
                            'type' => $container->tipe_kontainer,
                        ];
                    });
            })->values();
    }

    public function span($size): ?int
    {
        if (preg_match('/^\s*(20|40)(?:\s*(?:ft|feet|hc|hq|gp|dc|dry|[\x27\x22]))?\s*$/i', (string) $size, $match)) {
            return $match[1] === '40' ? 2 : 1;
        }

        return null;
    }

    public function state(Gudang $gudang): array
    {
        $containers = $this->containers($gudang);
        $available = $containers->keyBy('key');
        $positions = $gudang->positions()->orderBy('id')->get()->map(function ($position) use ($available) {
            $key = $position->source.':'.$position->container_id;
            $container = $available->get($key);

            return array_merge($position->toArray(), [
                'key' => $key,
                'stale' => ! $container || $container['span'] !== $position->span || $container['number'] !== $position->container_number,
            ]);
        });

        return [
            'layout' => $gudang->denah_layout,
            'version' => $gudang->denah_version,
            'positions' => $positions,
            'containers' => $containers,
        ];
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['denah' => $message]);
    }
}
