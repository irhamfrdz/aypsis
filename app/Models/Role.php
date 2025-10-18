<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class Role extends Model
{
    use HasFactory;

    use Auditable;
    protected $fillable = ['name', 'description'];

    public function users()
    {
        // Nama tabel pivot adalah 'role_user'
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function permissions()
    {
        // Nama tabel pivot adalah 'permission_role'
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}
