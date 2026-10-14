<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSIONS = [
        'belanja-amprahan-view',
        'belanja-amprahan-create',
        'belanja-amprahan-update',
        'belanja-amprahan-delete',
    ];

    public function up(): void
    {
        Schema::dropIfExists('belanja_amprahans');

        $permissionIds = DB::table('permissions')->whereIn('name', self::PERMISSIONS)->pluck('id');
        DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }

    public function down(): void
    {
        Schema::create('belanja_amprahans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('supplier')->nullable();
            $table->string('nama_barang')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('penerima_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->timestamps();
        });

        foreach (self::PERMISSIONS as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
