<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_service_kendaraan_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan', 100)->nullable();
            $table->date('tanggal_service');
            $table->string('nomor_polisi', 30);
            $table->string('bengkel');
            $table->text('rincian_service');
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('status', 20)->default('pending');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('nomor_polisi', 'svc_batam_plate_idx');
            $table->index(['status', 'tanggal_service'], 'svc_batam_status_date_idx');
        });

        $permissions = [
            'tagihan-service-kendaraan-batam-view' => 'Melihat Tagihan Service Kendaraan Batam',
            'tagihan-service-kendaraan-batam-create' => 'Menambah Tagihan Service Kendaraan Batam',
            'tagihan-service-kendaraan-batam-update' => 'Mengubah Tagihan Service Kendaraan Batam',
            'tagihan-service-kendaraan-batam-delete' => 'Menghapus Tagihan Service Kendaraan Batam',
        ];

        foreach ($permissions as $name => $description) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $permissionId = DB::table('permissions')->where('name', $name)->value('id');
            if ($permissionId && DB::table('users')->where('id', 1)->exists()) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => 1, 'permission_id' => $permissionId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_service_kendaraan_batams');

        $names = [
            'tagihan-service-kendaraan-batam-view',
            'tagihan-service-kendaraan-batam-create',
            'tagihan-service-kendaraan-batam-update',
            'tagihan-service-kendaraan-batam-delete',
        ];
        $permissionIds = DB::table('permissions')->whereIn('name', $names)->pluck('id');
        DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
