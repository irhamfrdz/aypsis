<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangPosition extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'container_id' => 'integer',
        'span' => 'integer',
        'bay' => 'integer',
        'row' => 'integer',
        'tier' => 'integer',
    ];
}
