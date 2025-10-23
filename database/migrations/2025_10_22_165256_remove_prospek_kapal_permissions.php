<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah table permissions ada
        if (Schema::hasTable('permissions')) {
            // Hapus permissions yang terkait prospek kapal
            DB::table('permissions')->where('name', 'like', '%prospek-kapal%')->delete();
        }

        // Cek dan hapus dari role_has_permissions jika table ada
        if (Schema::hasTable('role_has_permissions')) {
            DB::table('role_has_permissions')->whereIn('permission_id', function($query) {
                $query->select('id')->from('permissions')->where('name', 'like', '%prospek-kapal%');
            })->delete();
        }

        // Cek dan hapus dari model_has_permissions jika table ada
        if (Schema::hasTable('model_has_permissions')) {
            DB::table('model_has_permissions')->whereIn('permission_id', function($query) {
                $query->select('id')->from('permissions')->where('name', 'like', '%prospek-kapal%');
            })->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu implement rollback karena permissions sudah tidak digunakan
    }
};
