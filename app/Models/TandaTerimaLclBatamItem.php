<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTerimaLclBatamItem extends Model
{
    use HasFactory;

    protected $table = 'tanda_terima_lcl_batam_items';

    protected $fillable = [
        'tanda_terima_lcl_batam_id',
        'item_number',
        'panjang',
        'lebar',
        'tinggi',
        'meter_kubik',
        'tonase'
    ];

    public function tandaTerimaLclBatam()
    {
        return $this->belongsTo(TandaTerimaLclBatam::class, 'tanda_terima_lcl_batam_id');
    }
}
