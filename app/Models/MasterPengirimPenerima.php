<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPengirimPenerima extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_pengirim_penerima';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'npwp',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship to User who created this record
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Relationship to User who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Scope untuk filter data aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate kode otomatis
     */
    public static function generateKode()
    {
        $lastRecord = self::withTrashed()->orderBy('id', 'desc')->first();
        $lastNumber = $lastRecord ? intval(substr($lastRecord->kode, 3)) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return 'PP-' . $newNumber;
    }
}
