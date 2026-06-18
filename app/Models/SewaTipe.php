<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaTipe extends Model
{
    protected $table = 'sewa_tipes';

    protected $primaryKey = 'id_tipe';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_tipe',
        'nama_tipe',
    ];

    public function kontainers()
    {
        return $this->hasMany(SewaKontainer::class, 'id_tipe', 'id_tipe');
    }

    public function tarifs()
    {
        return $this->hasMany(SewaTarif::class, 'id_tipe', 'id_tipe');
    }
}
