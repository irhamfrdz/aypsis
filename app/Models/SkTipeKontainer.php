<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkTipeKontainer extends Model
{
    use Auditable;

    protected $table = 'sk_tipe_kontainers';

    protected $fillable = [
        'nama_tipe',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function kontainers()
    {
        return $this->hasMany(SkKontainer::class, 'tipe_id');
    }

    public function tarifs()
    {
        return $this->hasMany(SkTarifSewa::class, 'tipe_id');
    }
}
