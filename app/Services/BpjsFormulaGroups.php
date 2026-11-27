<?php

namespace App\Services;

use App\Models\MasterRumusBpjs;
use Illuminate\Support\Collection;

class BpjsFormulaGroups
{
    public static function display(Collection $actionGroups): Collection
    {
        // Keep the existing edit/delete/discount targets intact inside each display row.
        return $actionGroups->groupBy(fn ($records) => self::group($records)->keys()->sort()->implode('|'));
    }

    public static function group(Collection $records): Collection
    {
        return $records->groupBy(function (MasterRumusBpjs $record) {
            // Personal rules retain their own employee scope and edit action.
            if (! empty($record->employee_targets)) {
                return 'personal_'.$record->id;
            }

            $fields = array_diff($record->getFillable(), [
                'tipe', 'employee_targets', 'created_by', 'updated_by',
            ]);
            $values = [];
            foreach ($fields as $field) {
                $values[$field] = $record->$field;
            }
            // Future rates and discount exceptions are part of the formula too.
            $values['bpjs_effective_values'] = $record->bpjs_effective_values ?? [];

            return hash('sha256', json_encode(self::normalize($values), JSON_THROW_ON_ERROR));
        });
    }

    private static function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            $isList = array_is_list($value);
            $value = array_map(self::normalize(...), $value);
            if (! $isList) {
                ksort($value);
            }

            return $value;
        }

        return is_numeric($value) ? (float) $value : $value;
    }
}
