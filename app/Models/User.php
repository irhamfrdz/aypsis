<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'karyawan_id',
        'status',
        'registration_reason',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * The relationship with the Karyawan model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * The relationship with the admin who approved this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if user is approved
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if user is pending approval
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is rejected
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * The relationship with the Permission model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    /**
     * Check if the user has a specific permission.
     *
     * @param  string  $permissionName
     * @return bool
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        // Check if the user's permissions collection contains the permission with the given name.
        return $this->permissions->contains('name', $permissionName);
    }

    /**
     * Check if the user has any permission whose name starts with the given prefix.
     * Useful when permissions are scoped by resource (e.g. "tagihan-kontainer-sewa.*").
     *
     * @param string $prefix
     * @return bool
     */
    public function hasPermissionLike(string $prefix): bool
    {
        return $this->permissions->contains(function ($perm) use ($prefix) {
            return strpos($perm->name ?? '', $prefix) === 0;
        });
    }

    /**
     * Heuristic permission match: compare tokens of the requested ability and each permission
     * and return true when there's a sufficient overlap. This helps when ability names and
     * permission names use different prefixes but share core tokens (e.g. "master-pranota-tagihan-kontainer" vs "tagihan-kontainer-sewa.index").
     *
     * @param string $ability
     * @return bool
     */
    public function hasPermissionMatch(string $ability): bool
    {
        $abilityTokens = array_filter(array_map('strtolower', preg_split('/[^a-z0-9]+/i', $ability)));
        if (empty($abilityTokens)) {
            return false;
        }

        foreach ($this->permissions as $perm) {
            $pname = strtolower($perm->name ?? '');
            if ($pname === '') {
                continue;
            }

            // exact or substring matches already covered elsewhere, but keep quick checks
            if ($pname === strtolower($ability) || strpos($pname, strtolower($ability)) !== false || strpos(strtolower($ability), $pname) !== false) {
                return true;
            }

            $permTokens = array_filter(array_map('strtolower', preg_split('/[^a-z0-9]+/i', $pname)));
            if (empty($permTokens)) {
                continue;
            }

            $intersect = array_intersect($abilityTokens, $permTokens);
            // require at least two token overlaps to reduce false positives
            if (count($intersect) >= 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user is a driver (supir) based on karyawan divisi
     *
     * @return bool
     */
    public function isSupir(): bool
    {
        // Check if user has karyawan relationship
        if (!$this->karyawan) {
            return false;
        }

        // Check if karyawan's divisi is 'supir' (case insensitive)
        $divisi = strtolower($this->karyawan->divisi ?? '');
        return $divisi === 'supir' || $divisi === 'driver';
    }

    /**
     * The relationship with the Role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    /**
     * Check if the user can perform a specific action (alias for hasPermissionTo).
     * This method is used by the sidebar layout and other parts of the application.
     *
     * @param string|array $abilities
     * @param array $arguments
     * @return bool
     */
    public function can($abilities, $arguments = []): bool
    {
        // Handle string ability (most common case in this app)
        if (is_string($abilities)) {
            return $this->hasPermissionTo($abilities);
        }

        // Handle array of abilities
        if (is_array($abilities)) {
            foreach ($abilities as $ability) {
                if (is_string($ability) && $this->hasPermissionTo($ability)) {
                    return true;
                }
            }
            return false;
        }

        // For other cases, fall back to parent implementation
        return parent::can($abilities, $arguments);
    }
}
