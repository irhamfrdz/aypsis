<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPricelistKanisirBan extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor',
        'harga_1000_kawat',
        'harga_1000_benang',
        'harga_900_kawat',
        'status',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
