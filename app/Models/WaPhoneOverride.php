<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaPhoneOverride extends Model
{
    protected $table = 'wa_phone_overrides';

    protected $fillable = [
        'shipper_name',
        'telepon',
        'updated_by',
    ];

    /**
     * Get all overrides keyed by shipper_name for fast lookup.
     *
     * @return \Illuminate\Support\Collection<string, string>
     */
    public static function allKeyed(): \Illuminate\Support\Collection
    {
        return static::whereNotNull('telepon')
            ->where('telepon', '!=', '')
            ->pluck('telepon', 'shipper_name');
    }
}
