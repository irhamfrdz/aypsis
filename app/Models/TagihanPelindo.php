<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanPelindo extends Model
{
    use Auditable, HasFactory;

    protected $table = 'tagihan_pelindos';

    protected $fillable = [
        'nomor_tagihan',
        'tanggal_tagihan',
        'kapal',
        'voyage',
        'status_pembayaran',
        'tanggal_bayar',
        'total_tagihan',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'tanggal_bayar' => 'date',
        'total_tagihan' => 'decimal:2',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(TagihanPelindoItem::class, 'tagihan_pelindo_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }
        $t = '%'.str_replace(' ', '%', $term).'%';

        return $query->where(function ($q) use ($t) {
            $q->where('nomor_tagihan', 'like', $t)
                ->orWhere('keterangan', 'like', $t)
                ->orWhereHas('items', function ($sub) use ($t) {
                    $sub->where('nomor_kontainer', 'like', $t)
                        ->orWhere('kegiatan', 'like', $t);
                });
        });
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status_pembayaran) {
            'Lunas' => 'bg-green-100 text-green-800 border-green-200',
            'Belum Lunas' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200'
        };
    }
}
