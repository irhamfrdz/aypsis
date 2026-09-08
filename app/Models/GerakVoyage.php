<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GerakVoyage extends Model
{
    protected $fillable = [
        'nama_kapal',
        'no_voyage',
        'tanggal_mulai_berlayar',
        'tanggal_berlabuh',
        'tanggal_sandar',
        'tanggal_mulai_bongkar',
        'tanggal_selesai_bongkar',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
