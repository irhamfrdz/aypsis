<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBanDalamUsage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saved(function ($usage) {
            if (\Illuminate\Support\Facades\Schema::hasTable('pemakaian_ban')) {
                app(\App\Services\PemakaianBanSyncService::class)->run(false, 'stock_ban_dalam_usages', $usage->id);
            }
        });
        static::deleted(function ($usage) {
            if (\Illuminate\Support\Facades\Schema::hasTable('pemakaian_ban')) {
                \Illuminate\Support\Facades\DB::table('pemakaian_ban')
                    ->where('source_table', 'stock_ban_dalam_usages')->where('source_id', $usage->id)
                    ->update(['deleted_at' => now(), 'updated_at' => now()]);
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockBanDalam()
    {
        return $this->belongsTo(StockBanDalam::class);
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'penerima_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function gudang()
    {
        return $this->belongsTo(MasterGudangBan::class, 'gudang_id');
    }
}
