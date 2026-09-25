<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UangLembur extends Model
{
    protected $table = 'master_uang_lemburs';

    protected $fillable = [
        'group',
        'sub_group',
        'pengali_uang_makan_hari_libur',
    ];

    public function rules()
    {
        return $this->hasMany(UangLemburRule::class, 'uang_lembur_id');
    }
}
