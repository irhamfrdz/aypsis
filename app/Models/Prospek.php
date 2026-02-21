<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Models\User;

class Prospek extends Model
{
    use HasFactory, Auditable;

    protected $table = 'prospek';

    protected $fillable = [
        'tanggal',
        'nama_supir',
        'supir_ob',
        'barang',
        'pt_pengirim',
        'ukuran',
        'tipe',
        'no_surat_jalan',
        'surat_jalan_id',
        'tanda_terima_id',
        'nomor_kontainer',
        'no_seal',
        'tujuan_pengiriman',
        'total_ton',
        'kuantitas',
        'total_volume',
        'nama_kapal',
        'kapal_id',
        'no_voyage',
        'pelabuhan_asal',
        'tanggal_muat',
        'keterangan',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'tanggal',
        'tanggal_muat',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_ton' => 'decimal:3',
        'total_volume' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_AKTIF = 'aktif';
    const STATUS_MENUNGGU_KAPAL = 'menunggu_kapal';
    const STATUS_SUDAH_MUAT = 'sudah_muat';
    const STATUS_BATAL = 'batal';

    // Ukuran constants
    const UKURAN_20 = '20';
    const UKURAN_40 = '40';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_MENUNGGU_KAPAL => 'Menunggu Kapal',
            self::STATUS_SUDAH_MUAT => 'Sudah Muat',
            self::STATUS_BATAL => 'Batal'
        ];
    }

    public static function getUkuranOptions()
    {
        return [
            self::UKURAN_20 => '20 Feet',
            self::UKURAN_40 => '40 Feet'
        ];
    }

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tandaTerima()
    {
        return $this->belongsTo(\App\Models\TandaTerima::class, 'tanda_terima_id');
    }

    public function suratJalan()
    {
        return $this->belongsTo(\App\Models\SuratJalan::class, 'surat_jalan_id');
    }

    public function kapal()
    {
        return $this->belongsTo(\App\Models\MasterKapal::class, 'kapal_id');
    }

    /**
     * Boot method untuk auto-linking
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-link dengan TandaTerima ketika Prospek dibuat atau diupdate
        static::created(function (self $prospek) {
            $prospek->autoLinkTandaTerima();
        });

        static::updated(function (self $prospek) {
            if (($prospek->isDirty('surat_jalan_id') || $prospek->isDirty('no_surat_jalan')) && !$prospek->tanda_terima_id) {
                $prospek->autoLinkTandaTerima();
            }
        });
    }

    /**
     * Auto-link dengan TandaTerima berdasarkan surat_jalan_id dan no_surat_jalan
     */
    public function autoLinkTandaTerima()
    {
        if ($this->tanda_terima_id) {
            return; // Sudah ter-link
        }

        $tandaTerima = null;

        // Cari berdasarkan surat_jalan_id terlebih dahulu
        if ($this->surat_jalan_id) {
            $tandaTerima = \App\Models\TandaTerima::where('surat_jalan_id', $this->surat_jalan_id)->first();
        }

        // Jika tidak ditemukan, cari berdasarkan no_surat_jalan
        if (!$tandaTerima && $this->no_surat_jalan) {
            $tandaTerima = \App\Models\TandaTerima::where('no_surat_jalan', $this->no_surat_jalan)->first();
        }

        // Update jika ditemukan
        if ($tandaTerima) {
            $this->update(['tanda_terima_id' => $tandaTerima->id]);
        }
    }

    public function bls()
    {
        return $this->hasMany(Bl::class);
    }

    public function naikKapal()
    {
        return $this->hasMany(NaikKapal::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function scopeByTujuan($query, $tujuan)
    {
        return $query->where('tujuan_pengiriman', $tujuan);
    }

    public function scopeByUkuran($query, $ukuran)
    {
        return $query->where('ukuran', $ukuran);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? $this->status;
    }

    public function getUkuranLabelAttribute()
    {
        $ukurans = self::getUkuranOptions();
        return $ukurans[$this->ukuran] ?? $this->ukuran;
    }

    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal ? $this->tanggal->format('d/m/Y') : '';
    }

    public function getPenerimaAttribute()
    {
        // 1. Tanda Terima
        if ($this->tandaTerima) {
            return $this->tandaTerima->penerima;
        }

        // 2. Tanda Terima Tanpa Surat Jalan (via keterangan)
        if ($this->keterangan && preg_match('/Tanda Terima Tanpa Surat Jalan:\s*([^|]+)/', $this->keterangan, $matches)) {
            $noTttsj = trim($matches[1]);
            $tttsj = \App\Models\TandaTerimaTanpaSuratJalan::where('no_tanda_terima', $noTttsj)->first();
            if ($tttsj && $tttsj->penerima) {
                return $tttsj->penerima;
            }
        }

        // 3. CARGO fallback ke TTTSJ
        if (strtoupper($this->tipe) === 'CARGO' && !$this->tanda_terima_id) {
             $tttsj = \Illuminate\Support\Facades\DB::table('tanda_terima_tanpa_surat_jalan')
                ->where('pengirim', $this->pt_pengirim)
                ->where('supir', $this->nama_supir)
                ->where('tujuan_pengiriman', $this->tujuan_pengiriman)
                ->orderBy('created_at', 'desc')
                ->first();
             if ($tttsj && $tttsj->penerima) {
                 return $tttsj->penerima;
             }
        }

        // 4. LCL
        if (strtoupper($this->tipe) === 'LCL' && $this->nomor_kontainer) {
            $ttLcls = \App\Models\TandaTerimaLcl::whereHas('kontainerPivot', function($q) {
                $q->where('nomor_kontainer', $this->nomor_kontainer);
                if ($this->no_seal) {
                    $q->where('nomor_seal', $this->no_seal);
                }
            })->get();

            // Jika filter no_seal kosong, ambil tanpa filter no_seal kalau tidak nemu
            if ($ttLcls->isEmpty() && $this->no_seal) {
                $ttLcls = \App\Models\TandaTerimaLcl::whereHas('kontainerPivot', function($q) {
                    $q->where('nomor_kontainer', $this->nomor_kontainer);
                })->get();
            }

            if ($ttLcls->isNotEmpty()) {
                $penerimas = collect();
                foreach ($ttLcls as $tt) {
                    if ($tt->nama_penerima) {
                        $penerimas->push($tt->nama_penerima);
                    }
                }
                
                if ($penerimas->isNotEmpty()) {
                    return $penerimas->unique()->implode(', ');
                }
            }
        }

        return null; // fallback will be handled by blade
    }
}
