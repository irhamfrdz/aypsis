<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkUkuranKontainer extends Model
{
    use Auditable;

    protected $table = 'sk_ukuran_kontainers';

    protected $fillable = [
        'deskripsi_ukuran',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function kontainers()
    {
        return $this->hasMany(SkKontainer::class, 'ukuran_id');
    }

    public function tarifs()
    {
        return $this->hasMany(SkTarifSewa::class, 'ukuran_id');
    }
}
