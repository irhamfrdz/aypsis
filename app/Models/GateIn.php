<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class GateIn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_gate_in',
        'terminal_id',
        'kapal_id',
        'service_id',
        'tanggal_gate_in',
        'user_id',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'tanggal_gate_in' => 'datetime'
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

    public function service()
    {
        return $this->belongsTo(MasterService::class, 'service_id');
    }

    public function kontainers()
    {
        return $this->hasMany(Kontainer::class, 'gate_in_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
