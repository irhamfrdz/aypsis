<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'beritas';

    protected $fillable = [
        'judul',
        'konten',
        'tipe',
        'gambar',
        'is_active',
        'pinned',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'pinned'       => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Relasi ke User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope hanya berita aktif dan sudah dipublish
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', Carbon::now());
                     });
    }

    /**
     * URL gambar
     */
    public function getGambarUrlAttribute(): ?string
    {
        if (!$this->gambar) return null;
        return asset($this->gambar);
    }
}
