<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create tagihan_pelindos
        Schema::create('tagihan_pelindos', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan')->unique();
            $table->date('tanggal_tagihan');
            $table->enum('status_pembayaran', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index('nomor_tagihan');
            $table->index('tanggal_tagihan');
            $table->index('status_pembayaran');
        });

        // 2. Create tagihan_pelindo_items
        Schema::create('tagihan_pelindo_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tagihan_pelindo_id');
            $table->string('nomor_kontainer')->nullable();
            $table->unsignedBigInteger('pricelist_pelindo_id')->nullable();
            $table->string('kegiatan');
            $table->string('ukuran')->nullable();
            $table->decimal('tarif', 15, 2);
            $table->integer('jumlah')->default(1);
            $table->decimal('total', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('tagihan_pelindo_id')->references('id')->on('tagihan_pelindos')->onDelete('cascade');
            $table->foreign('pricelist_pelindo_id')->references('id')->on('pricelist_pelindos')->onDelete('set null');
        });

        // 3. Add permissions
        $permissions = [
            [
                'name' => 'tagihan-pelindo-view',
                'description' => 'Melihat data Tagihan Pelindo',
            ],
            [
                'name' => 'tagihan-pelindo-create',
                'description' => 'Menambah data Tagihan Pelindo',
            ],
            [
                'name' => 'tagihan-pelindo-edit',
                'description' => 'Mengubah data Tagihan Pelindo',
            ],
            [
                'name' => 'tagihan-pelindo-delete',
                'description' => 'Menghapus data Tagihan Pelindo',
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Give permission to admin (user_id = 1)
            $permissionId = DB::table('permissions')->where('name', $permission['name'])->value('id');
            if ($permissionId) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => 1, 'permission_id' => $permissionId],
                    []
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop tables
        Schema::dropIfExists('tagihan_pelindo_items');
        Schema::dropIfExists('tagihan_pelindos');

        // 2. Remove permissions
        $permissionNames = [
            'tagihan-pelindo-view',
            'tagihan-pelindo-create',
            'tagihan-pelindo-edit',
            'tagihan-pelindo-delete',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('user_permissions')
                ->whereIn('permission_id', $permissionIds)
                ->delete();

            DB::table('permissions')
                ->whereIn('id', $permissionIds)
                ->delete();
        }
    }
};
