<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Pengirim extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama_pengirim',
        'nickname1',
        'contact_person',
        'alamat',
        'catatan',
        'status',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            if ($model->isDirty('alamat')) {
                $names = array_filter([
                    $model->nama_pengirim,
                    $model->nickname1,
                    $model->getOriginal('nama_pengirim'),
                    $model->getOriginal('nickname1'),
                ]);
                if (!empty($names)) {
                    TandaTerimaLcl::whereIn('nama_pengirim', $names)
                        ->update(['alamat_pengirim' => $model->alamat]);

                    TandaTerima::whereIn('pengirim', $names)
                        ->update(['alamat_pengirim' => $model->alamat]);
                }
            }
        });
    }
}
