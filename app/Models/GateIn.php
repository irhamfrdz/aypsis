<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GateIn extends Model
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_gate_in',
        'pelabuhan',
        'kegiatan',
        'gudang',
        'kontainer',
        'muatan',
        'kapal_id',
        'tanggal_gate_in',
        'user_id',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_gate_in' => 'datetime',
    ];

    // Relationships

    public function terminal()
    {
        return $this->belongsTo(MasterTerminal::class, 'terminal_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function kontainers()
    {
        return $this->hasMany(Kontainer::class, 'gate_in_id');
    }

    // Relasi dengan surat jalan yang sudah gate in
    public function suratJalans()
    {
        return $this->hasMany('App\Models\SuratJalan', 'gate_in_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function petikemas()
    {
        return $this->hasMany(GateInPetikemas::class, 'gate_in_id');
    }

    public function aktivitas()
    {
        return $this->hasMany(GateInAktivitas::class, 'gate_in_id');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    // Accessors
    public function getFormattedTanggalGateInAttribute()
    {
        return $this->tanggal_gate_in ? $this->tanggal_gate_in->format('d/m/Y H:i') : '-';
    }

    public function getTotalKontainerAttribute()
    {
        return $this->kontainers()->count();
    }
}
