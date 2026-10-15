<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemens';

    protected $fillable = ['nama_departemen', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
