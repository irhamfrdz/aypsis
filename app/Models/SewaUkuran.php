<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaUkuran extends Model
{
    protected $table = 'sewa_ukurans';

    protected $primaryKey = 'id_ukuran';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_ukuran',
        'deskripsi_ukuran',
    ];

    public function kontainers()
    {
        return $this->hasMany(SewaKontainer::class, 'id_ukuran', 'id_ukuran');
    }

    public function tarifs()
    {
        return $this->hasMany(SewaTarif::class, 'id_ukuran', 'id_ukuran');
    }
}
