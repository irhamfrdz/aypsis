<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class PricelistSewaKontainer extends Model
{
    use HasFactory;

    use Auditable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pricelist_sewa_kontainers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor',
        'tarif',
        'ukuran_kontainer',
        'tanggal_harga_awal',
        'tanggal_harga_akhir',
        'keterangan',
        'harga',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_harga_awal' => 'date',
        'tanggal_harga_akhir' => 'date',
    ];
}
