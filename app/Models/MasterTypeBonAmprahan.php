<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTypeBonAmprahan extends Model
{
    use HasFactory;

    protected $table = 'master_type_bon_amprahans';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'status',
    ];

    public static function generateNextKode(): string
    {
        $lastNumber = static::query()->get('kode')->max(function ($record) {
            return preg_match('/^TBA(\d+)$/i', (string) $record->kode, $matches)
                ? (int) $matches[1]
                : 0;
        });

        return 'TBA'.str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);
    }
}
