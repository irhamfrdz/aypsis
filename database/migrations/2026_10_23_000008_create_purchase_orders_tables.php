<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_po', 60)->nullable()->unique();
            $table->date('tanggal_po');
            $table->date('tanggal_dibutuhkan')->nullable();
            $table->string('vendor', 255);
            $table->text('alamat_vendor')->nullable();
            $table->string('status', 20)->default('draft');
            $table->text('catatan')->nullable();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('diskon', 18, 2)->default(0);
            $table->decimal('pajak_persen', 5, 2)->default(0);
            $table->decimal('pajak', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['tanggal_po', 'id']);
            $table->index('status');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->unsignedInteger('urutan');
            $table->string('deskripsi', 500);
            $table->decimal('kuantitas', 14, 3);
            $table->string('satuan', 30)->nullable();
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('jumlah', 18, 2);
            $table->timestamps();
        });

        $permissions = [
            'purchase-order-view' => 'Melihat Purchase Order',
            'purchase-order-create' => 'Menambah Purchase Order',
            'purchase-order-update' => 'Mengubah Purchase Order',
            'purchase-order-delete' => 'Menghapus Purchase Order',
        ];
        foreach ($permissions as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                ['description' => $description, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        if (Schema::hasTable('roles') && Schema::hasTable('permission_role')) {
            $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
            if ($adminRoleId) {
                $permissionIds = DB::table('permissions')->whereIn('name', array_keys($permissions))->pluck('id');
                foreach ($permissionIds as $permissionId) {
                    DB::table('permission_role')->insertOrIgnore([
                        'role_id' => $adminRoleId,
                        'permission_id' => $permissionId,
                    ]);
                }
                if (Schema::hasTable('role_user') && Schema::hasTable('user_permissions')) {
                    $userIds = DB::table('role_user')->where('role_id', $adminRoleId)->pluck('user_id');
                    foreach ($userIds as $userId) {
                        foreach ($permissionIds as $permissionId) {
                            DB::table('user_permissions')->insertOrIgnore([
                                'user_id' => $userId,
                                'permission_id' => $permissionId,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down(): void
    {
        $names = ['purchase-order-view', 'purchase-order-create', 'purchase-order-update', 'purchase-order-delete'];
        $ids = DB::table('permissions')->whereIn('name', $names)->pluck('id');
        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('permission_id', $ids)->delete();
        }
        if (Schema::hasTable('permission_role')) {
            DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        }
        DB::table('permissions')->whereIn('name', $names)->delete();
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
