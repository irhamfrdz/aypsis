<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BpjsEffectiveValues
{
    public static function schedule($record, string $component, string $from, string $until, array $values): void
    {
        DB::transaction(function () use ($record, $component, $from, $until, $values) {
            $record->newQuery()->whereKey($record->id)->lockForUpdate()->firstOrFail();
            $query = DB::table('bpjs_effective_values')->where('target_type', get_class($record))
                ->where('target_id', $record->id);
            foreach ($query->where('valid_from', '<=', $until)->where('valid_until', '>=', $from)->get() as $entry) {
                if (array_intersect_key($values, json_decode($entry->values, true))) {
                    throw ValidationException::withMessages([$component.'_from' => 'Masa berlaku bertumpang tindih dengan jadwal yang sudah tersimpan.']);
                }
            }
            DB::table('bpjs_effective_values')->insert([
                'target_type' => get_class($record), 'target_id' => $record->id,
                'component' => $component, 'valid_from' => $from, 'valid_until' => $until,
                'values' => json_encode($values), 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
    }

    public static function attach($records): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('bpjs_effective_values')) {
            return;
        }
        foreach ($records->groupBy(fn ($record) => get_class($record)) as $type => $group) {
            $history = DB::table('bpjs_effective_values')->where('target_type', $type)
                ->whereIn('target_id', $group->pluck('id'))->orderBy('valid_from')->get()->groupBy('target_id');
            foreach ($group as $record) {
                $record->setAttribute('bpjs_effective_values', ($history[$record->id] ?? collect())->map(function ($row) {
                    return ['component' => $row->component, 'valid_from' => $row->valid_from,
                        'valid_until' => $row->valid_until, 'values' => json_decode($row->values, true)];
                })->values()->all());
            }
        }
    }
}
