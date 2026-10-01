<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagihanLoloBatam extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $table = 'tagihan_lolo_batams';

    protected $fillable = [
        'nomor_tagihan',
        'tanggal_tagihan',
        'vendor',
        'tipe_operator',
        'operator',
        'operator_karyawan_id',
        'kapal',
        'voyage',
        'status_pembayaran',
        'tanggal_bayar',
        'total_tagihan',
        'keterangan',
        'status_approval',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'tanggal_bayar' => 'date',
        'total_tagihan' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TagihanLoloBatamItem::class, 'tagihan_lolo_batam_id');
    }

    public function operatorKaryawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'operator_karyawan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = '%'.str_replace(' ', '%', $term).'%';

        return $query->where(function ($q) use ($term) {
            $q->where('nomor_tagihan', 'like', $term)
                ->orWhere('vendor', 'like', $term)
                ->orWhere('kapal', 'like', $term)
                ->orWhere('voyage', 'like', $term)
                ->orWhere('keterangan', 'like', $term)
                ->orWhereHas('items', function ($sub) use ($term) {
                    $sub->where('nomor_kontainer', 'like', $term)
                        ->orWhere('nomor_surat_jalan', 'like', $term);
                });
        });
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'Lunas' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Belum Lunas' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getFormattedTotalTagihanAttribute(): string
    {
        return 'Rp '.number_format($this->total_tagihan, 0, ',', '.');
    }
}
