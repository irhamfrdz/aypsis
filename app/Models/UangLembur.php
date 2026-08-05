<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UangLembur extends Model
{
    protected $table = 'master_uang_lemburs';

    protected $fillable = [
        'group',
        'sub_group',
    ];

    public function rules()
    {
        return $this->hasMany(UangLemburRule::class, 'uang_lembur_id');
    }
}
