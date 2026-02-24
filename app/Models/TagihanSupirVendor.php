<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanSupirVendor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
