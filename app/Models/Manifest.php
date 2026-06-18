<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    protected $fillable = [
        'nomor_bl',
        'nomor_urut',
        'nomor_manifest',
        'nomor_tanda_terima',
        'prospek_id',
        'nomor_kontainer',
        'no_seal',
        'tipe_kontainer',
        'size_kontainer',
        'no_voyage',
        'pelabuhan_asal',
        'pelabuhan_tujuan',
        'pelabuhan_muat',
        'pelabuhan_bongkar',
        'nama_kapal',
        'tanggal_berangkat',
        'nama_barang',
        'asal_kontainer',
        'ke',
        'pengirim',
        'alamat_pengirim',
        'penerima',
        'alamat_penerima',
        'alamat_pengiriman',
        'contact_person',
        'tonnage',
        'tonnage_perincian',
        'volume',
        'volume_perincian',
        'satuan',
        'term',
        'kuantitas',
        'penerimaan',
        'notify_party',
        'alamat_notify_party',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'penerimaan' => 'date',
        'tonnage' => 'decimal:3',
        'tonnage_perincian' => 'decimal:3',
        'volume' => 'decimal:3',
        'volume_perincian' => 'decimal:3',
        'kuantitas' => 'integer',
    ];

    // Relationships
    public function prospek()
    {
        return $this->belongsTo(Prospek::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function suratJalanBongkaran()
    {
        return $this->hasOne(SuratJalanBongkaran::class, 'manifest_id');
    }

    public function suratJalanBongkaranBatam()
    {
        return $this->hasOne(SuratJalanBongkaranBatam::class, 'manifest_id');
    }

    /**
     * Get the Tanda Terima number for display, with fallbacks.
     */
    public function getNomorTandaTerimaDisplayAttribute()
    {
        if (! empty($this->nomor_tanda_terima)) {
            return $this->nomor_tanda_terima;
        }

        if ($this->prospek) {
            if ($this->prospek->tandaTerima) {
                return $this->prospek->tandaTerima->no_tanda_terima ?: $this->prospek->tandaTerima->no_surat_jalan;
            }

            // Fallback: extract from keterangan if it's a TTTSJ
            if (preg_match('/Tanda Terima Tanpa Surat Jalan:\s*([^|]+)/', $this->prospek->keterangan, $matches)) {
                return trim($matches[1]);
            }
        }

        return '-';
    }

    /**
     * Get the Alamat Pengiriman, fallback to Alamat Penerima if empty.
     */
    public function getAlamatPengirimanAttribute($value)
    {
        return $value ?: $this->alamat_penerima;
    }

    /**
     * Resolves notify party from related tables.
     */
    public function getRelatedNotifyParty()
    {
        $ttNo = $this->nomor_tanda_terima;

        // 1. Check FCL Tanda Terima via prospek
        if ($this->prospek && $this->prospek->tandaTerima) {
            $tandaTerima = $this->prospek->tandaTerima;
            if (! empty($tandaTerima->notify_party)) {
                return [
                    'notify_party' => $tandaTerima->notify_party,
                    'alamat_notify_party' => $tandaTerima->alamat_notify_party,
                ];
            }
        }

        // 2. Check FCL Tanda Terima by nomor_tanda_terima
        if ($ttNo) {
            $fcl = \App\Models\TandaTerima::where('no_surat_jalan', $ttNo)->first();
            if ($fcl && ! empty($fcl->notify_party)) {
                return [
                    'notify_party' => $fcl->notify_party,
                    'alamat_notify_party' => $fcl->alamat_notify_party,
                ];
            }
        }

        // 3. Check Tanda Terima Tanpa Surat Jalan
        if ($ttNo) {
            $ttsj = \App\Models\TandaTerimaTanpaSuratJalan::where('no_tanda_terima', $ttNo)
                ->orWhere('nomor_tanda_terima', $ttNo)
                ->first();
            if ($ttsj && ! empty($ttsj->notify_party)) {
                return [
                    'notify_party' => $ttsj->notify_party,
                    'alamat_notify_party' => $ttsj->alamat_notify_party,
                ];
            }
        }

        // 4. Check LCL Tanda Terima
        if ($ttNo) {
            $lcl = \App\Models\TandaTerimaLcl::where('nomor_tanda_terima', $ttNo)->first();
            if ($lcl && ! empty($lcl->notify_party)) {
                return [
                    'notify_party' => $lcl->notify_party,
                    'alamat_notify_party' => $lcl->alamat_notify_party,
                ];
            }
        }

        // 5. Check Batam equivalents
        if ($ttNo && class_exists(\App\Models\TandaTerimaBatam::class)) {
            $fclBatam = \App\Models\TandaTerimaBatam::where('no_surat_jalan', $ttNo)->first();
            if ($fclBatam && ! empty($fclBatam->notify_party)) {
                return [
                    'notify_party' => $fclBatam->notify_party,
                    'alamat_notify_party' => $fclBatam->alamat_notify_party,
                ];
            }
        }

        if ($ttNo && class_exists(\App\Models\TandaTerimaTanpaSuratJalanBatam::class)) {
            $ttsjBatam = \App\Models\TandaTerimaTanpaSuratJalanBatam::where('no_tanda_terima', $ttNo)
                ->orWhere('nomor_tanda_terima', $ttNo)
                ->first();
            if ($ttsjBatam && ! empty($ttsjBatam->notify_party)) {
                return [
                    'notify_party' => $ttsjBatam->notify_party,
                    'alamat_notify_party' => $ttsjBatam->alamat_notify_party,
                ];
            }
        }

        if ($ttNo && class_exists(\App\Models\TandaTerimaLclBatam::class)) {
            $lclBatam = \App\Models\TandaTerimaLclBatam::where('nomor_tanda_terima', $ttNo)->first();
            if ($lclBatam && ! empty($lclBatam->notify_party)) {
                return [
                    'notify_party' => $lclBatam->notify_party,
                    'alamat_notify_party' => $lclBatam->alamat_notify_party,
                ];
            }
        }

        return null;
    }

    /**
     * Boot logic for automatically filling values on save.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($manifest) {
            if (empty($manifest->notify_party)) {
                $related = $manifest->getRelatedNotifyParty();
                if ($related) {
                    $manifest->notify_party = $related['notify_party'];
                    $manifest->alamat_notify_party = $related['alamat_notify_party'];
                }
            }
        });
    }
}
