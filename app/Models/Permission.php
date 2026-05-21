<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use Auditable;
    use Auditable, HasFactory;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        // Asumsi nama tabel pivot adalah 'user_permissions'
        return $this->belongsToMany(User::class, 'user_permissions');
    }
}
