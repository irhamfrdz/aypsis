<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaOb extends Model
{
    protected $fillable = [
        'nomor_pranota',
        'nama_kapal',
        'no_voyage',
        'tanggal_ob',
        'items',
        'status',
        'created_by',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function itemsPivot()
    {
        return $this->hasMany(PranotaObItem::class, 'pranota_ob_id');
    }

    /**
     * Enrich the items array for display.
     * Returns array of items with keys: nomor_kontainer, nama_barang, supir, size, biaya
     */
    public function getEnrichedItems(): array
    {
        $enrichedItems = [];
        // Prefer using pivot if exists
        try {
            $pivotRows = $this->itemsPivot()->get();
        } catch (\Throwable $e) {
            $pivotRows = null;
        }
        if ($pivotRows && $pivotRows->count() > 0) {
            foreach ($pivotRows as $p) {
                $enrichedItems[] = [
                    'nomor_kontainer' => $p->nomor_kontainer ?? '-',
                    'nama_barang' => $p->nama_barang ?? '-',
                    'supir' => $p->supir ?? '-',
                    'size' => $p->size ?? '-',
                    'biaya' => $p->biaya ?? null,
                ];
            }
            return $enrichedItems;
        }
        if (!is_array($this->items)) {
            return $enrichedItems;
        }

        foreach ($this->items as $item) {
            // if snapshot present
            if (!empty($item['nomor_kontainer']) || !empty($item['nama_barang'])) {
                $enrichedItems[] = [
                    'nomor_kontainer' => $item['nomor_kontainer'] ?? '-',
                    'nama_barang' => $item['nama_barang'] ?? ($item['jenis_barang'] ?? '-'),
                    'supir' => $item['supir'] ?? '-',
                    'size' => $item['size'] ?? ($item['size_kontainer'] ?? '-'),
                    'biaya' => $item['biaya'] ?? null,
                ];
                continue;
            }

            if (!isset($item['type']) || !isset($item['id'])) {
                $enrichedItems[] = [
                    'nomor_kontainer' => $item['nomor_kontainer'] ?? ('ID: ' . ($item['id'] ?? '?')),
                    'nama_barang' => $item['nama_barang'] ?? ('Type: ' . ($item['type'] ?? '?')),
                    'supir' => $item['supir'] ?? '-',
                    'size' => $item['size'] ?? '-',
                    'biaya' => $item['biaya'] ?? null,
                ];
                continue;
            }

            if ($item['type'] === 'bl') {
                try {
                    $bl = \DB::table('bls')->find($item['id']);
                    if ($bl) {
                        $supirName = '-';
                        if ($bl->supir_id) {
                            $supir = \DB::table('karyawans')->find($bl->supir_id);
                            $supirName = $supir ? ($supir->nama_lengkap ?? $supir->name ?? '-') : '-';
                        }
                        $enrichedItems[] = [
                            'nomor_kontainer' => $bl->nomor_kontainer ?? '-',
                            'nama_barang' => $bl->nama_barang ?? '-',
                            'supir' => $supirName,
                            'size' => $bl->size_kontainer ?? '-',
                            'biaya' => $bl->biaya ?? null,
                        ];
                    } else {
                        $enrichedItems[] = [
                            'nomor_kontainer' => 'ID: ' . $item['id'],
                            'nama_barang' => 'BL record not found',
                            'supir' => '-',
                            'size' => '-',
                            'biaya' => null,
                        ];
                    }
                } catch (\Throwable $e) {
                    $enrichedItems[] = [
                        'nomor_kontainer' => 'Error loading BL',
                        'nama_barang' => 'Exception: ' . $e->getMessage(),
                        'supir' => '-',
                        'size' => '-',
                        'biaya' => null,
                    ];
                }
            } elseif ($item['type'] === 'naik_kapal') {
                try {
                    $nk = \DB::table('naik_kapal')->find($item['id']);
                    if ($nk) {
                        $supirName = '-';
                        if (!empty($nk->supir_id)) {
                            $sup = \DB::table('karyawans')->find($nk->supir_id);
                            $supirName = $sup ? ($sup->nama_lengkap ?? $sup->name ?? '-') : '-';
                        }
                        $enrichedItems[] = [
                            'nomor_kontainer' => $nk->nomor_kontainer ?? '-',
                            'nama_barang' => $nk->jenis_barang ?? ($nk->nama_barang ?? '-'),
                            'supir' => $supirName,
                            'size' => $nk->size_kontainer ?? ($nk->ukuran_kontainer ?? '-'),
                            'biaya' => $nk->biaya ?? null,
                        ];
                    } else {
                        $enrichedItems[] = [
                            'nomor_kontainer' => 'ID: ' . $item['id'],
                            'nama_barang' => 'Naik Kapal record not found',
                            'supir' => '-',
                            'size' => '-',
                            'biaya' => null,
                        ];
                    }
                } catch (\Throwable $e) {
                    $enrichedItems[] = [
                        'nomor_kontainer' => 'Error loading Naik Kapal',
                        'nama_barang' => 'Exception: ' . $e->getMessage(),
                        'supir' => '-',
                        'size' => '-',
                        'biaya' => null,
                    ];
                }
            } else {
                // Unknown type
                $enrichedItems[] = [
                    'nomor_kontainer' => 'ID: ' . $item['id'] ?? '?',
                    'nama_barang' => 'Type: ' . ($item['type'] ?? '?'),
                    'supir' => '-',
                    'size' => '-',
                    'biaya' => null,
                ];
            }
        }

        return $enrichedItems;
    }

    /**
     * Calculate total amount from all items
     */
    public function calculateTotalAmount()
    {
        $total = 0;
        
        // Try pivot items first
        try {
            $pivotRows = $this->itemsPivot()->get();
            if ($pivotRows && $pivotRows->count() > 0) {
                foreach ($pivotRows as $item) {
                    $total += floatval($item->biaya ?? 0);
                }
                return $total;
            }
        } catch (\Throwable $e) {
            // Continue to items array
        }
        
        // Fallback to items array
        if (is_array($this->items)) {
            foreach ($this->items as $item) {
                $total += floatval($item['biaya'] ?? 0);
            }
        }
        
        return $total;
    }
}
