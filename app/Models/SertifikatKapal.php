<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatKapal extends Model
{
    protected $table = 'sertifikat_kapals';

    protected $fillable = [
        'nama_sertifikat',
        'keterangan',
        'status',
    ];
