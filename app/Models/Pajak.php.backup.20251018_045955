<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    protected $table = 'pajaks';

    protected $fillable = [
        'nama_status',
        'keterangan'
    ];

    protected $casts = [
        //
    ];

    /**
     * Scope for searching pajak
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('nama_status', 'like', '%' . $search . '%')
                        ->orWhere('keterangan', 'like', '%' . $search . '%');
        }
        return $query;
    }
}
