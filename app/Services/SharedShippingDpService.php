<?php

namespace App\Services;

use App\Models\BiayaKapalMeratusStage;
use App\Models\BiayaKapalTemasStage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SharedShippingDpService
{
    public function source($stage): string
    {
        return $stage instanceof BiayaKapalTemasStage ? 'temas' : 'meratus';
    }

    public function key($stage): string
    {
        return $this->source($stage).':'.$stage->id;
    }

    private function model(string $source): string
    {
        return $source === 'temas' ? BiayaKapalTemasStage::class : BiayaKapalMeratusStage::class;
    }

    public function usageQuery(string $source, bool $cross, ?int $excludingInvoiceId = null)
    {
        $target = $cross ? ($source === 'temas' ? 'meratus' : 'temas') : $source;
        $table = $cross ? "{$source}_dp_{$target}_settlements" : "{$source}_dp_references";

        return DB::table($table.' as refs')
            ->join("biaya_kapal_{$target}_stages as settlement", 'settlement.id', '=', 'refs.settlement_stage_id')
            ->join('biaya_kapals as invoice', 'invoice.id', '=', 'settlement.biaya_kapal_id')
            ->whereNull('invoice.deleted_at')->where('invoice.status_pembayaran', '!=', 'cancelled')
            ->when($excludingInvoiceId, fn ($q) => $q->where('invoice.id', '!=', $excludingInvoiceId));
    }

    public function balanceCents($dp, ?int $excludingInvoiceId = null, bool $lock = false): int
    {
        $used = 0;
        foreach ([false, true] as $cross) {
            $query = $this->usageQuery($this->source($dp), $cross, $excludingInvoiceId)
                ->where('refs.dp_stage_id', $dp->id)->select('refs.nominal_digunakan');
            if ($lock) {
                $query->lockForUpdate();
            }
            $used += $query->get()->sum(fn ($row) => (int) round((float) $row->nominal_digunakan * 100));
        }

        return max(0, (int) round((float) $dp->nominal_dibayar * 100) - $used);
    }

    // Keep each existing query available, including its numeric IDs, for legacy callers.
    public function candidates(string $source, ?int $excludingInvoiceId = null)
    {
        $table = "biaya_kapal_{$source}_stages";
        $native = $this->usageQuery($source, false, $excludingInvoiceId)
            ->whereColumn('refs.dp_stage_id', "$table.id")->selectRaw('COALESCE(SUM(refs.nominal_digunakan), 0)');
        $cross = $this->usageQuery($source, true, $excludingInvoiceId)
            ->whereColumn('refs.dp_stage_id', "$table.id")->selectRaw('COALESCE(SUM(refs.nominal_digunakan), 0)');
        $used = DB::query()->selectRaw('('.$native->toSql().') + ('.$cross->toSql().')', array_merge($native->getBindings(), $cross->getBindings()));

        return $this->model($source)::select($table.'.*')->selectSub($used, 'dp_digunakan')
            ->where('payment_mode', 'dp')->where('nominal_dibayar', '>', 0)
            ->whereHas('biayaKapal', fn ($q) => $q->where('status_pembayaran', '!=', 'cancelled'))
            ->where('nominal_dibayar', '>', $used)
            ->when($excludingInvoiceId, fn ($q) => $q->where('biaya_kapal_id', '!=', $excludingInvoiceId));
    }

    public function available(?int $excludingInvoiceId = null): Collection
    {
        return collect(['temas', 'meratus'])->flatMap(fn ($source) => $this->candidates($source, $excludingInvoiceId)
            ->with('biayaKapal:id,nomor_invoice,tanggal')->get()->all())
            ->sortByDesc(fn ($dp) => $dp->tanggal_dp?->format('Y-m-d') ?? $dp->biayaKapal->tanggal?->format('Y-m-d') ?? '')
            ->values();
    }

    private function parse($key, string $defaultSource): array
    {
        if (is_int($key) || (is_string($key) && ctype_digit($key))) {
            return [$defaultSource, (int) $key];
        }
        if (is_string($key) && preg_match('/^(temas|meratus):([1-9][0-9]*)$/', $key, $match)) {
            return [$match[1], (int) $match[2]];
        }

        throw ValidationException::withMessages([$defaultSource => 'Referensi DP tidak valid.']);
    }

    public function lockSources(string $target, int $invoiceId, array $sections): void
    {
        $keys = collect($sections)->flatMap(fn ($section) => $section['dp_stage_ids'] ?? (isset($section['dp_stage_id']) ? [$section['dp_stage_id']] : []))->all();
        foreach (['temas', 'meratus'] as $source) {
            $table = $source === $target ? "{$source}_dp_references" : "{$source}_dp_{$target}_settlements";
            foreach (DB::table($table.' as refs')->join("biaya_kapal_{$target}_stages as s", 's.id', '=', 'refs.settlement_stage_id')
                ->where('s.biaya_kapal_id', $invoiceId)->pluck('refs.dp_stage_id') as $id) {
                $keys[] = $source.':'.$id;
            }
        }
        $ids = ['temas' => [], 'meratus' => []];
        foreach ($keys as $key) {
            [$source, $id] = $this->parse($key, $target);
            $ids[$source][] = $id;
        }
        // All settlement paths take the same source locks before reading or returning balances.
        foreach (['temas', 'meratus'] as $source) {
            $this->model($source)::whereIn('id', array_unique($ids[$source]))->orderBy('id')->lockForUpdate()->get();
        }
    }

    public function resolve(array $keys, string $target, $index = 0): Collection
    {
        $dps = collect();
        foreach ($keys as $key) {
            [$source, $id] = $this->parse($key, $target);
            $table = "biaya_kapal_{$source}_stages";
            $dp = $this->model($source)::select($table.'.*')
                ->join('biaya_kapals as dp_invoice', 'dp_invoice.id', '=', $table.'.biaya_kapal_id')
                ->where($table.'.id', $id)->whereNull('dp_invoice.deleted_at')
                ->where('dp_invoice.status_pembayaran', '!=', 'cancelled')->lockForUpdate()->first();
            if (! $dp || $dps->has($this->key($dp))) {
                throw ValidationException::withMessages(["$target.$index.dp_stage_ids" => 'Referensi DP tidak tersedia atau duplikat.']);
            }
            $dps->put($this->key($dp), $dp);
        }

        return $dps->values();
    }

    public function sync($stage, array $allocations): void
    {
        $native = [];
        $cross = [];
        foreach ($allocations as $key => $allocation) {
            [$source, $id] = $this->parse($key, $this->source($stage));
            if ($source === $this->source($stage)) {
                $native[$id] = $allocation;
            } else {
                $cross[$id] = $allocation;
            }
        }
        $stage->dpStages()->sync($native);
        $stage->crossDpStages()->sync($cross);
        $stage->update(['dp_stage_id' => array_key_first($native)]);
    }
}
