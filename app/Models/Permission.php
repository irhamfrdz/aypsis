<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        // Asumsi nama tabel pivot adalah 'user_permissions'
        return $this->belongsToMany(User::class, 'user_permissions');
    }
}
