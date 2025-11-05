<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class Order extends Model
{
    use Auditable;
    protected $fillable = [
        'nomor_order',
        'tanggal_order',
        'tujuan_kirim',
        'no_tiket_do',
        'tujuan_ambil',
        'tujuan_ambil_id',
        'penerima',
        'alamat_penerima',
        'kontak_penerima',
        'size_kontainer',
        'unit_kontainer',
        'units',
        'sisa',
        'outstanding_status',
        'completion_percentage',
        'completed_at',
        'processing_history',
        'tipe_kontainer',
        'tanggal_pickup',
        'satuan',
        'exclude_ftz03',
        'include_ftz03',
        'exclude_sppb',
        'include_sppb',
        'exclude_buruh_bongkar',
        'include_buruh_bongkar',
        'term_id',
        'pengirim_id',
        'jenis_barang_id',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal_order' => 'date',
        'tanggal_pickup' => 'date',
        'completed_at' => 'datetime',
        'processing_history' => 'array',
        'exclude_ftz03' => 'boolean',
        'include_ftz03' => 'boolean',
        'exclude_sppb' => 'boolean',
        'include_sppb' => 'boolean',
        'exclude_buruh_bongkar' => 'boolean',
        'include_buruh_bongkar' => 'boolean',
        'units' => 'integer',
        'sisa' => 'integer',
        'completion_percentage' => 'decimal:2',
    ];

    // Accessor to ensure processing_history is always an array
    public function getProcessingHistoryAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    // Relationships
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(Pengirim::class);
    }

    public function jenisBarang(): BelongsTo
    {
        return $this->belongsTo(JenisBarang::class, 'jenis_barang_id');
    }

    public function tujuanAmbil(): BelongsTo
    {
        return $this->belongsTo(TujuanKegiatanUtama::class, 'tujuan_ambil_id');
    }

    public function suratJalans()
    {
        return $this->hasMany(SuratJalan::class);
    }

    // Outstanding Scopes
    public function scopeOutstanding($query)
    {
        return $query->where('sisa', '>', 0)->where('outstanding_status', '!=', 'completed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('outstanding_status', 'completed')->where('sisa', 0);
    }

    public function scopePartial($query)
    {
        return $query->where('outstanding_status', 'partial')->where('sisa', '>', 0);
    }

    public function scopePending($query)
    {
        return $query->where('outstanding_status', 'pending')->where('sisa', '=', function($query) {
            $query->selectRaw('units');
        });
    }



    // Outstanding Helper Methods
    public function isOutstanding()
    {
        return $this->sisa > 0 && $this->outstanding_status !== 'completed';
    }

    public function isCompleted()
    {
        return $this->sisa == 0 && $this->outstanding_status === 'completed';
    }

    public function getProcessedUnits()
    {
        return $this->units - $this->sisa;
    }

    public function updateOutstandingStatus()
    {
        if ($this->sisa <= 0) {
            $this->outstanding_status = 'completed';
            $this->completion_percentage = 100.00;
            $this->completed_at = now();
        } elseif ($this->sisa < $this->units) {
            $this->outstanding_status = 'partial';
            $this->completion_percentage = round((($this->units - $this->sisa) / $this->units) * 100, 2);
        } else {
            $this->outstanding_status = 'pending';
            $this->completion_percentage = 0.00;
        }

        return $this;
    }

    public function processUnits($processed_count, $note = null)
    {
        if ($processed_count > $this->sisa) {
            throw new \InvalidArgumentException('Processed units cannot exceed remaining units');
        }

        $this->sisa -= $processed_count;

        // Add to processing history
        // Ensure processing_history is always an array
        $history = $this->processing_history;
        if (!is_array($history)) {
            $history = [];
        }

        $history[] = [
            'processed_count' => $processed_count,
            'remaining' => $this->sisa,
            'note' => $note,
            'processed_at' => now()->toISOString(),
            'processed_by' => auth()->user()?->id ?? null
        ];
        $this->processing_history = $history;

        $this->updateOutstandingStatus();
        $this->save();

        return $this;
    }

    public function getOutstandingStatusBadgeAttribute()
    {
        $status = $this->outstanding_status ?? 'pending';
        
        // Map status to Indonesian based on context
        if ($status === 'pending') {
            // Check if order is confirmed and ready to process
            if ($this->status === 'confirmed' && $this->sisa == $this->units) {
                $text = 'Siap Dikerjakan';
                $badgeClass = 'badge-info';
            } else {
                $text = 'Menunggu';
                $badgeClass = 'badge-warning';
            }
        } else {
            $statusMap = [
                'partial' => 'Sedang Dikerjakan',
                'completed' => 'Selesai'
            ];
            $text = $statusMap[$status] ?? ucfirst($status);
            
            switch ($status) {
                case 'partial':
                    $badgeClass = 'badge-primary';
                    break;
                case 'completed':
                    $badgeClass = 'badge-success';
                    break;
                default:
                    $badgeClass = 'badge-secondary';
            }
        }

        return '<span class="' . $badgeClass . '">' . $text . '</span>';
    }


}

