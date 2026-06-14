<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKartuBensinBatam extends Model
{
    use Auditable, HasFactory;

    protected $table = 'master_kartu_bensin_batams';

    protected $fillable = [
        'nomor_kartu',
        'nama_kartu',
        'provider',
        'mobil_id',
        'karyawan_id',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Scope for active cards
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Relationships
    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
