<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Cek apakah user bisa akses menu tertentu
     */
    public static function canAccessMenu(string $menuName): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin bisa akses semua menu
        try {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
        } catch (\Exception $e) {
            // Jika method hasRole tidak tersedia, lanjutkan
        }

        $permissions = config('permissions.menu_permissions.' . $menuName, []);

        // Jika tidak ada permission yang dibutuhkan, berarti semua user bisa akses
        if (empty($permissions)) {
            return true;
        }

        // Cek apakah user punya salah satu permission yang dibutuhkan
        foreach ($permissions as $permission) {
            try {
                if ($user->can($permission)) {
                    return true;
                }
                if (method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike($permission)) {
                    return true;
                }
            } catch (\Exception $e) {
                // Jika method tidak tersedia, lanjutkan ke permission berikutnya
                continue;
            }
        }

        return false;
    }

    /**
     * Cek apakah user bisa akses sub-menu master data
     */
    public static function canAccessMasterMenu(string $subModule): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin bisa akses semua
        try {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
        } catch (\Exception $e) {
            // Method tidak tersedia
        }

        // Cek permission master-data atau sub-module specific
        try {
            if ($user->can('master-data')) {
                return true;
            }
            if ($user->can($subModule)) {
                return true;
            }
            if (method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike('master-')) {
                return true;
            }
        } catch (\Exception $e) {
            // Method tidak tersedia
        }

        return false;
    }

    /**
     * Cek apakah user bisa akses modul tertentu
     */
    public static function canAccessModule(string $moduleName): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin bisa akses semua
        try {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
        } catch (\Exception $e) {
            // Method tidak tersedia
        }

        // Cek permission module
        try {
            if ($user->can($moduleName)) {
                return true;
            }
            if (method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike($moduleName)) {
                return true;
            }
        } catch (\Exception $e) {
            // Method tidak tersedia
        }

        return false;
    }

    /**
     * Get semua permissions yang dibutuhkan untuk menu
     */
    public static function getMenuPermissions(): array
    {
        return [
            // Master Data permissions
            'master-karyawan',
            'master-user',
            'master-kontainer',
            'master-pricelist-sewa-kontainer',
            'master-tujuan',
            'master-kegiatan',
            'master-permission',
            'master-mobil',

            // Module permissions
            'master-data',
            'user-approval',
            'tagihan-kontainer',
            'permohonan',
            'pranota-supir',
            'pembayaran-pranota-supir',
        ];
    }

    /**
     * Get permission description
     */
    public static function getPermissionDescription(string $permission): string
    {
        $descriptions = [
            'master-karyawan' => 'Akses manajemen karyawan',
            'master-user' => 'Akses manajemen user',
            'master-kontainer' => 'Akses manajemen kontainer',
            'master-pricelist-sewa-kontainer' => 'Akses pricelist sewa kontainer',
            'master-tujuan' => 'Akses manajemen tujuan',
            'master-kegiatan' => 'Akses manajemen kegiatan',
            'master-permission' => 'Akses manajemen permission',
            'master-mobil' => 'Akses manajemen mobil',
            'master-data' => 'Akses semua menu master data',
            'user-approval' => 'Persetujuan user baru',
            'tagihan-kontainer' => 'Akses menu tagihan kontainer sewa',
            'permohonan' => 'Akses menu permohonan memo',
            'pranota-supir' => 'Akses menu pranota supir',
            'pembayaran-pranota-supir' => 'Akses menu pembayaran pranota supir',
        ];

        return $descriptions[$permission] ?? 'Permission ' . $permission;
    }
}
