<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Penerima extends Model
{
    use Auditable;

    protected $fillable = [
        'nama_penerima',
        'contact_person',
        'alamat',
        'npwp',
        'nitku',
        'catatan',
        'status',
        'iu_bp_kawasan',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            if ($model->isDirty('alamat')) {
                $names = array_filter([
                    $model->nama_penerima,
                    $model->getOriginal('nama_penerima'),
                ]);
                if (!empty($names)) {
                    TandaTerimaLcl::whereIn('nama_penerima', $names)
                        ->update(['alamat_penerima' => $model->alamat]);
                    TandaTerimaLcl::whereIn('notify_party', $names)
                        ->update(['alamat_notify_party' => $model->alamat]);

                    TandaTerima::whereIn('penerima', $names)
                        ->update(['alamat_penerima' => $model->alamat]);
                    TandaTerima::whereIn('notify_party', $names)
                        ->update(['alamat_notify_party' => $model->alamat]);
                }
            }
        });
    }
}
