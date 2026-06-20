<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalStuffing extends Model
{
    protected $table = 'biaya_kapal_stuffing';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'tanda_terima_ids',
        'subtotal',
        'pph',
        'total_biaya',
    ];

    protected $casts = [
        'tanda_terima_ids' => 'array',
    ];

    /**
     * Relationship to BiayaKapal
     */
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    /**
     * Accessor to get Tanda Terima models
     */
    public function getTandaTerimasAttribute()
    {
        return $this->getTandaTerimas();
    }

    /**
     * Helper to get Tanda Terima models
     */
    public function getTandaTerimas()
    {
        if (empty($this->tanda_terima_ids)) {
            return collect();
        }

        $results = collect();
        $grouped = [];

        foreach ($this->tanda_terima_ids as $item) {
            if (is_array($item)) {
                $id = $item['id'] ?? null;
                $type = $item['type'] ?? 'tanda_terima';
            } else {
                $id = $item;
                $type = 'tanda_terima';
            }

            if ($id) {
                $grouped[$type][] = $id;
            }
        }

        foreach ($grouped as $type => $ids) {
            if ($type === 'tanda_terima_lcl') {
                $records = TandaTerimaLcl::whereIn('id', $ids)->get()->map(function ($tt) {
                    // Normalize attributes so that generic views can render them transparently
                    $tt->no_surat_jalan = $tt->nomor_tanda_terima;
                    $tt->pengirim = $tt->nama_pengirim;
                    $tt->penerima = $tt->nama_penerima;
                    return $tt;
                });
                $results = $results->merge($records);
            } elseif ($type === 'tanda_terima_tanpa_surat_jalan') {
                $records = TandaTerimaTanpaSuratJalan::whereIn('id', $ids)->get()->map(function ($tt) {
                    // Normalize attributes
                    $tt->no_surat_jalan = $tt->no_tanda_terima ?: $tt->nomor_tanda_terima;
                    return $tt;
                });
                $results = $results->merge($records);
            } else {
                $records = TandaTerima::whereIn('id', $ids)->get();
                $results = $results->merge($records);
            }
        }

        return $results;
    }
}
