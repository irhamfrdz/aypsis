<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'beritas';

    protected $fillable = [
        'judul',
        'konten',
        'target_departemen',
        'tipe',
        'gambar',
        'is_active',
        'pinned',
        'kecepatan_teks',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'target_departemen' => 'array',
        'is_active' => 'boolean',
        'pinned' => 'boolean',
        'kecepatan_teks' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Helper cek apakah berita/pengumuman ditujukan ke semua departemen
     */
    public function getIsSemuaDepartemenAttribute(): bool
    {
        return empty($this->target_departemen) || ! is_array($this->target_departemen) || count($this->target_departemen) === 0;
    }

    /**
     * Helper label departemen sasaran
     */
    public function getTargetDepartemenLabelAttribute(): string
    {
        if ($this->is_semua_departemen) {
            return 'Semua Departemen';
        }

        return implode(', ', (array) $this->target_departemen);
    }

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
        if (! $this->gambar) {
            return null;
        }

        return asset($this->gambar);
    }
}
