<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanKontainerSewa extends Model
{
    use HasFactory;

    protected $table = 'tagihan_kontainer_sewa';

    protected $fillable = [
        'vendor',
        'nomor_kontainer',
        'group',
        'tanggal_harga_awal',
        'tanggal_harga_akhir',
        'periode',
        'masa',
        'dpp',
        'dpp_nilai_lain',
        'ppn',
        'pph',
        'grand_total',
    ];

    protected $casts = [
        'tanggal_harga_awal' => 'date',
        'tanggal_harga_akhir' => 'date',
        'masa' => 'decimal:2',
        'dpp' => 'decimal:2',
        'dpp_nilai_lain' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    // Return array of nomor_kontainer parsed from the `nomor_kontainer` CSV field.
    public function getNomorKontainerListAttribute()
    {
        $raw = $this->attributes['nomor_kontainer'] ?? '';
        if (trim($raw) === '') return [];
        $parts = array_map('trim', explode(',', $raw));
        return array_values(array_filter($parts, function($v){ return $v !== ''; }));
    }

    public function scopeSearch($query, $term)
    {
        if (empty($term)) return $query;
        $t = '%' . str_replace(' ', '%', $term) . '%';
        return $query->where(function ($q) use ($t) {
            $q->where('vendor', 'like', $t)
              ->orWhere('group', 'like', $t)
              ->orWhere('nomor_kontainer', 'like', $t);
        });
    }
}
